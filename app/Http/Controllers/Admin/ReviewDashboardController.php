<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Vendor;
use App\Models\VendorUnblockRequest;
use App\Models\VendorBadReview;
use Illuminate\Http\Request;

class ReviewDashboardController extends Controller
{
    public function index()
    {
        $totalReviews = Review::count();
        $pendingReviews = Review::pending()->count();
        $approvedReviews = Review::approved()->count();
        $rejectedReviews = Review::rejected()->count();

        $pendingUnblockRequests = VendorUnblockRequest::pending()->with(['vendor.user'])->count();
        $totalUnblockRequests = VendorUnblockRequest::count();

        // Vendors at risk (close to or over the auto-block threshold)
        $threshold = config('vandorax.bad_review_threshold', 10);
        $atRiskVendors = Vendor::where('bad_reviews_count', '>', 0)
            ->orderByDesc('bad_reviews_count')
            ->take(8)
            ->get()
            ->map(function ($v) use ($threshold) {
                $v->progress_pct = min(100, round(($v->bad_reviews_count / $threshold) * 100, 1));
                $v->is_over_threshold = $v->bad_reviews_count >= $threshold;
                return $v;
            });

        $recentReviews = Review::with(['product', 'vendor', 'customer'])
            ->latest()
            ->take(10)
            ->get();

        $recentUnblockRequests = VendorUnblockRequest::with(['vendor'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.reviews-dashboard', compact(
            'totalReviews', 'pendingReviews', 'approvedReviews', 'rejectedReviews',
            'pendingUnblockRequests', 'totalUnblockRequests',
            'atRiskVendors', 'recentReviews', 'recentUnblockRequests', 'threshold'
        ));
    }
}
