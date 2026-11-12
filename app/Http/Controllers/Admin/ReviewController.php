<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Vendor;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    public function index(Request $request)
    {
        $query = Review::with(['product', 'vendor', 'customer'])
            ->latest();

        if ($request->filled('rating')) {
            $query->where('product_rating', $request->rating);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('review_text', 'like', "%{$search}%")
                  ->orWhere('review_title', 'like', "%{$search}%");
            });
        }

        $reviews = $query->paginate(20)->withQueryString();
        $vendors = Vendor::orderBy('store_name')->get(['id', 'store_name']);

        return view('admin.reviews.index', compact('reviews', 'vendors'));
    }

    public function show(Review $review)
    {
        $review->load(['product', 'vendor', 'customer', 'images', 'response']);

        return view('admin.reviews.show', compact('review'));
    }

    public function approve(Review $review)
    {
        $review->approve();
        $this->reviewService->recalculateProductRating($review->product_id);
        $this->reviewService->recalculateVendorRating($review->vendor_id);

        return back()->with('success', 'Review approved.');
    }

    public function reject(Request $request, Review $review)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $review->reject($validated['reason']);

        return back()->with('success', 'Review rejected.');
    }

    public function destroy(Review $review)
    {
        $productId = $review->product_id;
        $vendorId = $review->vendor_id;

        $this->reviewService->deleteReview($review);

        return back()->with('success', 'Review deleted and ratings recalculated.');
    }

    public function flag(Review $review)
    {
        $review->update(['status' => 'pending']);

        return back()->with('success', 'Review flagged for moderation.');
    }
}