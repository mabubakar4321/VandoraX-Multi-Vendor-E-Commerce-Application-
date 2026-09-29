<!-- Add to Cart with Sidebar Component -->
<div class="space-y-4">
    <!-- Variant Selection (if product has variants) -->
    @if($product->variants()->count() > 0)
    <div>
        <label class="block text-sm font-semibold text-gray-900 mb-2">
            Select Option
        </label>
        <select id="variant-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            <option value="">Choose an option...</option>
            @foreach($product->variants as $variant)
                <option value="{{ $variant->id }}" data-price="{{ $variant->price }}" data-stock="{{ $variant->stock }}">
                    {{ $variant->variant_value }} - ${{ number_format($variant->price, 2) }}
                    @if($variant->stock <= 0) (Out of Stock) @endif
                </option>
            @endforeach
        </select>
        <p class="text-sm text-gray-600 mt-2">
            Stock: <span id="variant-stock" class="font-semibold">0</span>
        </p>
    </div>
    @endif

    <!-- Quantity Selection -->
    <div>
        <label class="block text-sm font-semibold text-gray-900 mb-2">
            Quantity
        </label>
        <div class="flex items-center border border-gray-300 rounded-lg w-fit">
            <button onclick="decreaseQty()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 transition">−</button>
            <input type="number" id="quantity" value="1" min="1" class="w-16 text-center border-0 focus:ring-0" />
            <button onclick="increaseQty()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 transition">+</button>
        </div>
    </div>

    <!-- Add to Cart Button -->
    <button onclick="addToCart()" id="add-to-cart-btn" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition disabled:bg-gray-400">
        🛒 Add to Cart
    </button>

    <!-- Add to Wishlist Button (if user is logged in) -->
    @if(auth()->check())
    <button onclick="addToWishlist()" class="w-full border border-blue-600 text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
        ♡ Save for Later
    </button>
    @else
    <a href="/auth/login" class="block w-full border border-blue-600 text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition text-center">
        Login to Save for Later
    </a>
    @endif
</div>

<!-- Cart Sidebar -->
<div id="cart-sidebar" class="fixed right-0 top-0 h-full w-96 bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
        <h2 class="text-xl font-bold text-gray-900">Shopping Cart</h2>
        <button onclick="closeCartSidebar()" class="text-gray-500 hover:text-gray-900 text-2xl leading-none">
            ✕
        </button>
    </div>

    <!-- Cart Items -->
    <div id="cart-items-container" class="flex-1 overflow-y-auto p-6">
        <p class="text-center text-gray-500 py-12">Your cart is empty</p>
    </div>

    <!-- Cart Summary -->
    <div class="border-t border-gray-200 p-6 bg-gray-50 space-y-4">
        <div class="space-y-2">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span id="sidebar-subtotal">$0.00</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Shipping</span>
                <span class="text-green-600 font-semibold">Free</span>
            </div>
            <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-gray-200">
                <span>Total</span>
                <span id="sidebar-total">$0.00</span>
            </div>
        </div>

        <div class="space-y-3">
            @if(auth()->check())
                <a href="{{ route('checkout.index') }}" class="block w-full bg-blue-600 text-white text-center px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Proceed to Checkout
                </a>
            @else
                <a href="/auth/login" class="block w-full bg-blue-600 text-white text-center px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Login to Checkout
                </a>
            @endif

            <a href="{{ route('cart.index') }}" class="block w-full border border-gray-300 text-gray-700 text-center px-4 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                View Full Cart
            </a>
            <button onclick="closeCartSidebar()" class="w-full text-gray-700 px-4 py-3 font-semibold hover:text-gray-900 transition">
                Continue Shopping
            </button>
        </div>
    </div>
</div>

<!-- Overlay -->
<div id="cart-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 opacity-0 pointer-events-none transition-opacity duration-300" onclick="closeCartSidebar()"></div>

<style>
    #cart-sidebar {
        max-width: 100%;
    }

    @media (max-width: 640px) {
        #cart-sidebar {
            width: 100%;
        }
    }

    .cart-item {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .cart-item-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 0.5rem;
        background: #f3f4f6;
    }

    .cart-item-details {
        flex: 1;
    }

    .cart-item-name {
        font-weight: 600;
        color: #111827;
        font-size: 0.875rem;
    }

    .cart-item-vendor {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .cart-item-price {
        font-weight: 600;
        color: #111827;
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }

    .cart-item-remove {
        color: #ef4444;
        font-size: 0.75rem;
        cursor: pointer;
        margin-top: 0.5rem;
    }

    .cart-item-remove:hover {
        color: #dc2626;
    }
</style>

<script>
const product = {!! json_encode($product) !!};

function decreaseQty() {
    const qty = parseInt(document.getElementById('quantity').value);
    if (qty > 1) {
        document.getElementById('quantity').value = qty - 1;
    }
}

function increaseQty() {
    const variantSelect = document.getElementById('variant-select');
    const maxStock = variantSelect ? parseInt(variantSelect.options[variantSelect.selectedIndex].dataset.stock) : {{ $product->getTotalStock() }};
    const qty = parseInt(document.getElementById('quantity').value);
    if (qty < maxStock) {
        document.getElementById('quantity').value = qty + 1;
    }
}

function openCartSidebar() {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');

    sidebar.classList.remove('translate-x-full');
    overlay.classList.remove('opacity-0', 'pointer-events-none');
    document.body.style.overflow = 'hidden';
}

function closeCartSidebar() {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');

    sidebar.classList.add('translate-x-full');
    overlay.classList.add('opacity-0', 'pointer-events-none');
    document.body.style.overflow = 'auto';
}

function updateCartSidebar() {
    fetch('/cart-sidebar/items')
        .then(r => r.json())
        .then(data => {
            if (!data.success || data.total_items === 0) {
                document.getElementById('cart-items-container').innerHTML = '<p class="text-center text-gray-500 py-12">Your cart is empty</p>';
                document.getElementById('sidebar-subtotal').textContent = '$0.00';
                document.getElementById('sidebar-total').textContent = '$0.00';
                return;
            }

            // Build cart items HTML
            let html = '';

            data.items_by_vendor.forEach((vendorItems, vendorIndex) => {
                if (vendorIndex > 0) {
                    html += '<div class="border-t-2 border-gray-200 my-3"></div>';
                }

                if (vendorItems.length > 0 && vendorItems[0].vendor_name) {
                    html += '<div class="text-sm font-semibold text-gray-900 px-2 py-2">' + vendorItems[0].vendor_name + '</div>';
                }

                vendorItems.forEach(item => {
                    html += `
                        <div class="cart-item">
                            ${item.product_image ? `<img src="${item.product_image}" alt="${item.product_name}" class="cart-item-image">` : `<div class="cart-item-image"></div>`}
                            <div class="cart-item-details">
                                <div class="cart-item-name">${item.product_name}</div>
                                ${item.variant_name ? `<div class="cart-item-vendor">${item.variant_name}</div>` : ''}
                                <div class="cart-item-vendor">Qty: ${item.quantity}</div>
                                <div class="cart-item-price">$${parseFloat(item.total).toFixed(2)}</div>
                                <div class="cart-item-remove" onclick="removeFromCart(${item.id})">Remove</div>
                            </div>
                        </div>
                    `;
                });
            });

            document.getElementById('cart-items-container').innerHTML = html;
            document.getElementById('sidebar-subtotal').textContent = '$' + parseFloat(data.subtotal).toFixed(2);
            document.getElementById('sidebar-total').textContent = '$' + parseFloat(data.subtotal).toFixed(2);
        })
        .catch(e => {
            console.error('Error loading cart:', e);
        });
}

function removeFromCart(cartItemId) {
    if (!confirm('Remove this item from cart?')) return;

    fetch(`/cart/items/${cartItemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            updateCartSidebar();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(e => {
        console.error('Error:', e);
        alert('An error occurred.');
    });
}

function addToCart() {
    const variantId = document.getElementById('variant-select') ? document.getElementById('variant-select').value : null;
    const quantity = parseInt(document.getElementById('quantity').value);

    if (document.getElementById('variant-select') && !variantId) {
        alert('Please select a variant');
        return;
    }

    const btn = document.getElementById('add-to-cart-btn');
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
            // Show success message
            btn.textContent = '✓ Added!';
            setTimeout(() => {
                btn.disabled = false;
                btn.textContent = '🛒 Add to Cart';
            }, 2000);

            // Reset form
            document.getElementById('quantity').value = 1;
            if (document.getElementById('variant-select')) {
                document.getElementById('variant-select').value = '';
            }

            // Open cart sidebar and update items
            updateCartSidebar();
            openCartSidebar();
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.textContent = '🛒 Add to Cart';
        }
    })
    .catch(e => {
        console.error('Error:', e);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.textContent = '🛒 Add to Cart';
    });
}

function addToWishlist() {
    fetch('{{ route('wishlist.add') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: {{ $product->id }}
        })
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message);
    })
    .catch(e => {
        console.error('Error:', e);
        alert('An error occurred. Please try again.');
    });
}

// Update variant info when selected
@if($product->variants()->count() > 0)
document.getElementById('variant-select').addEventListener('change', function() {
    if (this.value) {
        const stock = parseInt(this.options[this.selectedIndex].dataset.stock);
        document.getElementById('variant-stock').textContent = stock > 0 ? stock : 'Out of Stock';
    }
});

// Set initial stock display
const variantSelect = document.getElementById('variant-select');
if (variantSelect && variantSelect.options[1]) {
    const stock = parseInt(variantSelect.options[1].dataset.stock);
    document.getElementById('variant-stock').textContent = stock > 0 ? stock : 'Out of Stock';
}
@endif

// Close sidebar on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCartSidebar();
    }
});
</script>
