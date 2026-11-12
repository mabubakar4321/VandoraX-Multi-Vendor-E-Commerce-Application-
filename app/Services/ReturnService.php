<?php

namespace App\Services;

use App\Models\Dispute;
use App\Models\OrderItem;
use App\Models\ProductReturn;
use App\Models\RefundRecord;
use App\Models\ReturnItem;
use App\Models\ReturnPhoto;
use App\Models\User;
use App\Models\VendorWithdrawal;
use App\Notifications\ReturnUpdatedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ReturnService
{
    public function create(OrderItem $item, array $data, array $photos = []): ProductReturn
    {
        $item->load(['order', 'vendorOrder', 'product']);
        abort_unless($item->order->customer_id === auth()->id(), 403);

        $deliveredAt = $item->vendorOrder?->delivered_at
            ?? ($item->vendorOrder?->status === 'delivered' ? $item->vendorOrder->updated_at : null);
        if (!$deliveredAt) {
            throw new \RuntimeException('Returns are available only after the item has been delivered.');
        }
        if ($item->status === 'returned') {
            throw new \RuntimeException('This item already has a return request.');
        }
        $quantity = max(1, (int) ($data['quantity'] ?? $item->quantity));
        if ($quantity > $item->quantity) {
            throw new \RuntimeException('The requested return quantity exceeds the purchased quantity.');
        }
        $activeReturns = ProductReturn::where('order_item_id', $item->id)
            ->whereNotIn('status', ['rejected'])
            ->with('returnItems')
            ->get();
        $activeQuantity = $activeReturns->sum(function (ProductReturn $activeReturn) use ($item) {
            return $activeReturn->returnItems->sum('quantity') ?: $item->quantity;
        });
        if ($activeQuantity + $quantity > $item->quantity) {
            throw new \RuntimeException('The requested quantity is already covered by active return requests.');
        }

        return DB::transaction(function () use ($item, $data, $photos, $deliveredAt, $quantity) {
            $unitPrice = (float) $item->total_amount / max(1, (int) $item->quantity);
            $refundAmount = round($unitPrice * $quantity, 2);
            $return = ProductReturn::create([
                'return_number' => 'RET-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8)),
                'order_item_id' => $item->id,
                'customer_id' => $item->order->customer_id,
                'vendor_id' => $item->vendorOrder->vendor_id,
                'reason' => $data['reason'],
                'description' => $data['description'],
                'status' => 'requested',
                'refund_amount' => $refundAmount,
                'return_deadline' => null,
            ]);
            ReturnItem::create([
                'return_id' => $return->id,
                'order_item_id' => $item->id,
                'quantity' => $quantity,
                'item_price' => $unitPrice,
                'refund_amount' => $refundAmount,
                'status' => 'pending',
            ]);
            $item->update(['return_requested' => true]);
            $this->recordStatus($return, null, 'requested', 'Return request submitted.');
            foreach ($photos as $photo) {
                if ($photo instanceof UploadedFile) {
                    ReturnPhoto::create([
                        'return_id' => $return->id,
                        'path' => $photo->store('returns', 'public'),
                        'original_name' => $photo->getClientOriginalName(),
                        'mime_type' => $photo->getMimeType(),
                        'size' => $photo->getSize(),
                    ]);
                }
            }
            $return = $return->fresh(['photos', 'orderItem.product', 'vendor.user']);
            $return->customer?->notify(new ReturnUpdatedNotification($return, 'Your return request was submitted and is waiting for vendor review.'));
            $return->vendor?->user?->notify(new ReturnUpdatedNotification($return, 'A customer submitted a new return request for your review.'));
            return $return;
        });
    }

    public function approve(ProductReturn $return, int $vendorId, array $data): void
    {
        abort_unless($return->vendor_id === $vendorId, 403);
        if (!in_array($return->status, ['requested', 'vendor_review'], true)) {
            throw new \RuntimeException('Only requested returns can be approved.');
        }
        $return->update([
            'return_shipping_address' => $data['return_shipping_address'],
            'return_instructions' => $data['return_instructions'] ?? null,
            'return_label_url' => $data['return_label_url'] ?? null,
            'vendor_approved_at' => now(),
            'return_deadline' => now()->addDays(7),
        ]);
        $return->setStatus('approved', 'Vendor approved the return and provided shipping instructions.');
        $return->load('customer');
        $return->customer?->notify(new ReturnUpdatedNotification($return, 'Your return was approved. Please ship it back using the provided instructions.'));
    }

    public function reject(ProductReturn $return, int $vendorId, string $reason): void
    {
        abort_unless($return->vendor_id === $vendorId, 403);
        if (!in_array($return->status, ['requested', 'vendor_review'], true)) {
            throw new \RuntimeException('This return cannot be rejected now.');
        }
        $return->update(['rejection_reason' => $reason, 'vendor_response' => $reason, 'rejected_at' => now()]);
        $return->setStatus('rejected', $reason);
        $hasAnotherActiveReturn = ProductReturn::where('order_item_id', $return->order_item_id)
            ->where('id', '!=', $return->id)
            ->whereNotIn('status', ['rejected', 'refunded'])
            ->exists();
        $return->orderItem()->update(['return_requested' => $hasAnotherActiveReturn]);
        $return->load('customer');
        $return->customer?->notify(new ReturnUpdatedNotification($return, 'Your return request was rejected. You may appeal this decision to admin.'));
    }

    public function shipBack(ProductReturn $return, array $data): void
    {
        abort_unless($return->customer_id === auth()->id(), 403);
        if ($return->status !== 'approved') {
            throw new \RuntimeException('The vendor must approve this return first.');
        }
        $return->update([
            'return_tracking_number' => $data['return_tracking_number'],
            'return_tracking_url' => $data['return_tracking_url'] ?? null,
            'shipped_back_at' => now(),
            'returned_at' => now(),
        ]);
        $return->setStatus('shipped_back', 'Customer submitted return shipping tracking.');
        $return->load('vendor.user');
        $return->vendor?->user?->notify(new ReturnUpdatedNotification($return, 'The customer shipped the return back and provided tracking information.'));
    }

    public function receive(ProductReturn $return, int $vendorId, ?string $notes = null): void
    {
        abort_unless($return->vendor_id === $vendorId, 403);
        if ($return->status !== 'shipped_back') {
            throw new \RuntimeException('Only shipped-back returns can be marked received.');
        }
        $return->update(['received_at' => now(), 'inspection_notes' => $notes]);
        $return->setStatus('received', $notes ?: 'Vendor received the returned item.');
        $return->load('customer');
        $return->customer?->notify(new ReturnUpdatedNotification($return, 'The vendor marked your returned item as received.'));
    }

    public function appeal(ProductReturn $return, string $claim): Dispute
    {
        abort_unless($return->customer_id === auth()->id(), 403);
        if ($return->status !== 'rejected') {
            throw new \RuntimeException('Only rejected returns can be appealed.');
        }
        if ($return->disputes()->whereIn('status', ['open', 'under_review'])->exists()) {
            throw new \RuntimeException('This return already has an active appeal.');
        }
        $dispute = Dispute::create([
            'dispute_number' => 'DSP-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8)),
            'order_id' => $return->orderItem->order_id,
            'return_id' => $return->id,
            'customer_id' => $return->customer_id,
            'vendor_id' => $return->vendor_id,
            'dispute_type' => 'product_quality',
            'customer_claim' => $claim,
            'status' => 'open',
        ]);
        $return->load(['vendor.user']);
        $return->vendor?->user?->notify(new ReturnUpdatedNotification($return, 'The customer appealed return ' . $return->return_number . '. Admin review is required.'));
        User::admins()->get()->each(
            fn (User $admin) => $admin->notify(new ReturnUpdatedNotification($return, 'A customer appealed return ' . $return->return_number . '.'))
        );
        return $dispute;
    }

    public function refund(ProductReturn $return, ?int $adminId = null, ?string $notes = null): RefundRecord
    {
        if ($return->status !== 'received' && !($return->status === 'rejected' && $adminId !== null)) {
            throw new \RuntimeException('The return must be received before it can be refunded.');
        }
        $item = $return->orderItem()->with(['order.payment', 'returns.returnItems'])->firstOrFail();
        $payment = $item->order->payment;
        $amount = (float) ($return->refund_amount ?: $item->total_amount);

        return DB::transaction(function () use ($return, $item, $payment, $amount, $adminId, $notes) {
            if ($return->refundRecords()->where('status', 'completed')->exists()) {
                throw new \RuntimeException('This return has already been refunded.');
            }

            $source = 'vendor_account';
            $oldStatus = $return->status;
            $gatewayRefundId = null;
            if ($payment?->payment_gateway === 'stripe') {
                $gatewayRefund = app(\App\Services\StripePaymentService::class)
                    ->refundAmount($payment, $amount, 'Return ' . $return->return_number);
                $gatewayRefundId = $gatewayRefund->id ?? null;
            }
            $escrow = $payment?->escrowAccount;
            if ($escrow && $payment->escrow_status === 'held') {
                $held = (float) ($escrow->remaining_amount ?? $escrow->amount);
                $refunded = min($amount, $held);
                $escrow->update([
                    'refunded_amount' => (float) $escrow->refunded_amount + $refunded,
                    'remaining_amount' => max(0, $held - $refunded),
                    'status' => $held <= $refunded ? 'refunded' : 'held',
                    'refunded_at' => now(),
                ]);
                $payment->update([
                    'refunded_amount' => (float) $payment->refunded_amount + $refunded,
                    'escrow_status' => $held <= $refunded ? 'refunded' : 'held',
                    'refunded_at' => $held <= $refunded ? now() : $payment->refunded_at,
                ]);
                $source = 'escrow';
            } else {
                VendorWithdrawal::create([
                    'vendor_id' => $return->vendor_id,
                    'amount' => -$amount,
                    'status' => 'pending',
                    'payout_method' => 'bank_transfer',
                    'reference' => 'RETURN-DEBIT-' . $return->return_number,
                    'notes' => 'Refund debit for returned item.',
                ]);
            }

            $record = RefundRecord::create([
                'return_id' => $return->id,
                'refund_amount' => $amount,
                'status' => 'completed',
                'transaction_id' => $gatewayRefundId ?: 'REF-' . $return->id . '-' . time(),
                'notes' => $notes,
                'refund_source' => $source,
                'processed_by' => $adminId ?: auth()->id(),
                'processed_at' => now(),
            ]);
            $return->update([
                'status' => 'refunded',
                'refund_amount' => $amount,
                'refund_source' => $source,
                'vendor_debit_amount' => $source === 'vendor_account' ? $amount : 0,
                'refund_processed_at' => now(),
                'refund_approved_at' => now(),
            ]);
            $this->recordStatus($return, $oldStatus, 'refunded', 'Refund completed from ' . $source . '.');
            $return->returnItems()->update(['status' => 'refunded']);
            $returnedQuantity = $item->returns()
                ->where('status', 'refunded')
                ->with('returnItems')
                ->get()
                ->sum(fn (ProductReturn $refundedReturn) => $refundedReturn->returnItems->sum('quantity'));
            if ($returnedQuantity >= (int) $item->quantity) {
                $item->update(['status' => 'returned', 'return_requested' => false]);
            }
            $return->load('customer');
            $return->customer?->notify(new ReturnUpdatedNotification($return, 'Your refund of ₨' . number_format($amount, 2) . ' has been processed.'));
            $return->load('vendor.user');
            $return->vendor?->user?->notify(new ReturnUpdatedNotification($return, 'The refund for return ' . $return->return_number . ' has been processed.'));
            return $record;
        });
    }

    private function recordStatus(ProductReturn $return, ?string $old, string $new, string $notes): void
    {
        \App\Models\ReturnStatusHistory::create([
            'return_id' => $return->id,
            'old_status' => $old,
            'new_status' => $new,
            'notes' => $notes,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);
    }
}
