<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'last_activity',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Methods
    public function getTotalItems()
    {
        return $this->items()->sum('quantity');
    }

    public function getSubtotal()
    {
        return $this->items()->get()->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    public function getItemsByVendor()
    {
        return $this->items()
            ->with(['product.vendor.user', 'product.images', 'variant'])
            ->get()
            ->groupBy(function ($item) {
                return $item->product->vendor_id;
            });
    }

    public function clearCart()
    {
        $this->items()->delete();
    }

    public static function getOrCreateForUser($userId = null, $sessionId = null)
    {
        if ($userId) {
            $cart = self::where('user_id', $userId)->first();
        } else {
            $cart = self::where('session_id', $sessionId)->first();
        }

        if (!$cart) {
            $cart = self::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'last_activity' => now(),
            ]);
        }

        $cart->update(['last_activity' => now()]);

        return $cart;
    }

    public function mergeWithSessionCart($sessionId)
    {
        $sessionCart = self::where('session_id', $sessionId)->first();

        if ($sessionCart) {
            foreach ($sessionCart->items as $sessionItem) {
                $existingItem = $this->items()
                    ->where('product_id', $sessionItem->product_id)
                    ->where('variant_id', $sessionItem->variant_id)
                    ->first();

                if ($existingItem) {
                    $existingItem->increment('quantity', $sessionItem->quantity);
                } else {
                    $sessionItem->update(['cart_id' => $this->id]);
                }
            }

            $sessionCart->delete();
        }
    }
}
