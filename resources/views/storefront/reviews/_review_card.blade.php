<div class="border-b border-gray-100 pb-6" data-review-id="{{ $review->id }}">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold">
                {{ strtoupper(substr($review->customer->name ?? 'U', 0, 1)) }}
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-1">
                <span class="font-medium text-gray-900">{{ $review->customer->name ?? 'Anonymous' }}</span>
                @if ($review->is_verified_purchase)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                        Verified Purchase
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-1 mb-2">
                @for ($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= $review->product_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
                <span class="text-sm text-gray-500 ml-2">{{ $review->created_at->format('M d, Y') }}</span>
            </div>

            @if ($review->review_title)
                <h4 class="font-semibold text-gray-900 mb-1">{{ $review->review_title }}</h4>
            @endif

            <p class="text-gray-700 text-sm leading-relaxed mb-3">{{ $review->review_text }}</p>

            @if ($review->images->count() > 0)
                <div class="flex gap-2 mb-3 flex-wrap">
                    @foreach ($review->images as $image)
                        <img src="{{ Storage::url($image->image_url) }}" alt="Review photo" class="w-16 h-16 object-cover rounded border border-gray-200 cursor-pointer hover:opacity-80" onclick="window.open('{{ Storage::url($image->image_url) }}')">
                    @endforeach
                </div>
            @endif

            @if ($review->response)
                <div class="bg-gray-50 rounded p-3 mb-3 border-l-4 border-emerald-500">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-emerald-700 uppercase">Vendor Response</span>
                        <span class="text-xs text-gray-500">{{ $review->response->created_at->format('M d, Y') }}</span>
                    </div>
                    <p class="text-sm text-gray-700">{{ $review->response->response_text }}</p>
                </div>
            @endif

            <div class="flex items-center gap-4">
                @auth
                    <button type="button" class="review-vote-btn text-xs text-gray-500 hover:text-emerald-600 flex items-center gap-1" data-review-id="{{ $review->id }}" data-vote="helpful">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                        Helpful ({{ $review->is_helpful_count }})
                    </button>
                @else
                    <span class="text-xs text-gray-400">Helpful ({{ $review->is_helpful_count }})</span>
                @endauth

                @auth
                    @if ($review->customer_id === auth()->id())
                        <a href="{{ route('storefront.reviews.edit', [$review->product, $review]) }}" class="text-xs text-gray-500 hover:text-emerald-600">Edit</a>
                        <form method="POST" action="{{ route('storefront.reviews.destroy', [$review->product, $review]) }}" class="inline" onsubmit="return confirm('Delete this review?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-gray-500 hover:text-red-600">Delete</button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>