<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Vendor;
use App\Models\VendorBadReview;
use App\Models\Product;
use App\Notifications\VendorAutoBlockedNotification;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function createReview(array $data): Review
    {
        return DB::transaction(function () use ($data) {
            $review = Review::create($data);

            // Track bad reviews (1-2 star)
            if ($review->product_rating <= 2) {
                VendorBadReview::create([
                    'vendor_id' => $review->vendor_id,
                    'review_id' => $review->id,
                    'review_rating' => $review->product_rating,
                    'flagged_at' => now(),
                ]);

                $this->refreshVendorBadReviewCount($review->vendor_id);
                $this->checkAutoBlock($review->vendor_id);
            }

            $this->recalculateProductRating($review->product_id);
            $this->recalculateVendorRating($review->vendor_id);

            return $review;
        });
    }

    public function updateReview(Review $review, array $data): Review
    {
        return DB::transaction(function () use ($review, $data) {
            $wasBad = $review->product_rating <= 2;
            $review->update($data);
            $isBad = $review->product_rating <= 2;

            if (!$wasBad && $isBad) {
                VendorBadReview::create([
                    'vendor_id' => $review->vendor_id,
                    'review_id' => $review->id,
                    'review_rating' => $review->product_rating,
                    'flagged_at' => now(),
                ]);
            } elseif ($wasBad && !$isBad) {
                VendorBadReview::where('review_id', $review->id)->delete();
            }

            $this->refreshVendorBadReviewCount($review->vendor_id);
            $this->checkAutoBlock($review->vendor_id);

            $this->recalculateProductRating($review->product_id);
            $this->recalculateVendorRating($review->vendor_id);

            return $review->fresh();
        });
    }

    public function deleteReview(Review $review): void
    {
        DB::transaction(function () use ($review) {
            $productId = $review->product_id;
            $vendorId = $review->vendor_id;

            VendorBadReview::where('review_id', $review->id)->delete();
            $review->delete();

            $this->refreshVendorBadReviewCount($vendorId);
            $this->recalculateProductRating($productId);
            $this->recalculateVendorRating($vendorId);
        });
    }

    public function recalculateProductRating(int $productId): void
    {
        $stats = Review::where('product_id', $productId)
            ->approved()
            ->selectRaw('AVG(product_rating) as avg_rating, COUNT(*) as total_reviews')
            ->first();

        Product::where('id', $productId)->update([
            'average_rating' => round($stats->avg_rating ?? 0, 2),
            'reviews_count' => (int) ($stats->total_reviews ?? 0),
        ]);
    }

    public function recalculateVendorRating(int $vendorId): void
    {
        $avg = Review::where('vendor_id', $vendorId)
            ->approved()
            ->avg('product_rating');

        Vendor::where('id', $vendorId)->update([
            'average_rating' => round($avg ?? 0, 2),
        ]);
    }

    public function getProductRatingBreakdown(int $productId): array
    {
        $rows = Review::where('product_id', $productId)
            ->approved()
            ->selectRaw('product_rating, COUNT(*) as total')
            ->groupBy('product_rating')
            ->pluck('total', 'product_rating');

        $breakdown = [];
        for ($i = 1; $i <= 5; $i++) {
            $breakdown[$i] = (int) ($rows[$i] ?? 0);
        }
        return $breakdown;
    }

    public function canCustomerReview(int $customerId, int $productId): bool
    {
        if (Review::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->exists()) {
            return false;
        }

        return $this->hasDeliveredOrder($customerId, $productId);
    }

    public function hasDeliveredOrder(int $customerId, int $productId): bool
    {
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.customer_id', $customerId)
            ->where('order_items.product_id', $productId)
            ->where('orders.status', 'delivered')
            ->exists();
    }

    private function refreshVendorBadReviewCount(int $vendorId): void
    {
        $count = VendorBadReview::where('vendor_id', $vendorId)->count();
        Vendor::where('id', $vendorId)->update(['bad_reviews_count' => $count]);
    }

    private function checkAutoBlock(int $vendorId): void
    {
        $threshold = config('vandorax.bad_review_threshold', 10);
        $vendor = Vendor::find($vendorId);

        if (!$vendor || $vendor->isBlocked()) {
            return;
        }

        if ($vendor->bad_reviews_count >= $threshold) {
            $this->autoBlockVendor($vendor, $vendor->bad_reviews_count);
        }
    }

    private function autoBlockVendor(Vendor $vendor, int $badCount): void
    {
        $reason = "Your store has been automatically blocked due to {$badCount}+ negative reviews";
        $vendor->blockVendor($reason);
        $vendor->update([
            'requested_unblock' => false,
            'unblock_message' => $reason,
        ]);

        if ($vendor->user) {
            $vendor->user->notify(new VendorAutoBlockedNotification($vendor, $reason));
        }
    }
}
