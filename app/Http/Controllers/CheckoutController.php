<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\VendorOrder;
use App\Models\ProductInventoryLog;
use App\Models\Payment;
use App\Services\BankTransferService;
use App\Services\OrderService;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    protected function getCart()
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        return Cart::getOrCreateForUser($userId, $sessionId);
    }

    /**
     * Show checkout page
     */
    public function index()
    {
        $cart = $this->getCart();

        // Validate cart is not empty
        if ($cart->getTotalItems() == 0) {
            return redirect()->route('cart.index')
                           ->with('error', 'Your cart is empty.');
        }

        // Get cart items grouped by vendor
        $itemsByVendor = $cart->getItemsByVendor();
        $subtotal = $cart->getSubtotal();
        $totalItems = $cart->getTotalItems();

        // Get customer's saved addresses
        $addresses = CustomerAddress::where('customer_id', auth()->id())
                                   ->orderBy('is_default', 'desc')
                                   ->orderBy('created_at', 'desc')
                                   ->get();

        // Get default address
        $defaultAddress = $addresses->firstWhere('is_default', true);

        // Calculate commission per vendor (if needed)
        $vendorCommissions = [];
        foreach ($itemsByVendor as $vendorId => $items) {
            $vendor = $items->first()->product->vendor;
            $vendorSubtotal = $items->sum(fn($item) => $item->price * $item->quantity);

            // Get category commission rates
            $commissionAmount = 0;
            foreach ($items as $item) {
                $category = $item->product->category;
                $commissionRate = $category->commission_rate ?? 0;
                $itemTotal = $item->price * $item->quantity;
                $commissionAmount += ($itemTotal * $commissionRate / 100);
            }

            $vendorCommissions[$vendorId] = [
                'subtotal' => $vendorSubtotal,
                'commission' => $commissionAmount,
                'vendor_receives' => $vendorSubtotal - $commissionAmount,
            ];
        }

        return view('checkout.index', compact(
            'cart',
            'itemsByVendor',
            'subtotal',
            'totalItems',
            'addresses',
            'defaultAddress',
            'vendorCommissions'
        ));
    }

    /**
     * Store a new address
     */
    public function storeAddress(Request $request)
    {
        $request->validate([
            'label' => 'nullable|string|max:255',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state_province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'delivery_instructions' => 'nullable|string|max:500',
            'is_default' => 'boolean',
        ]);

        $address = CustomerAddress::create([
            'customer_id' => auth()->id(),
            'label' => $request->label,
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city' => $request->city,
            'state_province' => $request->state_province,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'delivery_instructions' => $request->delivery_instructions,
            'is_default' => $request->is_default ?? false,
        ]);

        if ($request->is_default) {
            $address->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully!',
            'address' => $address,
        ]);
    }

    /**
     * Set an address as default
     */
    public function setDefaultAddress(CustomerAddress $address)
    {
        // Verify address belongs to current user
        if ($address->customer_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $address->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Default address updated!',
        ]);
    }

    /**
     * Delete an address
     */
    public function deleteAddress(CustomerAddress $address)
    {
        // Verify address belongs to current user
        if ($address->customer_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully!',
        ]);
    }

    /**
     * Process checkout and create order
     */
    public function process(Request $request)
    {
        $rules = [
            'address_id' => 'required|exists:customer_addresses,id',
            'payment_method' => 'required|in:card,bank_transfer,cod',
        ];
        $messages = [];

        if ($request->payment_method === 'bank_transfer') {
            $rules += [
                'bank_payer_name' => 'required|string|max:255',
                'bank_payment_app' => 'required|string|max:255',
                'bank_payment_source' => 'required|string|max:255',
                'bank_customer_account_number' => 'required|string|max:100',
                'payment_slip' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ];
            $messages = [
                'bank_payer_name.required' => 'Please enter the payer name.',
                'bank_payment_app.required' => 'Please enter the payment app or platform.',
                'bank_payment_source.required' => 'Please enter the payment source.',
                'bank_customer_account_number.required' => 'Please enter the customer account number.',
                'payment_slip.required' => 'Please upload the bank payment slip or receipt.',
            ];
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $cart = $this->getCart();

        // Verify address belongs to user
        $address = CustomerAddress::where('id', $request->address_id)
                                  ->where('customer_id', auth()->id())
                                  ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid address selected.',
            ], 400);
        }

        $paymentMethod = $request->payment_method;
        $paymentStatus = 'pending';
        $stripeClientSecret = null;
        $stripeIsFake = false;

        $paymentData = [
            'bank_payer_name' => $request->bank_payer_name ?? null,
            'bank_payment_app' => $request->bank_payment_app ?? null,
            'bank_payment_source' => $request->bank_payment_source ?? null,
            'bank_customer_account_number' => $request->bank_customer_account_number ?? null,
        ];

        try {
            // Use OrderService to create order with full splitting logic
            $orderService = app(OrderService::class);

            // For card payments, create order first then handle Stripe
            if ($paymentMethod === 'card') {
                $order = $orderService->createOrderFromCart($cart, $address, 'card');

                // Now handle Stripe payment intent
                try {
                    $stripePayment = app(StripePaymentService::class)->createPaymentIntent($order);
                    $stripeIsFake = $stripePayment['is_fake'] ?? false;
                    $stripeClientSecret = $stripePayment['client_secret'];
                    $stripePaymentId = $stripePayment['payment']->id;

                    if ($stripeIsFake) {
                        app(StripePaymentService::class)->markCompleted(
                            $stripePayment['payment'],
                            (object) ['id' => $stripePayment['payment']->transaction_id, 'status' => 'succeeded'],
                            'fake_auto_completed'
                        );
                        $paymentStatus = 'completed';
                        $order->update(['payment_id' => $stripePaymentId, 'payment_status' => 'completed']);
                    } else {
                        $order->update(['payment_id' => $stripePaymentId, 'payment_status' => 'pending']);
                    }

                    Log::info('Card payment processed', [
                        'order_id' => $order->id,
                        'payment_id' => $stripePaymentId,
                        'is_fake' => $stripeIsFake,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Stripe intent creation failed during checkout', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                    $order->update(['payment_status' => 'failed']);
                    return response()->json([
                        'success' => false,
                        'message' => 'Card payment could not be started. ' . ($e->getMessage() ?: 'Please try again or use another payment method.'),
                    ], 500);
                }
            } else {
                // For bank_transfer or cod, let OrderService handle payment creation
                if ($paymentMethod === 'bank_transfer' && $request->hasFile('payment_slip')) {
                    $slipPath = $request->file('payment_slip')->store('payment-slips', 'public');
                    $paymentData['payment_slip_path'] = $slipPath;
                }

                $order = $orderService->createOrderFromCart($cart, $address, $paymentMethod, $paymentData);

                if ($paymentMethod === 'cod') {
                    $paymentStatus = 'completed';
                }

                Log::info('Order created with payment method: ' . $paymentMethod, [
                    'order_id' => $order->id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'stripe_client_secret' => $stripeClientSecret,
                'stripe_publishable_key' => config('stripe.publishable_key'),
                'stripe_is_fake' => $stripeIsFake,
                'redirect' => route('order.confirmation', $order->id),
            ]);
        } catch (\RuntimeException $e) {
            // Stock validation errors
            Log::warning('Checkout rejected due to stock issue', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            Log::error('Checkout process failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your order. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
