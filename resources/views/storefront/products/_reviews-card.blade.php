{{--
    Single review card — dark theme, responsive.
    Expects: $review, $product.
--}}
@php
    $rating = (int) ($review->product_rating ?? 0);
    $initial = strtoupper(substr($review->customer->name ?? 'A', 0, 1));
    $voted = auth()->id() ? $review->getUserVote(auth()->id()) : null;
@endphp

<article class="rvw-card" data-rating="{{ $rating }}">
    <div class="rvw-card-head">
        <span class="rvw-avatar" aria-hidden="true">{{ $initial }}</span>

        <div class="rvw-card-meta">
            <div class="rvw-card-name">
                <span class="rvw-cust-name">{{ $review->customer->name ?? 'Anonymous' }}</span>
                @if ($review->is_verified_purchase)
                    <span class="rvw-verified" title="Verified purchase">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-8 8a1 1 0 01-1.4 0l-4-4a1 1 0 111.4-1.4l3.3 3.3 7.3-7.3a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>
                        Verified
                    </span>
                @endif
            </div>
            <div class="rvw-card-stars" aria-label="{{ $rating }} out of 5 stars">
                @for ($i = 1; $i <= 5; $i++)
                    <svg class="rvw-mini-star {{ $i <= $rating ? 'rvw-on' : 'rvw-off' }}" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @endfor
                <span class="rvw-star-num">{{ $rating }}/5</span>
            </div>
        </div>

        <time class="rvw-card-time" datetime="{{ $review->created_at->toIso8601String() }}">
            {{ $review->created_at->diffForHumans() }}
        </time>
    </div>

    @if ($review->review_title)
        <h4 class="rvw-card-title">{{ $review->review_title }}</h4>
    @endif

    <p class="rvw-card-text">{{ $review->review_text }}</p>

    @if ($review->images->count() > 0)
        <div class="rvw-card-photos">
            @foreach ($review->images as $image)
                <a href="{{ $image->getImageUrl() }}" target="_blank" rel="noopener" class="rvw-photo-thumb">
                    <img src="{{ $image->getImageUrl() }}" alt="Review photo" loading="lazy">
                </a>
            @endforeach
        </div>
    @endif

    @if ($review->response)
        <div class="rvw-vendor-reply">
            <div class="rvw-reply-box">
                <div class="rvw-reply-head">
                <span class="rvw-reply-badge">
                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 2l7 4v5c0 4.4-3.1 7.6-7 9-3.9-1.4-7-4.6-7-9V6l7-4z"/></svg>
                    Store response
                </span>
                <span class="rvw-reply-store">{{ $review->response->vendor->store_name ?? 'Vendor' }}</span>
                <span class="rvw-reply-time">{{ $review->response->created_at->diffForHumans() }}</span>
                </div>
                <p class="rvw-reply-text">{{ $review->response->response_text }}</p>
            </div>
        </div>
    @endif

    @if (auth()->check() && auth()->user()->hasRole('customer'))
        <div class="rvw-card-vote">
            <span class="rvw-vote-q">Was this helpful?</span>
            <div class="rvw-vote-actions">
                <button type="button"
                        data-vote="helpful"
                        data-review="{{ $review->id }}"
                        data-product="{{ $product->id }}"
                        data-vote-url="{{ route('storefront.reviews.vote', ['product' => $product->id, 'review' => $review->id]) }}"
                        data-csrf="{{ csrf_token() }}"
                        class="rvw-vote-btn rvw-vote-ajax {{ $voted === 'helpful' ? 'is-active' : '' }}"
                        aria-pressed="{{ $voted === 'helpful' ? 'true' : 'false' }}">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M7 10v7m0-7 4.5-6.5a1.5 1.5 0 012.7 1.2L13 8h3.2a2 2 0 012 2.4l-1.3 6.3a2 2 0 01-2 1.6H9a2 2 0 01-2-2V10z"/></svg>
                    <span class="rvw-vote-count" data-count="helpful">{{ $review->is_helpful_count }}</span>
                </button>
                <button type="button"
                        data-vote="unhelpful"
                        data-review="{{ $review->id }}"
                        data-product="{{ $product->id }}"
                        data-vote-url="{{ route('storefront.reviews.vote', ['product' => $product->id, 'review' => $review->id]) }}"
                        data-csrf="{{ csrf_token() }}"
                        class="rvw-vote-btn rvw-vote-no rvw-vote-ajax {{ $voted === 'unhelpful' ? 'is-active' : '' }}"
                        aria-pressed="{{ $voted === 'unhelpful' ? 'true' : 'false' }}">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M13 10V3M7 10v7M7 10l-4.5 6.5A1.5 1.5 0 0 0 3 17l.7-4.3L3.2 8.1A2 2 0 0 1 5.5-.1H9a2 2 0 0 1 2 2v6.6M13 10l4.5 6.5A1.5 1.5 0 0 0 17 13"/></svg>
                    <span class="rvw-vote-count" data-count="unhelpful">{{ $review->is_unhelpful_count }}</span>
                </button>
            </div>
        </div>
    @endif
</article>
