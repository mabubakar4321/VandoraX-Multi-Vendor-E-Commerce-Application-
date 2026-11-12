<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewResponse;
use App\Models\VendorUnblockRequest;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(403, 'No vendor profile found.');
        }

        $threshold = config('vandorax.bad_review_threshold', 10);

        // Vendors can always view unblock history and submit a proactive
        // unblock request; the only limit is one *pending* request at a time.
        $canRequestUnblock = !VendorUnblockRequest::where('vendor_id', $vendor->id)
            ->pending()
            ->exists();

        $query = Review::where('vendor_id', $vendor->id)
            ->with(['customer', 'product', 'response', 'images'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('rating')) {
            $query->where('product_rating', $request->rating);
        }

        if ($request->filled('responded')) {
            if ($request->responded === 'yes') {
                $query->whereHas('response');
            } else {
                $query->whereDoesntHave('response');
            }
        }

        $reviews = $query->paginate(20)->withQueryString();

        $pendingCount = Review::where('vendor_id', $vendor->id)
            ->where('status', 'pending')
            ->count();

        $badReviewCount = Review::where('vendor_id', $vendor->id)
            ->where('product_rating', '<=', 2)
            ->count();

        $unrespondedCount = Review::where('vendor_id', $vendor->id)
            ->approved()
            ->whereDoesntHave('response')
            ->count();

        $pendingUnblock = VendorUnblockRequest::where('vendor_id', $vendor->id)
            ->pending()
            ->first();

        $recentRequests = VendorUnblockRequest::where('vendor_id', $vendor->id)
            ->latest()
            ->take(5)
            ->get();

        return view('vendor.reviews.index', compact(
            'reviews', 'pendingCount', 'badReviewCount',
            'unrespondedCount', 'pendingUnblock', 'recentRequests', 'vendor',
            'canRequestUnblock', 'threshold'
        ));
    }

    public function show(Review $review)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor || $review->vendor_id !== $vendor->id) {
            abort(403, 'You can only view reviews of your own products.');
        }

        $review->load(['customer', 'product', 'images', 'response.vendor']);

        $relatedReviews = Review::where('vendor_id', $vendor->id)
            ->where('id', '!=', $review->id)
            ->with('customer')
            ->latest()
            ->take(5)
            ->get();

        return view('vendor.reviews.show', compact('review', 'relatedReviews', 'vendor'));
    }
}
