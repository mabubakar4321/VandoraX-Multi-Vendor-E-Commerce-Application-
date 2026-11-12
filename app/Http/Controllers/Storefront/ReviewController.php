<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\ReviewHelpfulVote;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    public function create(Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('customer.login')
                ->with('error', 'Please log in to write a review.');
        }

        if (!$this->reviewService->canCustomerReview(Auth::id(), $product->id)) {
            return back()->with('error', 'You can only review products you have purchased and received.');
        }

        $order = Order::whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->where('customer_id', Auth::id())
            ->where('status', 'delivered')
            ->first();

        return view('storefront.reviews.create', compact('product', 'order'));
    }

    public function store(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Please log in to write a review.');
        }

        if (!$this->reviewService->canCustomerReview(Auth::id(), $product->id)) {
            return back()->with('error', 'You can only review products you have purchased and received.');
        }

        $validated = $request->validate([
            'product_rating' => 'required|integer|min:1|max:5',
            'vendor_rating' => 'required|integer|min:1|max:5',
            'review_title' => 'nullable|string|max:255',
            'review_text' => 'required|string|min:10|max:5000',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $order = Order::whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->where('customer_id', Auth::id())
            ->where('status', 'delivered')
            ->firstOrFail();

        $review = $this->reviewService->createReview([
            'product_id' => $product->id,
            'order_id' => $order->id,
            'vendor_id' => $product->vendor_id,
            'customer_id' => Auth::id(),
            'product_rating' => $validated['product_rating'],
            'vendor_rating' => $validated['vendor_rating'],
            'review_title' => $validated['review_title'] ?? null,
            'review_text' => $validated['review_text'],
            'is_verified_purchase' => true,
            'status' => 'approved',
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('reviews/' . $review->id, 'public');
                ReviewImage::create([
                    'review_id' => $review->id,
                    'image_url' => $path,
                    'display_order' => $index,
                ]);
            }
        }

        return redirect()
            ->route('storefront.products.show', $product)
            ->with('success', 'Your review has been submitted.');
    }

    public function edit(Product $product, Review $review)
    {
        if ($review->customer_id !== Auth::id()) {
            abort(403, 'You can only edit your own reviews.');
        }

        return view('storefront.reviews.edit', compact('review', 'product'));
    }

    public function update(Request $request, Product $product, Review $review)
    {
        if ($review->customer_id !== Auth::id()) {
            abort(403, 'You can only edit your own reviews.');
        }

        $validated = $request->validate([
            'product_rating' => 'required|integer|min:1|max:5',
            'vendor_rating' => 'required|integer|min:1|max:5',
            'review_title' => 'nullable|string|max:255',
            'review_text' => 'required|string|min:10|max:5000',
        ]);

        $updatedReview = $this->reviewService->updateReview($review, [
            'product_rating' => $validated['product_rating'],
            'vendor_rating' => $validated['vendor_rating'],
            'review_title' => $validated['review_title'] ?? null,
            'review_text' => $validated['review_text'],
        ]);

        return back()->with('success', 'Your review has been updated.');
    }

    public function destroy(Product $product, Review $review)
    {
        if ($review->customer_id !== Auth::id()) {
            abort(403, 'You can only delete your own reviews.');
        }

        $productId = $review->product_id;
        $this->reviewService->deleteReview($review);

        return redirect()
            ->route('storefront.products.show', $productId)
            ->with('success', 'Your review has been deleted.');
    }

    public function vote(Request $request, Product $product, Review $review)
    {
        if ($review->product_id !== $product->id || $review->status !== 'approved') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'This review is not available for voting.'], 404);
            }

            abort(404);
        }

        $validated = $request->validate(['vote_type' => 'required|in:helpful,unhelpful']);

        $userId = Auth::id();
        $existingVote = ReviewHelpfulVote::where('review_id', $review->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingVote) {
            if ($existingVote->vote_type === $validated['vote_type']) {
                $existingVote->delete();
                $review->decrement($validated['vote_type'] === 'helpful' ? 'is_helpful_count' : 'is_unhelpful_count');
            } else {
                $oldType = $existingVote->vote_type;
                $existingVote->update(['vote_type' => $validated['vote_type']]);
                $review->decrement($oldType === 'helpful' ? 'is_helpful_count' : 'is_unhelpful_count');
                $review->increment($validated['vote_type'] === 'helpful' ? 'is_helpful_count' : 'is_unhelpful_count');
            }
        } else {
            ReviewHelpfulVote::create([
                'review_id' => $review->id,
                'user_id' => $userId,
                'vote_type' => $validated['vote_type'],
            ]);
            $review->increment($validated['vote_type'] === 'helpful' ? 'is_helpful_count' : 'is_unhelpful_count');
        }

        $review->refresh();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Thanks for your feedback.',
                'helpful_count' => $review->is_helpful_count,
                'unhelpful_count' => $review->is_unhelpful_count,
                'active_vote' => ReviewHelpfulVote::where('review_id', $review->id)
                    ->where('user_id', $userId)
                    ->value('vote_type'),
            ]);
        }

        return back()->with('success', 'Thanks for your feedback.');
    }
}
