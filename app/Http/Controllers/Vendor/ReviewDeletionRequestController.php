<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewDeletionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewDeletionRequestController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(403, 'No vendor profile found.');
        }

        $reviews = Review::where('vendor_id', $vendor->id)
            ->with(['customer', 'product'])
            ->latest()
            ->get();

        $requests = ReviewDeletionRequest::where('vendor_id', $vendor->id)
            ->with(['review.product', 'review.customer'])
            ->latest()
            ->get();

        return view('vendor.reviews.deletion-requests', compact('reviews', 'requests', 'vendor'));
    }

    public function create(Review $review)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor || $review->vendor_id !== $vendor->id) {
            abort(403, 'You can only request deletion of your own reviews.');
        }

        $pendingRequest = ReviewDeletionRequest::where('review_id', $review->id)
            ->pending()
            ->first();

        if ($pendingRequest) {
            return redirect()->route('vendor.reviews.show', $review)
                ->with('info', 'A deletion request for this review is already pending.');
        }

        $review->load(['customer', 'product']);

        return view('vendor.reviews.deletion-request', compact('review', 'vendor'));
    }

    public function store(Request $request, Review $review)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor || $review->vendor_id !== $vendor->id) {
            abort(403, 'You can only request deletion of your own reviews.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:20', 'max:3000'],
        ]);

        if (ReviewDeletionRequest::where('review_id', $review->id)->pending()->exists()) {
            return redirect()->route('vendor.reviews.show', $review)
                ->with('info', 'A deletion request for this review is already pending.');
        }

        ReviewDeletionRequest::create([
            'review_id' => $review->id,
            'vendor_id' => $vendor->id,
            'reason' => $validated['reason'],
        ]);

        return redirect()->route('vendor.reviews.show', $review)
            ->with('success', 'Your review deletion request was submitted for admin review.');
    }
}
