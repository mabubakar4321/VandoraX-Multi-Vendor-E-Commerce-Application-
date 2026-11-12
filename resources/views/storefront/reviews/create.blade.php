@extends('layouts.storefront')

@section('title', 'Write a Review - ' . $product->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('storefront.products.show', $product) }}" class="hover:text-emerald-600">{{ $product->name }}</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900">Write a Review</span>
    </nav>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Write a Review</h1>
        <p class="text-gray-600 mb-8">Share your experience with <strong>{{ $product->name }}</strong></p>

        <form method="POST" action="{{ route('storefront.reviews.store', $product) }}" enctype="multipart/form-data" id="review-form">
            @csrf

            {{-- Star Rating --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Overall Rating <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-2" id="star-rating">
                    @for ($i = 1; $i <= 5; $i++)
                        <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 focus:outline-none transition-colors" data-value="{{ $i }}">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                    @endfor
                    <span id="rating-text" class="ml-2 text-sm text-gray-500">Select a rating</span>
                </div>
                <input type="hidden" name="product_rating" id="product_rating" value="{{ old('product_rating') }}" required>
                @error('product_rating')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Review Title --}}
            <div class="mb-6">
                <label for="review_title" class="block text-sm font-medium text-gray-700 mb-2">Review Title (optional)</label>
                <input type="text" name="review_title" id="review_title" value="{{ old('review_title') }}" maxlength="100"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Summarize your experience">
                @error('review_title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Review Text --}}
            <div class="mb-6">
                <label for="review_text" class="block text-sm font-medium text-gray-700 mb-2">Your Review <span class="text-red-500">*</span></label>
                <textarea name="review_text" id="review_text" rows="6" required minlength="10" maxlength="5000"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="What did you like or dislike? How was the quality, fit, and value?">{{ old('review_text') }}</textarea>
                <div class="flex justify-between mt-1">
                    @error('review_text')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <span class="text-xs text-gray-400 ml-auto"><span id="char-count">0</span>/5000</span>
                </div>
            </div>

            {{-- Photo Upload --}}
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Add Photos (optional)</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-emerald-400 transition-colors">
                    <input type="file" name="images[]" id="review-images" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                    <label for="review-images" class="cursor-pointer">
                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-sm text-gray-600">Click to upload photos</span>
                        <span class="block text-xs text-gray-400 mt-1">JPEG, PNG, WebP up to 5MB each (max 5 photos)</span>
                    </label>
                </div>
                <div id="image-preview-container" class="flex gap-2 mt-3 flex-wrap"></div>
                @error('images.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('storefront.products.show', $product) }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Cancel</a>
                <button type="submit" class="px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed" id="submit-btn" disabled>
                    Submit Review
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const starBtns = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('product_rating');
    const ratingText = document.getElementById('rating-text');
    const submitBtn = document.getElementById('submit-btn');
    const textarea = document.getElementById('review_text');
    const charCount = document.getElementById('char-count');
    const labels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];

    function updateStars(value) {
        starBtns.forEach((btn, idx) => {
            btn.classList.toggle('text-yellow-400', idx < value);
            btn.classList.toggle('text-gray-300', idx >= value);
        });
        ratingText.textContent = labels[value] || 'Select a rating';
        ratingInput.value = value;
        checkSubmit();
    }

    function checkSubmit() {
        const hasRating = ratingInput.value && parseInt(ratingInput.value) > 0;
        const hasText = textarea.value.trim().length >= 10;
        submitBtn.disabled = !(hasRating && hasText);
    }

    starBtns.forEach(btn => {
        btn.addEventListener('click', () => updateStars(parseInt(btn.dataset.value)));
        btn.addEventListener('mouseenter', () => {
            const val = parseInt(btn.dataset.value);
            starBtns.forEach((b, i) => {
                b.classList.toggle('text-yellow-400', i < val);
                b.classList.toggle('text-gray-300', i >= val);
            });
        });
    });

    document.getElementById('star-rating').addEventListener('mouseleave', () => {
        updateStars(parseInt(ratingInput.value) || 0);
    });

    textarea.addEventListener('input', () => {
        charCount.textContent = textarea.value.length;
        checkSubmit();
    });

    document.getElementById('review-images').addEventListener('change', function(e) {
        const container = document.getElementById('image-preview-container');
        container.innerHTML = '';
        Array.from(e.target.files).slice(0, 5).forEach(file => {
            const reader = new FileReader();
            reader.onload = (ev) => {
                const img = document.createElement('img');
                img.src = ev.target.result;
                img.className = 'w-16 h-16 object-cover rounded border border-gray-200';
                container.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });

    if (ratingInput.value) updateStars(parseInt(ratingInput.value));
    checkSubmit();
});
</script>
@endpush
@endsection