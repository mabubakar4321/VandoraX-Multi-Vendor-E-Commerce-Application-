<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\ProductInventory;
use App\Models\ProductInventoryLog;
use App\Models\VendorOrder;
use App\Models\User;
use App\Notifications\OrderConfirmationNotification;
use App\Notifications\NewVendorOrderNotification;
use App\Services\CommissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Create a main order from a customer's cart, split into vendor sub-orders.
     *
     * @param  Cart            $cart
     * @param  CustomerAddress  $address
     * @param  string           $paymentMethod   card | bank_transfer | cod
     * @param  array|null       $paymentData     extra payment fields
     * @return Order
     */
    public function createOrderFromCart(
        Cart $cart,
        CustomerAddress $address,
        string $paymentMethod,
        ?array $paymentData = null
    ): Order {
        $user = $cart->user;

        $cartItems = $cart->items()->with(['product.vendor', 'product.category', 'variant'])->get();

        if ($cartItems->isEmpty()) {
            throw new \RuntimeException('Cart is empty; cannot create order.');
        }

        // Validate stock before creating any records
        $this->validateStock($cartItems);

        return DB::transaction(function () use ($cart, $address, $paymentMethod, $paymentData, $cartItems, $user) {
            $totalAmount = 0;
            $totalCommission = 0;

            $groupedByVendor = $cartItems->groupBy(fn ($item) => $item->product->vendor_id);
            $vendorCount = $groupedByVendor->count();

            // 1. Create main order
            $order = Order::create([
                'customer_id'       => $user->id,
                'order_number'      => $this->generateOrderNumber(),
                'total_amount'      => 0,
                'commission_amount' => 0,
                'vendor_count'      => $vendorCount,
                'status'            => 'pending',
                'payment_status'    => $this->resolvePaymentStatus($paymentMethod),
                'notes'            => null,
            ]);

            // Record creation in status history
            OrderStatusHistory::record(
                $order,
                'created',
                'Order created via checkout.',
                $user,
                null
            );

            // 2. Create delivery address
            OrderAddress::create([
                'order_id'              => $order->id,
                'full_name'             => $address->full_name,
                'phone_number'          => $address->phone,
                'address_line_1'        => $address->address_line_1,
                'address_line_2'        => $address->address_line_2,
                'city'                  => $address->city,
                'province'              => $address->state_province,
                'postal_code'           => $address->postal_code,
                'country'               => $address->country,
                'delivery_instructions' => $address->delivery_instructions,
            ]);

            // 3. Split into vendor sub-orders
            $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
            $index = 0;

            foreach ($groupedByVendor as $vendorId => $vendorItems) {
                $vendor = $vendorItems->first()->product->vendor;
                $letter = $letters[min($index, count($letters) - 1)];
                $vendorOrderNumber = $order->order_number . '-' . $letter;

                $vendorTotal = 0;
                $vendorCommission = 0;

                // Create the sub-order first so items can reference it
                $vendorOrder = VendorOrder::create([
                    'order_id'         => $order->id,
                    'vendor_id'         => $vendorId,
                    'vendor_order_number' => $vendorOrderNumber,
                    'subtotal'          => 0,
                    'commission_amount' => 0,
                    'vendor_amount'      => 0,
                    'status'            => 'pending',
                ]);

                foreach ($vendorItems as $item) {
                    $itemTotal = $item->price * $item->quantity;
                    $vendorTotal += $itemTotal;

                    $category = $item->product->category;
                    $commissionRate = $category->commission_rate ?? 0;
                    $commission = round($itemTotal * $commissionRate / 100, 2);
                    $vendorCommission += $commission;
                    $totalCommission += $commission;

                    $orderItem = OrderItem::create([
                        'order_id'         => $order->id,
                        'product_id'       => $item->product_id,
                        'variant_id'       => $item->variant_id,
                        'vendor_order_id'  => $vendorOrder->id,
                        'quantity'         => $item->quantity,
                        'price_at_purchase'=> $item->price,
                        'total_amount'     => $itemTotal,
                        'status'           => 'pending',
                        'return_requested' => false,
                    ]);

                    // Deduct stock
                    $this->deductStock($item->product, $item->variant, $item->quantity);
                }

                // Update sub-order totals
                $vendorOrder->update([
                    'subtotal'          => $vendorTotal,
                    'commission_amount' => $vendorCommission,
                    'vendor_amount'     => $vendorTotal - $vendorCommission,
                ]);

                $totalAmount += $vendorTotal;
                $index++;
            }

            // 4. Finalise order totals
            $order->update([
                'total_amount'      => $totalAmount,
                'commission_amount' => $totalCommission,
            ]);

            // 5. Payment handling
            $this->handlePayment($order, $paymentMethod, $paymentData);

            // 5b. Record commission per vendor sub-order (pending until escrow release)
            app(CommissionService::class)->recordForOrder($order);

            // 6. Notify each vendor of their sub-order
            foreach ($order->vendorOrders as $vendorOrder) {
                $vendorUser = $vendorOrder->vendor?->user;
                if ($vendorUser) {
                    $vendorUser->notify(new NewVendorOrderNotification($vendorOrder));
                }
            }

            // 7. Notify customer
            $user->notify(new OrderConfirmationNotification($order));

            // 8. Clear cart
            $cart->clearCart();

            Log::info('Order created and split into vendor sub-orders', [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'vendor_count' => $vendorCount,
            ]);

            return $order;
        });
    }

    /**
     * Cancel an entire order before any vendor confirms.
     * Restores stock and refunds payment.
     */
    public function cancelOrder(Order $order, ?User $cancelledBy = null, string $reason = 'Customer cancelled'): void
    {
        DB::transaction(function () use ($order, $cancelledBy, $reason) {
            // Only cancel if no vendor has confirmed yet
            $anyConfirmed = $order->vendorOrders()
                ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
                ->exists();

            if ($anyConfirmed) {
                throw new \RuntimeException('Cannot cancel: a vendor has already confirmed this order.');
            }

            $oldStatus = $order->status;
            $order->update(['status' => 'cancelled']);

            OrderStatusHistory::record(
                $order,
                'cancelled',
                "Order cancelled: {$reason}",
                $cancelledBy,
                $oldStatus
            );

            // Cancel all vendor sub-orders
            foreach ($order->vendorOrders as $vendorOrder) {
                if ($vendorOrder->status === 'pending') {
                    $vendorOrder->update(['status' => 'cancelled']);
                }
            }

            // Restore stock
            $this->restoreStock($order);

            // Refund payment
            $this->refundPayment($order, $reason);
        });
    }

    /**
     * Cancel individual order items where the vendor has not yet confirmed.
     *
     * @param  Order        $order
     * @param  int[]        $itemIds
     * @param  User|null    $cancelledBy
     */
    public function cancelItems(Order $order, array $itemIds, ?User $cancelledBy = null): void
    {
        DB::transaction(function () use ($order, $itemIds, $cancelledBy) {
            $cancelableItems = $order->items()
                ->whereIn('id', $itemIds)
                ->where('status', 'pending')
                ->with('vendorOrder')
                ->get();

            $rejected = $cancelableItems->filter(fn ($item) => $item->vendorOrder?->status !== 'pending');
            if ($rejected->isNotEmpty()) {
                throw new \RuntimeException(
                    'Some items cannot be cancelled because their vendor has already confirmed.'
                );
            }

            foreach ($cancelableItems as $item) {
                $item->update(['status' => 'cancelled']);
                $this->restoreStockForItem($item);
            }

            // Recalculate vendor order subtotals
            $affectedVendorOrderIds = $cancelableItems->pluck('vendor_order_id')->unique();
            foreach ($affectedVendorOrderIds as $voId) {
                $vendorOrder = VendorOrder::find($voId);
                if ($vendorOrder) {
                    $activeItems = $order->items()
                        ->where('vendor_order_id', $voId)
                        ->where('status', '!=', 'cancelled')
                        ->get();
                    $newSubtotal = (float) $activeItems->sum('total_amount');
                    // Recompute commission from active items only (previous commission becomes stale when items removed)
                    $newCommission = 0;
                    foreach ($activeItems as $activeItem) {
                        $rate = $activeItem->product->category->commission_rate ?? 0;
                        $newCommission += round((float) $activeItem->total_amount * $rate / 100, 2);
                    }
                    $vendorOrder->update([
                        'subtotal'          => $newSubtotal,
                        'commission_amount' => $newCommission,
                        'vendor_amount'     => $newSubtotal - $newCommission,
                    ]);

                    // If all items in this sub-order are cancelled, cancel the sub-order
                    $allCancelled = $activeItems->isEmpty();
                    if ($allCancelled && $vendorOrder->status === 'pending') {
                        $vendorOrder->update(['status' => 'cancelled']);
                    }
                }
            }

            // Recalculate main order totals
            $activeItemsAll = $order->items()->where('status', '!=', 'cancelled')->get();
            $order->update([
                'total_amount'      => $activeItemsAll->sum('total_amount'),
                'commission_amount' => $order->vendorOrders()->sum('commission_amount'),
            ]);

            OrderStatusHistory::record(
                $order,
                'items_cancelled',
                'Items cancelled: ' . implode(', ', $itemIds),
                $cancelledBy,
                $order->status
            );
        });
    }

    // ─────────────────────────────────────────────────────────────
    // Stock helpers
    // ─────────────────────────────────────────────────────────────

    protected function validateStock($cartItems): void
    {
        foreach ($cartItems as $item) {
            $available = $this->getAvailableStock($item->product, $item->variant);
            if ($available < $item->quantity) {
                $product = $item->product;
                $variantLabel = $item->variant ? " ({$item->variant->variant_value})" : '';
                throw new \RuntimeException(
                    "Insufficient stock for {$product->name}{$variantLabel}. "
                    . "Available: {$available}, Requested: {$item->quantity}."
                );
            }
        }
    }

    protected function getAvailableStock($product, $variant): int
    {
        if ($variant) {
            $inventory = ProductInventory::where('product_id', $variant->product_id)
                ->where('variant_id', $variant->id)
                ->first();
            if ($inventory) {
                return $inventory->getAvailableStock();
            }
            // Fallback to variant.stock
            return (int) $variant->stock;
        }

        $inventory = ProductInventory::where('product_id', $product->id)
            ->whereNull('variant_id')
            ->first();
        if ($inventory) {
            return $inventory->getAvailableStock();
        }

        // If product has variants, sum all variant stock
        if ($product->variants()->count() > 0) {
            return (int) $product->variants()->sum('stock');
        }

        return 0;
    }

    protected function deductStock($product, $variant, int $quantity): void
    {
        if ($variant) {
            $inventory = ProductInventory::where('product_id', $variant->product_id)
                ->where('variant_id', $variant->id)
                ->lockForUpdate()
                ->first();

            if ($inventory) {
                $before = $inventory->quantity;
                $inventory->decrement('quantity', $quantity);
                $inventory->save();
                ProductInventoryLog::create([
                    'product_id'      => $variant->product_id,
                    'variant_id'      => $variant->id,
                    'quantity_before' => $before,
                    'quantity_after'  => $inventory->quantity,
                    'quantity_change' => -$quantity,
                    'action_type'     => 'order_placed',
                    'reason'          => 'Order placed',
                    'created_by'      => auth()->id(),
                ]);
            } else {
                // Fallback: decrement variant.stock
                $before = $variant->stock;
                $variant->decrement('stock', $quantity);
                ProductInventoryLog::create([
                    'product_id'      => $variant->product_id,
                    'variant_id'      => $variant->id,
                    'quantity_before' => $before,
                    'quantity_after'  => $variant->stock,
                    'quantity_change' => -$quantity,
                    'action_type'     => 'order_placed',
                    'reason'          => 'Order placed (fallback)',
                    'created_by'      => auth()->id(),
                ]);
            }
        } else {
            $inventory = ProductInventory::where('product_id', $product->id)
                ->whereNull('variant_id')
                ->lockForUpdate()
                ->first();

            if ($inventory) {
                $before = $inventory->quantity;
                $inventory->decrement('quantity', $quantity);
                $inventory->save();
                ProductInventoryLog::create([
                    'product_id'      => $product->id,
                    'quantity_before' => $before,
                    'quantity_after'  => $inventory->quantity,
                    'quantity_change' => -$quantity,
                    'action_type'     => 'order_placed',
                    'reason'          => 'Order placed',
                    'created_by'      => auth()->id(),
                ]);
            } else {
                // If product has variants, find one with stock
                $variant = $product->variants()
                    ->where('stock', '>', 0)
                    ->orderBy('stock', 'desc')
                    ->first();
                if ($variant) {
                    $before = $variant->stock;
                    $variant->decrement('stock', $quantity);
                    ProductInventoryLog::create([
                        'product_id'      => $product->id,
                        'variant_id'      => $variant->id,
                        'quantity_before' => $before,
                        'quantity_after'  => $variant->stock,
                        'quantity_change' => -$quantity,
                        'action_type'     => 'order_placed',
                        'reason'          => 'Order placed (auto variant)',
                        'created_by'      => auth()->id(),
                    ]);
                }
            }
        }
    }

    protected function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $this->restoreStockForItem($item);
        }
    }

    protected function restoreStockForItem(OrderItem $item): void
    {
        if ($item->variant_id) {
            $inventory = ProductInventory::where('product_id', $item->product_id)
                ->where('variant_id', $item->variant_id)
                ->first();
            if ($inventory) {
                $before = $inventory->quantity;
                $inventory->increment('quantity', $item->quantity);
                $inventory->save();
            } else {
                $item->variant?->increment('stock', $item->quantity);
            }
        } else {
            $inventory = ProductInventory::where('product_id', $item->product_id)
                ->whereNull('variant_id')
                ->first();
            if ($inventory) {
                $before = $inventory->quantity;
                $inventory->increment('quantity', $item->quantity);
                $inventory->save();
            }
        }

        ProductInventoryLog::create([
            'product_id'      => $item->product_id,
            'variant_id'      => $item->variant_id,
            'quantity_before' => null,
            'quantity_after'  => null,
            'quantity_change' => $item->quantity,
            'action_type'     => 'order_cancelled',
            'reason'          => 'Order cancelled — stock restored',
            'created_by'      => auth()->id(),
        ]);
    }

    /**
     * Public method to restore stock for an order (used by admin).
     */
    public function restoreStockPublic(Order $order): void
    {
        $this->restoreStock($order);
    }


    // ─────────────────────────────────────────────────────────────
    // Payment helpers
    // ─────────────────────────────────────────────────────────────

    protected function refundPayment(Order $order, string $reason): void
    {
        $payment = $order->payment;
        if (!$payment) {
            return;
        }

        if ($payment->payment_gateway === 'stripe' && $payment->status === 'completed') {
            app(\App\Services\StripePaymentService::class)->refundPayment($payment, null, $reason);
        } else {
            $payment->refund($reason);
        }

        $order->update(['payment_status' => 'refunded']);
    }

    protected function handlePayment(Order $order, string $paymentMethod, ?array $paymentData = null): void
    {
        if ($paymentMethod === 'cod') {
            // Create explicit COD payment record so Payment history, escrow reporting, and
            // order/payment join queries remain consistent across all payment methods.
            $payment = Payment::create([
                'order_id'       => $order->id,
                'amount'         => $order->total_amount,
                'currency'       => 'PKR',
                'payment_method' => 'cod',
                'payment_gateway'=> 'cod',
                'transaction_id' => 'COD-' . $order->id . '-' . time(),
                'status'         => 'completed',
                'escrow_status'  => 'held',
            ]);
            \App\Models\EscrowAccount::firstOrCreate(
                ['payment_id' => $payment->id],
                ['amount' => $payment->amount, 'status' => 'held', 'held_at' => now(), 'description' => 'COD escrow hold for order ' . $order->order_number]
            );
            $order->update(['payment_id' => $payment->id, 'payment_status' => 'completed']);
            return;
        }

        if ($paymentMethod === 'card') {
            // Card is handled by StripePaymentService in the controller
            // This is a fallback for non-Stripe card flows
            $payment = Payment::create([
                'order_id'      => $order->id,
                'amount'        => $order->total_amount,
                'currency'      => 'PKR',
                'payment_method'=> 'card',
                'payment_gateway' => 'stripe',
                'transaction_id'=> 'CARD-' . $order->id . '-' . time(),
                'status'        => 'pending',
                'escrow_status' => 'held',
            ]);
            $order->update(['payment_id' => $payment->id, 'payment_status' => 'pending']);
            return;
        }

        if ($paymentMethod === 'bank_transfer') {
            $payment = Payment::create([
                'order_id'      => $order->id,
                'amount'        => $order->total_amount,
                'currency'      => 'PKR',
                'payment_method'=> 'bank_transfer',
                'payment_gateway' => 'bank_transfer',
                'transaction_id'=> 'BANK-' . $order->id . '-' . time(),
                'status'        => 'pending',
                'escrow_status' => 'held',
                'bank_payer_name' => $paymentData['bank_payer_name'] ?? null,
                'bank_payment_app' => $paymentData['bank_payment_app'] ?? null,
                'bank_payment_source' => $paymentData['bank_payment_source'] ?? null,
                'bank_customer_account_number' => $paymentData['bank_customer_account_number'] ?? null,
                'payment_slip_path' => $paymentData['payment_slip_path'] ?? null,
            ]);
            $order->update(['payment_id' => $payment->id, 'payment_status' => 'pending']);
            \App\Models\EscrowAccount::firstOrCreate(
                ['payment_id' => $payment->id],
                ['amount' => $payment->amount, 'status' => 'held', 'held_at' => now(), 'description' => 'Bank transfer escrow hold for order ' . $order->order_number]
            );
            return;
        }
    }

    protected function resolvePaymentStatus(string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'cod'   => 'completed',
            default => 'pending',
        };
    }

    protected function generateOrderNumber(): string
    {
        // Format: ORD-YYYY-XXXX (unique, year-scoped, zero-padded 4-digit sequence)
        // Spec requirement: unique order number — tested for uniqueness + sequential generation
        $year = now()->year;
        $prefix = "ORD-{$year}-";
        $seq = Order::where('order_number', 'like', "{$prefix}%")
            ->orderBy('order_number', 'desc')
            ->value('order_number');

        if ($seq) {
            $lastSeq = (int) substr($seq, -4);
            $seq = $lastSeq + 1;
        } else {
            $seq = 1;
        }

        $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
        // Guard against race/duplicate: retry with incremented sequence if collision
        $attempts = 0;
        while (Order::where('order_number', $candidate)->exists() && $attempts < 10) {
            $seq++;
            $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
            $attempts++;
        }
        if (Order::where('order_number', $candidate)->exists()) {
            // Final fallback: timestamp suffix guarantees uniqueness
            $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT) . '-' . substr((string) time(), -4);
        }
        return $candidate;
    }
}
