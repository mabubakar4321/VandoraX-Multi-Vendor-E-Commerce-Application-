{{-- Product Review Section Component --}}
@props(['product', 'reviews', 'ratingStats'])

<div id="reviews-section" class="mt-12 border-t border-gray-200 pt-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Customer Reviews</h2>

    {{-- Rating Summary --}}
    <div class="bg-gray-50 rounded-lg p-6 mb-8">
        <div class="flex flex-col md:flex-row gap-8">
            {{-- Overall Rating --}}
            <div class="flex-shrink-0 text-center md:text-left">
                <div class="text-5xl font-bold text-gray-900">{{ number_format($product->average_rating ?? 0, 1) }}</div>
                <div class="flex items-center justify-center md:justify-start mt-2">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($product->average_rating ?? 0) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <div class="text-sm text-gray-600 mt-1">{{ $product->reviews_count ?? 0 }} reviews</div>
            </div>

            {{-- Rating Breakdown --}}
            <div class="flex-1">
                @foreach ([5, 4, 3, 2, 1] as $stars)
                    @php
                        $count = $ratingStats[$stars] ?? 0;
                        $total = $product->reviews_count ?? 1;
                        $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                    @endphp
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-sm font-medium text-gray-700 w-12">{{ $stars }} star</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600 w-8 text-right">{{ $count }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Write Review Button --}}
            <div class="flex-shrink-0 flex items-center">
                @auth
                    @if (auth()->user()->hasPurchasedProduct($product->id))
                        <a href="{{ route('storefront.reviews.create', $product) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Write a Review
                        </a>
                    @else
                        <span class="text-sm text-gray-500 italic">Purchase to review</span>
                    @endif
                @else
                    <a href="{{ route('customer.login') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                        Log in to write a review
                    </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Sort & Filter --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600">Sort by:</span>
            <select id="review-sort" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="helpful">Most Helpful</option>
                <option value="recent">Most Recent</option>
                <option value="highest">Highest Rated</option>
                <option value="lowest">Lowest Rated</option>
            </select>
        </div>
    </div>

    {{-- Reviews List --}}
    <div id="reviews-list" class="space-y-6">
        @forelse ($reviews as $review)
            @include('storefront.reviews._review_card', ['review' => $review])
        @empty
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg">No reviews yet. Be the first to review this product!</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($reviews instanceof \Illuminate\Pagination\LengthAwarePaginator && $reviews->hasPages())
        <div class="mt-8">
            {{ $reviews->links() }}
        </div>
    @endif
</div>