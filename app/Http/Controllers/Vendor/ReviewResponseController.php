<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewResponseController extends Controller
{
    public function store(Request $request, Review $review)
    {
        $vendor = Auth::user()->vendor;

        if ($review->vendor_id !== $vendor?->id) {
            abort(403, 'You can only respond to reviews of your own products.');
        }

        if ($review->hasResponded()) {
            return back()->with('error', 'You have already responded to this review.');
        }

        $validated = $request->validate([
            'response_text' => 'required|string|min:10|max:2000',
        ]);

        ReviewResponse::create([
            'review_id' => $review->id,
            'vendor_id' => $vendor->id,
            'response_text' => $validated['response_text'],
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Response submitted successfully.'], 201);
        }

        return back()->with('success', 'Your response has been posted.');
    }

    public function update(Request $request, Review $review)
    {
        $vendor = Auth::user()->vendor;

        if ($review->vendor_id !== $vendor?->id) {
            abort(403);
        }

        $response = $review->response;

        if (!$response || $response->vendor_id !== $vendor->id) {
            abort(403, 'No response found to update.');
        }

        $validated = $request->validate([
            'response_text' => 'required|string|min:10|max:2000',
        ]);

        $response->update(['response_text' => $validated['response_text']]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Response updated successfully.']);
        }

        return back()->with('success', 'Your response has been updated.');
    }

    public function destroy(Request $request, Review $review)
    {
        $vendor = Auth::user()->vendor;

        if ($review->vendor_id !== $vendor?->id) {
            abort(403);
        }

        $response = $review->response;

        if (!$response || $response->vendor_id !== $vendor->id) {
            abort(403, 'No response found to delete.');
        }

        $response->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Response deleted successfully.']);
        }

        return back()->with('success', 'Your response has been removed.');
    }
}