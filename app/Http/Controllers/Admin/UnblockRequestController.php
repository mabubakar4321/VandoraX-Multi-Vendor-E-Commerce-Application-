<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorUnblockRequest;
use App\Models\VendorBadReview;
use App\Models\Review;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Notifications\UnblockRequestReviewedNotification;

class UnblockRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = VendorUnblockRequest::with(['vendor.user'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.unblock-requests.index', compact('requests'));
    }

    public function show(VendorUnblockRequest $unblockRequest)
    {
        $unblockRequest->load(['vendor.user']);

        $badReviews = VendorBadReview::where('vendor_id', $unblockRequest->vendor_id)
            ->with('review')
            ->latest()
            ->get();

        $vendor = $unblockRequest->vendor;

        return view('admin.unblock-requests.show', compact('unblockRequest', 'badReviews', 'vendor'));
    }

    public function approve(VendorUnblockRequest $unblockRequest)
    {
        if ($unblockRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $unblockRequest->approve(auth()->id());

        $vendor = $unblockRequest->vendor;
        $vendor->update([
            'bad_reviews_count' => 0,
            'requested_unblock' => false,
            'unblock_message' => null,
        ]);

        VendorBadReview::where('vendor_id', $vendor->id)->delete();

        $vendor->user?->notify(new UnblockRequestReviewedNotification($unblockRequest, true));

        return redirect()->route('admin.unblock-requests.show', $unblockRequest)
            ->with('success', 'Vendor unblocked successfully. Bad review count reset.');
    }

    public function reject(Request $request, VendorUnblockRequest $unblockRequest)
    {
        if ($unblockRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $unblockRequest->reject(auth()->id(), $validated['reason']);

        $unblockRequest->vendor->user?->notify(new UnblockRequestReviewedNotification($unblockRequest, false));

        return redirect()->route('admin.unblock-requests.show', $unblockRequest)
            ->with('success', 'Unblock request rejected.');
    }
}
