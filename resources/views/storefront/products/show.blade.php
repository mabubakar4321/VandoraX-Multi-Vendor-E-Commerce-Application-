@extends('layouts.storefront')

@section('title', $product->name . ' - VandoraX')

@push('styles')
<style>
    .product-page {
        --a: var(--accent, #ffa500); --a2: var(--accent-light, #ffc04d); --ad: var(--accent-dark, #e68a00);
        --bg: var(--primary, #0a0e27); --card: var(--primary-light, #16213e);
        --t1: var(--text-primary, #ffffff); --t2: var(--text-secondary, #b8c1d9); --t3: var(--text-muted, #7a86a8);
        --bl: var(--border-light, rgba(255,255,255,.08)); --bm: var(--border-medium, rgba(255,255,255,.16));
        --ok: var(--success, #22c55e); --err: var(--error, #ef4444);
        box-sizing: border-box; width: 100%; max-width: 1400px; padding: 0 0 60px; color: var(--t1);
    }
    .product-page *, .product-page *::before, .product-page *::after { box-sizing: border-box; }

    /* Breadcrumbs */
    .product-page .breadcrumbs { display: flex; flex-wrap: wrap; gap: 8px; padding: 20px 0; font-size: 13px; color: var(--t3); }
    .product-page .breadcrumbs a { color: var(--t2); text-decoration: none; transition: color .2s; }
    .product-page .breadcrumbs a:hover { color: var(--a); }
    .product-page .breadcrumbs span { color: var(--t3); }
    .product-page .breadcrumbs span:last-child { color: var(--a); font-weight: 600; }

    /* Layout */
    .product-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; margin-bottom: 64px; }
    .gallery-section { position: sticky; top: 100px; height: fit-content; }

    /* Gallery */
    .main-image-container {
        width: 100%; aspect-ratio: 1/1; background: var(--card); border: 1px solid var(--bl); border-radius: 16px;
        overflow: hidden; margin-bottom: 16px; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 30px rgba(0,0,0,.3);
    }
    .main-image { width: 100%; height: 100%; object-fit: contain; transition: transform .4s ease; }
    .main-image:hover { transform: scale(1.04); }
    .thumbnail-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
    .thumbnail {
        aspect-ratio: 1/1; background: var(--card); border: 2px solid var(--bl); border-radius: 10px;
        cursor: pointer; overflow: hidden; transition: all .25s;
    }
    .thumbnail:hover, .thumbnail.active { border-color: var(--a); box-shadow: 0 0 0 3px rgba(255,165,0,.15); }
    .thumbnail img { width: 100%; height: 100%; object-fit: cover; }

    /* Info */
    .product-info { display: flex; flex-direction: column; gap: 20px; }
    .product-page .badge {
        display: inline-block; width: fit-content; padding: 6px 14px; border-radius: 999px; font-size: 11px; font-weight: 700;
        letter-spacing: .8px; text-transform: uppercase; color: var(--a); background: rgba(255,165,0,.12); border: 1px solid rgba(255,165,0,.35);
    }
    .product-title { font-family: 'Playfair Display', serif; font-size: clamp(28px, 4vw, 40px); font-weight: 800; line-height: 1.2; color: var(--t1); letter-spacing: -.5px; }
    .vendor-info { display: flex; align-items: center; gap: 14px; padding: 16px 20px; background: var(--card); border: 1px solid var(--bl); border-radius: 12px; }
    .vendor-avatar {
        width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 18px; color: var(--bg); background: linear-gradient(135deg, var(--a), var(--ad)); text-transform: uppercase;
    }
    .vendor-details h3 { font-size: 15px; font-weight: 700; color: var(--t1); }
    .vendor-details p { font-size: 12px; color: var(--t3); margin-top: 2px; }
    .product-page .price {
        font-size: clamp(32px, 5vw, 44px); font-weight: 800; margin: 4px 0;
        background: linear-gradient(135deg, var(--a), var(--a2)); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
    }
    .product-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 20px; background: var(--card); border: 1px solid var(--bl); border-radius: 12px; }
    .meta-item { display: flex; flex-direction: column; gap: 4px; }
    .meta-label, .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t3); letter-spacing: .8px; }
    .meta-value { font-size: 14px; font-weight: 600; color: var(--t1); }
    .meta-value.in-stock { color: var(--ok); }
    .meta-value.out-stock { color: var(--err); }

    /* Variants */
    .variants-section, .quantity-section { display: flex; flex-direction: column; gap: 12px; }
    .variants-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px; }
    .variant-card { padding: 14px 12px; background: var(--card); border: 1px solid var(--bm); border-radius: 10px; cursor: pointer; text-align: center; transition: all .25s; }
    .variant-card:hover { border-color: var(--a); transform: translateY(-2px); }
    .variant-card.selected { border-color: var(--a); background: rgba(255,165,0,.12); box-shadow: 0 0 0 3px rgba(255,165,0,.15); }
    .variant-value { font-size: 14px; font-weight: 700; color: var(--t1); margin-bottom: 4px; }
    .variant-price { font-size: 12px; color: var(--a); font-weight: 600; }
    .variant-stock { font-size: 11px; color: var(--t3); margin-top: 4px; }

    /* Quantity */
    .quantity-controls { display: flex; align-items: center; width: fit-content; background: var(--card); border: 1px solid var(--bm); border-radius: 10px; overflow: hidden; }
    .quantity-btn { width: 46px; height: 46px; border: none; background: transparent; color: var(--t2); font-size: 18px; cursor: pointer; transition: all .2s; }
    .quantity-btn:hover { color: var(--a); background: rgba(255,165,0,.08); }
    .quantity-input { width: 60px; height: 46px; border: none; border-left: 1px solid var(--bl); border-right: 1px solid var(--bl); text-align: center; font-size: 15px; font-weight: 700; color: var(--t1); background: transparent; }
    .quantity-input:focus { outline: none; }

    /* Buttons */
    .buttons-section { display: flex; flex-direction: column; gap: 12px; padding-top: 4px; }
    .product-page .btn {
        width: 100%; padding: 15px 24px; border: 1px solid transparent; border-radius: 10px; font-size: 15px; font-weight: 700;
        cursor: pointer; transition: all .3s; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .product-page .btn-primary { background: linear-gradient(135deg, var(--a), var(--ad)); color: var(--bg); box-shadow: 0 8px 25px rgba(255,165,0,.3); }
    .product-page .btn-primary:hover:not(:disabled) { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(255,165,0,.45); }
    .product-page .btn-primary:disabled { background: var(--card); color: var(--t3); box-shadow: none; cursor: not-allowed; border-color: var(--bl); }
    .product-page .btn-secondary, .product-page .btn-wishlist { background: transparent; color: var(--a); border-color: var(--bm); }
    .product-page .btn-secondary:hover, .product-page .btn-wishlist:hover { background: rgba(255,165,0,.1); border-color: var(--a); }

    /* Features */
    .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 8px; }
    .feature-item { text-align: center; padding: 18px 10px; background: var(--card); border: 1px solid var(--bl); border-radius: 12px; }
    .feature-icon { font-size: 26px; margin-bottom: 8px; color: var(--a); }
    .feature-title { font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--t1); letter-spacing: .6px; margin-bottom: 4px; }
    .feature-desc { font-size: 12px; color: var(--t3); }

    /* Tabs */
    .tabs-container { background: var(--card); border: 1px solid var(--bl); border-radius: 16px; padding: 0 32px 32px; box-shadow: 0 8px 30px rgba(0,0,0,.3); }
    .tabs { display: flex; gap: 32px; border-bottom: 1px solid var(--bl); margin-bottom: 28px; overflow-x: auto; }
    .tab { padding: 20px 0; font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--t3); cursor: pointer; border-bottom: 2px solid transparent; transition: all .2s; letter-spacing: .8px; white-space: nowrap; }
    .tab:hover { color: var(--t1); }
    .tab.active { color: var(--a); border-bottom-color: var(--a); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .description-content { font-size: 15px; line-height: 1.8; color: var(--t2); max-width: 800px; }
    .description-content p { margin-bottom: 16px; }
    .description-content strong { color: var(--t1); }
    .specs-table { display: grid; max-width: 800px; }
    .spec-row { display: grid; grid-template-columns: 200px 1fr; padding: 14px 0; border-bottom: 1px solid var(--bl); gap: 24px; }
    .spec-label { font-weight: 700; color: var(--a); font-size: 12px; text-transform: uppercase; letter-spacing: .6px; }
    .spec-value { color: var(--t2); font-size: 14px; }

    /* Responsive */
    @media (max-width: 1024px) {
        .product-grid { grid-template-columns: 1fr; gap: 32px; }
        .gallery-section { position: static; }
    }
    @media (max-width: 768px) {
        .thumbnail-grid { grid-template-columns: repeat(3, 1fr); }
        .features-grid { grid-template-columns: 1fr; }
        .tabs-container { padding: 0 20px 24px; }
        .spec-row { grid-template-columns: 1fr; gap: 4px; }
    }
    @media (max-width: 480px) {
        .product-meta { grid-template-columns: 1fr; }
        .thumbnail-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endpush

@section('content')
<div class="product-page" id="productPage">
    <!-- BREADCRUMBS -->
    <div class="breadcrumbs">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <a href="{{ route('storefront.products.index') }}">Shop</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('storefront.products.index', ['category' => $product->category->id]) }}">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span>{{ Str::limit($product->name, 50) }}</span>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="product-main">
        <!-- PRODUCT GRID -->
        <div class="product-grid">
            <!-- GALLERY -->
            <div class="gallery-section">
                @php
                    $placeholders = [
                        'https://images.unsplash.com/photo-1591290619270-6c1b6c3dc45e?w=800&h=800&fit=crop&q=85',
                    ];
                    $mainImage = $product->images->first();
                    $fallbackImage = $placeholders[0];
                    $mainImageUrl = $mainImage ? $mainImage->getImageUrl() : $fallbackImage;
                @endphp

                <div class="main-image-container">
                    <img id="mainImage" src="{{ $mainImageUrl }}" alt="{{ $product->name }}" class="main-image">
                </div>

                @if($product->images->count() > 1)
                    <div class="thumbnail-grid">
                        @foreach($product->images as $index => $image)
                            <div class="thumbnail {{ $index === 0 ? 'active' : '' }}" onclick="changeImage('{{ $image->getImageUrl() }}', this)">
                                <img src="{{ $image->getImageUrl() }}" alt="{{ $product->name }}">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- PRODUCT INFO -->
            <div class="product-info">
                <span class="badge">{{ $product->moderation_status === 'approved' ? '✓ Verified' : 'Available' }}</span>

                <h1 class="product-title">{{ $product->name }}</h1>

                <div class="vendor-info">
                    <div class="vendor-avatar">{{ substr($product->vendor?->store_name ?? 'V', 0, 1) }}</div>
                    <div class="vendor-details">
                        <h3>{{ $product->vendor?->store_name ?? 'Premium Store' }}</h3>
                        <p>{{ $product->vendor?->user?->name ?? 'Verified Seller' }}</p>
                    </div>
                </div>

                <div class="price" id="productPrice">PKR {{ number_format($product->base_price, 0) }}</div>

                <div class="product-meta">
                    <div class="meta-item">
                        <span class="meta-label">Category</span>
                        <span class="meta-value">{{ $product->category?->name ?? 'General' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Stock</span>
                        <span class="meta-value {{ $product->getTotalStock() > 0 ? 'in-stock' : 'out-stock' }}">
                            {{ $product->getTotalStock() > 0 ? $product->getTotalStock() . ' Available' : 'Out of Stock' }}
                        </span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Delivery</span>
                        <span class="meta-value">{{ $product->delivery_time_days ?? 3 }} - {{ ($product->delivery_time_days ?? 3) + 2 }} Days</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">SKU</span>
                        <span class="meta-value">{{ $product->sku ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- VARIANTS -->
                @if($product->variants->isNotEmpty())
                <div class="variants-section">
                    <div class="section-title">Select Variant</div>
                    <div class="variants-grid">
                        @foreach($product->variants as $variant)
                            <div class="variant-card" data-variant-id="{{ $variant->id }}" onclick="selectVariant(this, {{ $variant->price }}, {{ $variant->stock }})">
                                <div class="variant-value">{{ $variant->variant_value }}</div>
                                <div class="variant-price">PKR {{ number_format($variant->price, 0) }}</div>
                                <div class="variant-stock">{{ $variant->stock > 0 ? 'In stock' : 'Out' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- QUANTITY -->
                <div class="quantity-section">
                    <div class="section-title">Quantity</div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="decreaseQty()">−</button>
                        <input type="number" id="quantity" class="quantity-input" value="1" min="1" max="{{ $product->getTotalStock() }}" readonly>
                        <button class="quantity-btn" onclick="increaseQty()">+</button>
                    </div>
                </div>

                <!-- BUTTONS -->
                <div class="buttons-section">
                    <button class="btn btn-primary" {{ $product->getTotalStock() > 0 ? '' : 'disabled' }} onclick="addToCart()">
                        🛒 Add to Cart
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn btn-secondary">
                        📋 View Cart
                    </a>
                    @if(auth()->check())
                    <button class="btn btn-wishlist" onclick="addToWishlist()">
                        ♡ Save for Later
                    </button>
                    @else
                    <a href="{{ route('customer.login') }}" class="btn btn-wishlist">
                        Login to Save
                    </a>
                    @endif
                </div>

                <!-- FEATURES -->
                <div class="features-grid">
                    <div class="feature-item">
                        <div class="feature-icon">🚚</div>
                        <div class="feature-title">Fast Delivery</div>
                        <div class="feature-desc">{{ $product->delivery_time_days ?? 3 }}-{{ ($product->delivery_time_days ?? 3) + 2 }} days</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <div class="feature-title">Secure</div>
                        <div class="feature-desc">100% Protected</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">↻</div>
                        <div class="feature-title">Returns</div>
                        <div class="feature-desc">7 days return</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABS -->
        <div class="tabs-container">
            <div class="tabs">
                <div class="tab active" onclick="switchTab(0)">Description</div>
                <div class="tab" onclick="switchTab(1)">Specifications</div>
                <div class="tab" onclick="switchTab(2)">Shipping & Returns</div>
            </div>

            <div class="tab-content active" id="tab-0">
                <div class="description-content">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>

            <div class="tab-content" id="tab-1">
                <div class="specs-table">
                    @if($product->specifications->count() > 0)
                        @foreach($product->specifications as $spec)
                            <div class="spec-row">
                                <div class="spec-label">{{ $spec->spec_key }}</div>
                                <div class="spec-value">{{ $spec->spec_value }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="spec-row">
                            <div class="spec-label">Product</div>
                            <div class="spec-value">{{ $product->name }}</div>
                        </div>
                    @endif
                    <div class="spec-row">
                        <div class="spec-label">SKU</div>
                        <div class="spec-value">{{ $product->sku ?? 'Not assigned' }}</div>
                    </div>
                    <div class="spec-row">
                        <div class="spec-label">Category</div>
                        <div class="spec-value">{{ $product->category?->name ?? 'General' }}</div>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="tab-2">
                <div class="description-content">
                    <p><strong>Shipping:</strong> Delivery in {{ $product->delivery_time_days ?? 3 }}-{{ ($product->delivery_time_days ?? 3) + 2 }} business days. Free shipping on all orders.</p>
                    <p><strong>Returns:</strong> 7-day return policy for unused items in original packaging. Refund processed within 5-7 business days.</p>
                    <p><strong>Support:</strong> Available 24/7 through VandoraX platform. Direct chat with seller available.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Centers the product page in the viewport (equal space left and right)
    (function () {
        function centerProductPage() {
            var el = document.getElementById('productPage');
            if (!el) return;
            el.style.marginLeft = '0px';
            var docW = document.documentElement.clientWidth;
            var w = Math.min(1400, docW - 32);
            el.style.width = w + 'px';
            var left = el.getBoundingClientRect().left + window.pageXOffset;
            el.style.marginLeft = (((docW - w) / 2) - left) + 'px';
        }
        centerProductPage();
        window.addEventListener('load', centerProductPage);
        window.addEventListener('resize', centerProductPage);
    })();

    let selectedVariantId = null;

    function changeImage(imageUrl, thumbnail) {
        document.getElementById('mainImage').src = imageUrl;
        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
        thumbnail.classList.add('active');
    }

    function selectVariant(card, price, stock) {
        document.querySelectorAll('.variant-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        selectedVariantId = card.dataset.variantId;
        document.getElementById('productPrice').textContent = 'PKR ' + Number(price).toLocaleString('en-US', {maximumFractionDigits: 0});
    }

    function increaseQty() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.max) || 99;
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    }

    function decreaseQty() {
        const input = document.getElementById('quantity');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    function switchTab(index) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.tab')[index].classList.add('active');
        document.getElementById('tab-' + index).classList.add('active');
    }

    function addToCart() {
        const quantity = parseInt(document.getElementById('quantity').value);
        const btn = event.target;
        const variantId = selectedVariantId || null;

        btn.disabled = true;
        btn.textContent = 'Adding...';

        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: {{ $product->id }},
                variant_id: variantId,
                quantity: quantity
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.textContent = '✓ Added!';
                document.getElementById('quantity').value = 1;
                // Dispatch event to update header badge
                document.dispatchEvent(new CustomEvent('cartUpdated'));
                setTimeout(() => {
                    btn.disabled = false;
                    btn.textContent = '🛒 Add to Cart';
                }, 2000);
            } else {
                alert(data.message || 'Error adding to cart');
                btn.disabled = false;
                btn.textContent = '🛒 Add to Cart';
            }
        })
        .catch(e => {
            console.error('Error:', e);
            btn.disabled = false;
            btn.textContent = '🛒 Add to Cart';
        });
    }

    function addToWishlist() {
        const btn = event.target;
        btn.disabled = true;
        btn.textContent = 'Adding...';

        fetch('{{ route('wishlist.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: {{ $product->id }}
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Dispatch event to update header badge
                document.dispatchEvent(new CustomEvent('wishlistUpdated'));
                btn.textContent = '♡ Saved!';
                setTimeout(() => {
                    btn.disabled = false;
                    btn.textContent = '♡ Save for Later';
                }, 2000);
            } else {
                alert(data.message || 'Could not save item');
                btn.disabled = false;
                btn.textContent = '♡ Save for Later';
            }
        })
        .catch(e => {
            console.error('Wishlist error:', e);
            alert('Could not save item. Please try again.');
            btn.disabled = false;
            btn.textContent = '♡ Save for Later';
        });
    }
</script>
@endpush