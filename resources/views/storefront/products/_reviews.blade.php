{{--
    Product Reviews — dark theme, fully responsive.
    Embedded inside .product-page > .tabs-container.
    Available CSS vars (from show.blade.php / storefront layout):
      --a/--a2/--ad  (orange accent), --card (panel bg), --t1/--t2/--t3 (text),
      --bl/--bm (borders), --ok (green), --err (red).
    Available data: $product, $reviews (paginator), $ratingBreakdown, $canReview.
--}}

<div id="product-reviews" class="reviews-ui">

    {{-- ================= HEADER / RATING SUMMARY ================= --}}
    <div class="rvw-summary">
        <div class="rvw-summary-main">
            <div class="rvw-score-wrap">
                <span class="rvw-score">{{ number_format($product->average_rating ?? 0, 1) }}</span>
                <div class="rvw-score-stars">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="rvw-star {{ $i <= round($product->average_rating ?? 0) ? 'rvw-on' : 'rvw-off' }}" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
            </div>
            <p class="rvw-subtitle">
                Based on <strong>{{ $reviews->total() }}</strong> customer review{{ $reviews->total() === 1 ? '' : 's' }}
            </p>
        </div>

        {{-- Rating distribution bars --}}
        <div class="rvw-breakdown">
            @foreach ([5, 4, 3, 2, 1] as $stars)
                @php
                    $count = $ratingBreakdown[$stars] ?? 0;
                    $pct = $product->reviews_count > 0 ? round(($count / $product->reviews_count) * 100) : 0;
                @endphp
                <button type="button" class="rvw-bar-row" data-rating="{{ $stars }}">
                    <span class="rvw-bar-label">{{ $stars }}<svg class="rvw-bar-star" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg></span>
                    <span class="rvw-bar-track"><span class="rvw-bar-fill" style="width:{{ $pct }}%"></span></span>
                    <span class="rvw-bar-count">{{ $count }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ================= WRITE REVIEW CTA / FORM ================= --}}
    @auth
        @if ($canReview)
            <div class="rvw-cta">
                <button type="button" class="rvw-toggle rvw-btn rvw-btn-primary" id="rvw-toggle-form">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="rvw-cta-ico"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Add a review
                </button>

                <form id="rvw-form" method="POST" action="{{ route('storefront.reviews.store', $product) }}" enctype="multipart/form-data" class="rvw-form">
                    @csrf
                    <div class="rvw-form-inner">
                        <div class="rvw-field">
                            <label class="rvw-label">Your rating <span class="rvw-req">*</span></label>
                            <div class="rvw-rate-input" role="radiogroup" aria-label="Your rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" class="rvw-rate-star" data-value="{{ $i }}" aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}">
                                        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    </button>
                                @endfor
                                <span class="rvw-rate-note" id="rvw-rate-note">Tap a star</span>
                                <input type="hidden" name="product_rating" id="rvw-product-rating" value="">
                                <input type="hidden" name="vendor_rating" id="rvw-vendor-rating" value="">
                            </div>
                            @error('product_rating')<p class="rvw-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="rvw-field">
                            <label class="rvw-label" for="rvw-title">Title <span class="rvw-opt">(optional)</span></label>
                            <input type="text" name="review_title" id="rvw-title" maxlength="255"
                                   class="rvw-input" placeholder="Summarise your experience" value="{{ old('review_title') }}">
                        </div>

                        <div class="rvw-field">
                            <label class="rvw-label" for="rvw-text">Your review <span class="rvw-req">*</span></label>
                            <textarea name="review_text" id="rvw-text" rows="4" required minlength="10" maxlength="5000"
                                      class="rvw-input rvw-textarea" placeholder="What did you like or dislike?">{{ old('review_text') }}</textarea>
                            @error('review_text')<p class="rvw-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="rvw-field">
                            <label class="rvw-label" for="rvw-photos">Photos <span class="rvw-opt">(optional, up to 5)</span></label>
                            <label class="rvw-file" for="rvw-photos">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="rvw-file-ico"><path d="M4 16l4.5-4.5a2 2 0 012.8 0L16 16m-2-2l1.5-1.5a2 2 0 012.8 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Upload up to 5 photos</span>
                            </label>
                            <input type="file" name="photos[]" id="rvw-photos" multiple accept="image/*" class="rvw-file-input">
                            <div id="rvw-photo-preview" class="rvw-photo-preview"></div>
                        </div>

                        <div class="rvw-form-actions">
                            <button type="button" class="rvw-btn rvw-btn-ghost" id="rvw-cancel">Cancel</button>
                            <button type="submit" class="rvw-btn rvw-btn-primary" id="rvw-submit" disabled>Submit review</button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div class="rvw-guest">
                <div>
                    <strong>Want to add a review?</strong>
                    <span>You can review this product after purchasing and receiving it.</span>
                </div>
            </div>
        @endif
    @else
        <div class="rvw-guest">
            <div>
                <strong>Add your review</strong>
                <span>Sign in to share your experience with this product.</span>
            </div>
            <a href="{{ route('customer.login') }}" class="rvw-btn rvw-btn-primary">Sign in to review</a>
        </div>
    @endauth

    {{-- ================= TOOLS: count + sort ================= --}}
    <div class="rvw-tools">
        <span class="rvw-tools-count">{{ $reviews->total() }} review{{ $reviews->total() === 1 ? '' : 's' }}</span>
        <div class="rvw-sort">
            <label class="rvw-sort-label" for="rvw-sort-select">Sort</label>
            <select id="rvw-sort-select" class="rvw-select"
                    onchange="window.location='{{ url('/products/' . $product->id) }}?sort='+encodeURIComponent(this.value)">
                <option value="helpful" {{ request('sort', 'helpful') === 'helpful' ? 'selected' : '' }}>Most helpful</option>
                <option value="recent" {{ request('sort') === 'recent' ? 'selected' : '' }}>Most recent</option>
                <option value="highest" {{ request('sort') === 'highest' ? 'selected' : '' }}>Highest rated</option>
                <option value="lowest" {{ request('sort') === 'lowest' ? 'selected' : '' }}>Lowest rated</option>
            </select>
        </div>
    </div>

    {{-- ================= REVIEW CARDS ================= --}}
    <div class="rvw-list">
        @forelse ($reviews as $review)
            @include('storefront.products._reviews-card', ['review' => $review, 'product' => $product])
        @empty
            <div class="rvw-empty">
                <div class="rvw-empty-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 10h8m-8 4h5m5 6l-4-8H4a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2h-1.5L18 22l-1.5-1.5"/></svg></div>
                <h4>No reviews yet</h4>
                <p>Be the first to share your experience with this product.</p>
            </div>
        @endforelse
    </div>

    {{-- ================= PAGINATION ================= --}}
    @if (method_exists($reviews, 'hasPages') && $reviews->hasPages())
        <div class="rvw-pagination">
            {{ $reviews->withQueryString()->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    /* ---------- Write-review form ---------- */
    var toggle = document.getElementById('rvw-toggle-form');
    var form   = document.getElementById('rvw-form');
    var cancel = document.getElementById('rvw-cancel');
    if (toggle && form) {
        toggle.addEventListener('click', function () {
            form.classList.toggle('is-open');
            toggle.classList.toggle('is-hidden');
        });
    }
    if (cancel) {
        cancel.addEventListener('click', function () {
            form.classList.remove('is-open');
            toggle.classList.remove('is-hidden');
            form.reset();
            document.querySelectorAll('.rvw-rate-star').forEach(function (s) { s.classList.remove('is-on'); });
            document.getElementById('rvw-product-rating').value = '';
            document.getElementById('rvw-vendor-rating').value = '';
            document.getElementById('rvw-rate-note').textContent = 'Tap a star';
            document.getElementById('rvw-submit').disabled = true;
            document.getElementById('rvw-photo-preview').innerHTML = '';
        });
    }

    /* ---------- Interactive star input ---------- */
    var stars = document.querySelectorAll('.rvw-rate-star');
    var note = document.getElementById('rvw-rate-note');
    var pIn = document.getElementById('rvw-product-rating');
    var vIn = document.getElementById('rvw-vendor-rating');
    var submit = document.getElementById('rvw-submit');
    var labels = ['Poor', 'Fair', 'Good', 'Very good', 'Excellent'];
    stars.forEach(function (star, idx) {
        star.addEventListener('click', function () {
            var val = parseInt(star.dataset.value, 10);
            paintStars(idx);
            pIn.value = val; vIn.value = val;
            note.textContent = labels[val - 1] || val + ' star';
            validateForm();
        });
        star.addEventListener('mouseenter', function () { paintStars(idx); });
    });
    document.getElementById('rvw-form').addEventListener('mouseleave', function () {
        var cur = parseInt(pIn.value, 10) - 1;
        if (cur >= 0) paintStars(cur);
    });
    function paintStars(activeIdx) {
        stars.forEach(function (s, i) { s.classList.toggle('is-on', i <= activeIdx); });
    }

    function validateForm() {
        var ok = pIn.value !== '';
        submit.disabled = !ok;
    }

    /* ---------- Photo preview ---------- */
    var fileInput = document.getElementById('rvw-photos');
    var preview = document.getElementById('rvw-photo-preview');
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            preview.innerHTML = '';
            Array.from(fileInput.files).slice(0, 5).forEach(function (file) {
                var img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = file.name;
                preview.appendChild(img);
            });
        });
    }

    /* ---------- Helpful / Unhelpful AJAX voting + toast popup ---------- */
    var toastHost = document.getElementById('rvw-toast-host');
    if (!toastHost) {
        toastHost = document.createElement('div');
        toastHost.id = 'rvw-toast-host';
        toastHost.className = 'rvw-toast-host';
        document.body.appendChild(toastHost);
    }

    // Toast styles are injected once so this works regardless of theme.
    var toastStyle = document.createElement('style');
    toastStyle.textContent = [
        '.rvw-toast-host{position:fixed;bottom:24px;right:24px;z-index:100;display:flex;flex-direction:column;gap:10px;pointer-events:none}',
        '.rvw-toast{pointer-events:auto;display:flex;align-items:center;gap:12px;min-width:240px;max-width:340px;padding:14px 18px;border-radius:12px;background:#0f172a;color:#fff;font:500 14px/1.4 "Poppins",sans-serif;box-shadow:0 12px 40px rgba(0,0,0,.35);opacity:0;transform:translateY(16px);transition:opacity .3s ease,transform .3s ease;border-left:4px solid #22c55e}',
        '.rvw-toast.is-visible{opacity:1;transform:translateY(0)}',
        '.rvw-toast-error{border-left-color:#ef4444}',
        '.rvw-toast-ico{flex-shrink:0;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;background:rgba(34,197,94,.2);color:#4ade80}',
        '.rvw-toast-error .rvw-toast-ico{background:rgba(239,68,68,.2);color:#f87171}',
        '.rvw-toast-msg{flex:1}',
        '@media (max-width:640px){.rvw-toast-host{left:16px;right:16px;bottom:16px}.rvw-toast{max-width:none}}'
    ].join('\n');
    document.head.appendChild(toastStyle);

    function showToast(message, type) {
        type = type || 'success';
        var el = document.createElement('div');
        el.className = 'rvw-toast' + (type === 'error' ? ' rvw-toast-error' : '');
        var icon = type === 'success' ? '\u2713' : '!';
        el.innerHTML = '<span class="rvw-toast-ico">' + icon + '</span><span class="rvw-toast-msg"></span>';
        el.querySelector('.rvw-toast-msg').textContent = message;
        toastHost.appendChild(el);
        requestAnimationFrame(function () { el.classList.add('is-visible'); });
        setTimeout(function () {
            el.classList.remove('is-visible');
            el.addEventListener('transitionend', function () { el.remove(); });
        }, 3200);
    }

    document.querySelectorAll('.rvw-vote-ajax').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (btn.disabled) return;
            btn.disabled = true;

            var body = new URLSearchParams();
            body.append('_token', btn.dataset.csrf);
            body.append('vote_type', btn.dataset.vote);

            fetch(btn.dataset.voteUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': btn.dataset.csrf
                },
                body: body.toString()
            })
            .then(function (r) {
                return r.text().then(function (text) {
                    var data = null;
                    try {
                        data = text ? JSON.parse(text) : null;
                    } catch (error) {
                        throw new Error('The server returned an invalid vote response.');
                    }

                    if (!r.ok || !data) {
                        throw new Error(data && data.message ? data.message : 'Could not record your vote.');
                    }

                    return data;
                });
            })
            .then(function (data) {
                var card = btn.closest('.rvw-card');
                var helpfulButton = card.querySelector('[data-vote="helpful"]');
                var unhelpfulButton = card.querySelector('[data-vote="unhelpful"]');

                helpfulButton.querySelector('.rvw-vote-count').textContent = data.helpful_count;
                unhelpfulButton.querySelector('.rvw-vote-count').textContent = data.unhelpful_count;

                [helpfulButton, unhelpfulButton].forEach(function (voteButton) {
                    var active = voteButton.dataset.vote === data.active_vote;
                    voteButton.classList.toggle('is-active', active);
                    voteButton.setAttribute('aria-pressed', active ? 'true' : 'false');
                });

                showToast(data.message || 'Thanks for your feedback.', 'success');
            })
            .catch(function (err) {
                showToast(err.message || 'Could not record your vote. Please try again.', 'error');
                console.error('vote error', err);
            })
            .finally(function () {
                btn.disabled = false;
            });
        });
    });

})();
</script>
@endpush