<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\VendorOrder;
use App\Models\ProductInventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
        $request->validate([
            'address_id' => 'required|exists:customer_addresses,id',
        ]);

        $cart = $this->getCart();

        // Validate cart
        $cartController = new CartController();
        $validationResult = $cartController->validateCart();
        $validationData = json_decode($validationResult->getContent(), true);

        if (!$validationData['valid']) {
            return response()->json([
                'success' => false,
                'message' => 'Some items in your cart are out of stock.',
                'issues' => $validationData['issues'],
            ], 400);
        }

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

        // Use transaction to ensure data integrity
        try {
            DB::beginTransaction();

            $cartItems = $cart->items()->with(['product', 'variant'])->get();
            $totalAmount = 0;
            $totalCommission = 0;

            // Create main order
            $order = Order::create([
                'customer_id' => auth()->id(),
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad(Order::count() + 1, 6, '0', STR_PAD_LEFT),
                'total_amount' => 0, // Will update after calculating
                'commission_amount' => 0,
                'vendor_count' => $cartItems->groupBy('product.vendor_id')->count(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'notes' => null,
            ]);

            // Create order address
            OrderAddress::create([
                'order_id' => $order->id,
                'full_name' => $address->full_name,
                'phone' => $address->phone,
                'address_line_1' => $address->address_line_1,
                'address_line_2' => $address->address_line_2,
                'city' => $address->city,
                'state_province' => $address->state_province,
                'postal_code' => $address->postal_code,
                'country' => $address->country,
                'delivery_instructions' => $address->delivery_instructions,
            ]);

            // Group items by vendor and create order items
            $vendorOrders = [];
            $groupedByVendor = $cartItems->groupBy('product.vendor_id');

            foreach ($groupedByVendor as $vendorId => $vendorItems) {
                $vendorTotal = 0;
                $vendorCommission = 0;

                foreach ($vendorItems as $item) {
                    $itemTotal = $item->price * $item->quantity;
                    $vendorTotal += $itemTotal;

                    // Calculate commission
                    $category = $item->product->category;
                    $commissionRate = $category->commission_rate ?? 0;
                    $commissionAmount = ($itemTotal * $commissionRate / 100);
                    $vendorCommission += $commissionAmount;
                    $totalCommission += $commissionAmount;

                    // Create order item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'quantity' => $item->quantity,
                        'price_at_purchase' => $item->price,
                        'total_amount' => $itemTotal,
                        'status' => 'pending',
                        'return_requested' => false,
                    ]);

                    // Deduct from inventory
                    $this->deductInventory($item->product, $item->variant, $item->quantity);

                    // Log inventory change
                    ProductInventoryLog::create([
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'quantity_change' => -$item->quantity,
                        'reason' => 'order_placed',
                        'reference_id' => $order->id,
                        'reference_type' => 'order',
                    ]);
                }

                // Create vendor order
                VendorOrder::create([
                    'order_id' => $order->id,
                    'vendor_id' => $vendorId,
                    'total_amount' => $vendorTotal,
                    'commission_amount' => $vendorCommission,
                    'vendor_receives' => $vendorTotal - $vendorCommission,
                    'status' => 'pending',
                ]);

                $totalAmount += $vendorTotal;
            }

            // Update order totals
            $order->update([
                'total_amount' => $totalAmount,
                'commission_amount' => $totalCommission,
                'payment_status' => 'completed', // Since no payment gateway
            ]);

            // Clear cart
            $cart->clearCart();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'redirect' => route('order.confirmation', $order->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your order. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Deduct inventory for product/variant
     */
    protected function deductInventory($product, $variant, $quantity)
    {
        if ($variant) {
            $variant->decrement('stock', $quantity);
        } else {
            // For products with variants, decrease variant stocks
            // For products without variants, decrease from inventory
            if ($product->variants()->count() > 0) {
                // If product has variants, find the one with stock
                $variant = $product->variants()
                    ->where('stock', '>', 0)
                    ->orderBy('stock', 'desc')
                    ->first();

                if ($variant) {
                    $variant->decrement('stock', $quantity);
                }
            }
        }
    }
}
