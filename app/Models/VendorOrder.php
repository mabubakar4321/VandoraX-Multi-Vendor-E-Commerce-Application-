<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'vendor_id',
        'vendor_order_number',
        'subtotal',
        'commission_amount',
        'vendor_amount',
        'status',
        'tracking_number',
        'tracking_url',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'vendor_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public const STATUS_PENDING    = 'pending';
    public const STATUS_CONFIRMED  = 'confirmed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED    = 'shipped';
    public const STATUS_DELIVERED  = 'delivered';
    public const STATUS_CANCELLED  = 'cancelled';

    public const VALID_TRANSITIONS = [
        self::STATUS_PENDING    => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
        self::STATUS_CONFIRMED  => [self::STATUS_PROCESSING, self::STATUS_CANCELLED],
        self::STATUS_PROCESSING => [self::STATUS_SHIPPED],
        self::STATUS_SHIPPED    => [self::STATUS_DELIVERED],
        self::STATUS_DELIVERED  => [],
        self::STATUS_CANCELLED  => [],
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'vendor_order_id');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeShipped($query)
    {
        return $query->where('status', self::STATUS_SHIPPED);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    // Methods

    public function canTransitionTo(string $newStatus): bool
    {
        $allowed = self::VALID_TRANSITIONS[$this->status] ?? [];
        return in_array($newStatus, $allowed, true);
    }

    public function confirm(): void
    {
        if (!$this->canTransitionTo(self::STATUS_CONFIRMED)) {
            throw new \RuntimeException("Vendor order #{$this->id} cannot transition from '{$this->status}' to 'confirmed'.");
        }

        $oldStatus = $this->status;
        $this->update([
            'status'       => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        OrderStatusHistory::record(
            $this->order,
            'confirmed',
            "Vendor sub-order {$this->vendor_order_number} confirmed.",
            auth()->user(),
            $oldStatus
        );

        $this->syncParentOrderStatus();
    }

    public function markAsProcessing(): void
    {
        if (!$this->canTransitionTo(self::STATUS_PROCESSING)) {
            throw new \RuntimeException("Vendor order #{$this->id} cannot transition from '{$this->status}' to 'processing'.");
        }

        $oldStatus = $this->status;
        $this->update(['status' => self::STATUS_PROCESSING]);

        OrderStatusHistory::record(
            $this->order,
            'processing',
            "Vendor sub-order {$this->vendor_order_number} moved to processing.",
            auth()->user(),
            $oldStatus
        );

        $this->syncParentOrderStatus();
    }

    public function markAsShipped($trackingNumber = null, $trackingUrl = null)
    {
        if (!$this->canTransitionTo(self::STATUS_SHIPPED)) {
            throw new \RuntimeException("Vendor order #{$this->id} cannot transition from '{$this->status}' to 'shipped'.");
        }

        $oldStatus = $this->status;
        $this->update([
            'status'          => self::STATUS_SHIPPED,
            'tracking_number' => $trackingNumber,
            'tracking_url'    => $trackingUrl,
            'shipped_at'      => now(),
        ]);

        OrderStatusHistory::record(
            $this->order,
            'shipped',
            "Vendor sub-order {$this->vendor_order_number} shipped. Tracking: " . ($trackingNumber ?: 'N/A'),
            auth()->user(),
            $oldStatus
        );

        $this->syncParentOrderStatus();
    }

    public function markAsDelivered()
    {
        if (!$this->canTransitionTo(self::STATUS_DELIVERED)) {
            throw new \RuntimeException("Vendor order #{$this->id} cannot transition from '{$this->status}' to 'delivered'.");
        }

        $oldStatus = $this->status;
        $this->update([
            'status'       => self::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);

        OrderStatusHistory::record(
            $this->order,
            'delivered',
            "Vendor sub-order {$this->vendor_order_number} delivered.",
            auth()->user(),
            $oldStatus
        );

        $this->syncParentOrderStatus();
    }

    public function cancel(string $reason = 'Vendor cancelled'): void
    {
        if (!$this->canTransitionTo(self::STATUS_CANCELLED)) {
            throw new \RuntimeException("Vendor order #{$this->id} cannot be cancelled from '{$this->status}'.");
        }

        $oldStatus = $this->status;
        $this->update(['status' => self::STATUS_CANCELLED]);

        OrderStatusHistory::record(
            $this->order,
            'cancelled',
            "Vendor sub-order {$this->vendor_order_number} cancelled: {$reason}",
            auth()->user(),
            $oldStatus
        );

        $this->syncParentOrderStatus();
    }

    /**
     * Sync the parent order status based on all its vendor sub-orders.
     */
    public function syncParentOrderStatus(): void
    {
        $order = $this->order()->with('vendorOrders')->first();
        $statuses = $order->vendorOrders->pluck('status')->values();

        if ($statuses->contains(self::STATUS_CANCELLED)) {
            $newStatus = 'cancelled';
        } elseif ($statuses->isNotEmpty() && $statuses->every(fn ($s) => $s === self::STATUS_DELIVERED)) {
            $newStatus = 'delivered';
        } elseif ($statuses->isNotEmpty() && $statuses->every(fn ($s) => in_array($s, [self::STATUS_SHIPPED, self::STATUS_DELIVERED], true))) {
            $newStatus = 'shipped';
        } elseif ($statuses->isNotEmpty() && $statuses->every(fn ($s) => in_array($s, [self::STATUS_PROCESSING, self::STATUS_SHIPPED, self::STATUS_DELIVERED], true))) {
            $newStatus = 'processing';
        } elseif ($statuses->isNotEmpty() && $statuses->every(fn ($s) => in_array($s, [self::STATUS_CONFIRMED, self::STATUS_PROCESSING, self::STATUS_SHIPPED, self::STATUS_DELIVERED], true))) {
            $newStatus = 'confirmed';
        } else {
            $newStatus = 'pending';
        }

        if ($order->status !== $newStatus) {
            $order->status = $newStatus;
            $order->save();
        }
    }
}
