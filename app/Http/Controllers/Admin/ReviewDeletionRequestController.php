<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewDeletionRequest;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewDeletionRequestController extends Controller
{
    public function __construct(private ReviewService $reviewService) {}

    public function index(Request $request)
    {
        $query = ReviewDeletionRequest::with(['review.product', 'vendor', 'reviewer'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.review-deletion-requests.index', compact('requests'));
    }

    public function show(ReviewDeletionRequest $reviewDeletionRequest)
    {
        $reviewDeletionRequest->load([
            'review.product',
            'review.customer',
            'review.images',
            'review.response.vendor',
            'vendor.user',
            'reviewer',
        ]);

        return view('admin.review-deletion-requests.show', compact('reviewDeletionRequest'));
    }

    public function approve(ReviewDeletionRequest $reviewDeletionRequest)
    {
        if ($reviewDeletionRequest->status !== 'pending') {
            return back()->with('error', 'This deletion request has already been processed.');
        }

        DB::transaction(function () use ($reviewDeletionRequest) {
            $review = $reviewDeletionRequest->review;

            if ($review) {
                $this->reviewService->deleteReview($review);
            }

            $reviewDeletionRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('admin.review-deletion-requests.show', $reviewDeletionRequest)
            ->with('success', 'The review was deleted and its ratings were recalculated.');
    }

    public function reject(Request $request, ReviewDeletionRequest $reviewDeletionRequest)
    {
        if ($reviewDeletionRequest->status !== 'pending') {
            return back()->with('error', 'This deletion request has already been processed.');
        }

        $validated = $request->validate([
            'admin_notes' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $reviewDeletionRequest->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.review-deletion-requests.show', $reviewDeletionRequest)
            ->with('success', 'The review deletion request was rejected.');
    }
}
