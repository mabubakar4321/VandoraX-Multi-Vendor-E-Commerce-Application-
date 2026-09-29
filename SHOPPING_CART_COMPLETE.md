# VandoraX Shopping Cart & Checkout System - Complete Implementation Guide

## Overview

This document describes the fully functional shopping cart, checkout, and order management system implemented for VandoraX. The system supports:

- ✅ Add products to cart (with variant selection)
- ✅ View cart items grouped by vendor
- ✅ Update quantity with real-time calculations
- ✅ Remove items from cart
- ✅ Clear entire cart
- ✅ Multi-vendor cart display with subtotals
- ✅ Inventory validation and stock checks
- ✅ Checkout process with delivery address management
- ✅ Order creation and confirmation
- ✅ Order tracking and history

## System Architecture

### Database Models

**Cart** - Stores user shopping carts
- `user_id` - Associated user
- `session_id` - For guest checkouts
- `last_activity` - Last interaction timestamp

**CartItem** - Individual items in cart
- `cart_id` - Reference to cart
- `product_id` - Reference to product
- `variant_id` - Optional variant selection
- `quantity` - Number of items
- `price` - Price at time of adding

**CustomerAddress** - Saved delivery addresses
- `customer_id` - Reference to customer
- `label` - Address nickname (e.g., "Home", "Work")
- `full_name`, `phone`, address fields
- `is_default` - Mark as default address

**Order** - Completed orders
- `customer_id` - Reference to customer
- `order_number` - Unique order identifier
- `total_amount` - Order total
- `commission_amount` - Platform commission
- `status` - Order status (pending, processing, shipped, delivered)
- `payment_status` - Payment status (pending, completed)

**OrderItem** - Items in an order
- `order_id` - Reference to order
- `product_id`, `variant_id` - Product reference
- `quantity` - Quantity ordered
- `price_at_purchase` - Price at time of order
- `total_amount` - Item total
- `status` - Item delivery status

**VendorOrder** - Vendor-specific order tracking
- `order_id` - Reference to order
- `vendor_id` - Reference to vendor
- `total_amount` - Vendor's total
- `commission_amount` - Commission charged
- `vendor_receives` - Amount vendor gets
- `status` - Vendor order status

## API Endpoints

### Cart Management

**POST /cart/add** - Add product to cart
```json
{
  "product_id": 1,
  "variant_id": null,
  "quantity": 1
}
```

**GET /cart** - View cart
Returns cart with items grouped by vendor

**PUT /cart/items/{cartItem}** - Update quantity
```json
{
  "quantity": 2
}
```

**DELETE /cart/items/{cartItem}** - Remove item from cart

**POST /cart/clear** - Clear entire cart

**GET /cart/count** - Get total items in cart

**GET /cart/validate** - Validate cart before checkout
Returns any stock availability issues

**POST /cart/items/{cartItem}/move-to-wishlist** - Move item to wishlist

### Checkout Process

**GET /checkout** - View checkout page
Shows cart, address selection/form, order summary

**POST /checkout/address** - Save delivery address
```json
{
  "label": "Home",
  "full_name": "John Doe",
  "phone": "+1234567890",
  "address_line_1": "123 Main St",
  "city": "New York",
  "state_province": "NY",
  "postal_code": "10001",
  "country": "United States",
  "delivery_instructions": "Ring doorbell twice",
  "is_default": true
}
```

**POST /checkout/address/{address}/default** - Set address as default

**DELETE /checkout/address/{address}** - Delete address

**POST /checkout/process** - Process checkout and create order
```json
{
  "address_id": 1
}
```

### Order Management

**GET /orders** - View all customer orders

**GET /orders/confirmation/{order}** - View order confirmation

**GET /orders/{order}** - View detailed order information

## Views & Templates

### Cart Page (`resources/views/cart/index.blade.php`)

- Empty cart message with "Continue Shopping" link
- Items grouped by vendor with vendor information and rating
- Individual item cards with:
  - Product image
  - Product name and variant
  - Price and total
  - Quantity controls (increment/decrement)
  - Remove button
- Order summary sidebar showing:
  - Item count
  - Subtotal
  - Free shipping
  - Total price
  - Proceed to checkout button
- Clear cart option

### Checkout Page (`resources/views/checkout/index.blade.php`)

**Step 1: Delivery Address**
- List of saved addresses with radio selection
- Option to add new address
- Address form with validation
- Default address option

**Step 2: Order Review**
- Items grouped by vendor
- Vendor information and subtotals
- Commission breakdown (optional, can be hidden)

**Order Summary Sidebar**
- Item count and subtotal
- Shipping (free)
- Tax calculation
- Total amount
- Place order button

### Order Confirmation (`resources/views/orders/confirmation.blade.php`)

- Success message
- Order number (prominently displayed)
- Delivery address
- Items by vendor with quantities and prices
- Order summary with payment status
- What's next timeline
- Links to view detailed order or continue shopping

### My Orders (`resources/views/orders/index.blade.php`)

- List of all customer orders
- For each order:
  - Order number and date
  - Order status badge (pending, processing, shipped, delivered)
  - Payment status badge
  - Total amount
  - Items preview (first 3 items + count)
  - View details link
- Pagination support
- Empty state with "Start Shopping" link

### Order Details (`resources/views/orders/show.blade.php`)

- Order status and payment status
- Delivery address
- Items grouped by vendor with full details
- Quantity, price, and status for each item
- Order summary sidebar
- Order information (number, date, totals)
- Print order button
- Continue shopping link

## Components

### Add to Cart Component (`resources/views/components/add-to-cart.blade.php`)

Include this on product detail pages for easy cart management:

```blade
@include('components.add-to-cart', ['product' => $product])
```

Features:
- Variant selection dropdown (if applicable)
- Dynamic stock display based on variant
- Quantity selector with increment/decrement
- Add to cart button
- Add to wishlist button (for logged-in users)

## Controllers

### CartController (`app/Http/Controllers/CartController.php`)

Methods:
- `getCart()` - Get or create cart for current user/session
- `index()` - Display cart
- `add()` - Add item to cart
- `update()` - Update item quantity
- `remove()` - Remove item from cart
- `clear()` - Clear entire cart
- `count()` - Get cart item count
- `validateCart()` - Validate before checkout
- `moveToWishlist()` - Move item to wishlist
- `checkStock()` - Check product/variant availability

### CheckoutController (`app/Http/Controllers/CheckoutController.php`)

Methods:
- `index()` - Display checkout page
- `storeAddress()` - Save customer address
- `setDefaultAddress()` - Mark address as default
- `deleteAddress()` - Delete saved address
- `process()` - Process checkout and create order
- `deductInventory()` - Update inventory after order

### OrderController (`app/Http/Controllers/OrderController.php`)

Methods:
- `index()` - List customer orders
- `confirmation()` - Show order confirmation
- `show()` - Show order details

## Features

### Cart Persistence

- Database-backed cart for authenticated users
- Session-based cart for guests
- Auto-merging when guest logs in
- Persistent across browser sessions

### Stock Management

- Real-time inventory validation
- Prevents overselling
- Updates stock on order placement
- Inventory logs for tracking

### Multi-Vendor Support

- Items grouped by vendor
- Separate vendor orders created
- Commission calculation per vendor
- Vendor-specific order tracking

### Address Management

- Multiple saved addresses per customer
- Default address selection
- Address labels (Home, Work, etc.)
- Delivery instructions support
- Full address validation

### Order Processing

- Atomic transaction for order creation
- Automatic inventory deduction
- Vendor order creation
- Commission calculation
- Payment status tracking
- Order confirmation page
- Order history and tracking

## Installation & Setup

### 1. Run Migrations

The required tables are already defined in the migrations. Run:

```bash
php artisan migrate
```

### 2. Update Routes

Routes are already configured in `routes/web.php`:

```php
// Cart Routes
Route::get('cart', [CartController::class, 'index'])->name('cart.index');
Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
// ... other cart routes

// Checkout Routes (require authentication)
Route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function () {
    // ... checkout routes
});

// Order Routes (require authentication)
Route::middleware('auth')->prefix('orders')->name('order.')->group(function () {
    // ... order routes
});
```

### 3. Update Product Detail Page

Add the add-to-cart component to your product detail page:

```blade
@include('components.add-to-cart', ['product' => $product])
```

### 4. Update Navigation

Add cart links to your navigation:

```blade
<a href="{{ route('cart.index') }}" class="...">
    Cart <span class="badge">{{ \App\Models\Cart::getOrCreateForUser(auth()->id(), session()->getId())->getTotalItems() }}</span>
</a>

@if(auth()->check())
    <a href="{{ route('order.index') }}" class="...">My Orders</a>
@endif
```

## Usage Examples

### Adding to Cart (JavaScript)

```javascript
fetch('/cart/add', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').content
    },
    body: JSON.stringify({
        product_id: 1,
        variant_id: null,
        quantity: 1
    })
})
.then(r => r.json())
.then(data => {
    if (data.success) {
        alert('Added to cart!');
    }
});
```

### Getting Cart Count (JavaScript)

```javascript
fetch('/cart/count')
    .then(r => r.json())
    .then(data => {
        document.getElementById('cart-count').textContent = data.count;
    });
```

### Validating Cart Before Checkout (JavaScript)

```javascript
fetch('/cart/validate')
    .then(r => r.json())
    .then(data => {
        if (!data.valid) {
            console.log('Stock issues:', data.issues);
        }
    });
```

## Inventory Management

### Stock Checking

The system checks stock in this order:
1. If product has variants, check selected variant stock
2. If product has no variants, check product's total stock via `getTotalStock()`
3. Product's `getTotalStock()` method checks:
   - If product has variants: sum of variant stocks
   - Otherwise: sum of inventory table quantities

### Inventory Deduction

When an order is placed:
1. For each order item, stock is decremented
2. Inventory log entry created for audit trail
3. Deduction only happens after successful order creation
4. Uses database transactions for atomicity

## Security Considerations

- Cart access validated per user/session
- Address ownership verified before checkout
- Inventory locked during transaction
- Order creation atomic (all-or-nothing)
- Stock never oversold
- Commission calculations verified

## Testing

### Manual Testing Checklist

- [ ] Add item to cart (with/without variant)
- [ ] Update quantity (should validate stock)
- [ ] Remove item from cart
- [ ] View cart grouped by vendor
- [ ] Clear entire cart
- [ ] Add new address during checkout
- [ ] Select saved address
- [ ] Set address as default
- [ ] Place order (should show confirmation)
- [ ] View order history
- [ ] View order details
- [ ] Check inventory deducted after order

### Test Data

Products with variants should be in database for full testing. Use the AddProductImages command or seed data.

## Troubleshooting

### Cart Not Persisting

- Ensure user is authenticated
- Check session configuration
- Verify cart table has data

### Stock Not Validating

- Check product has stock quantity or variants with stock
- Verify ProductVariant.stock field has value
- Check getTotalStock() method returns correct value

### Order Not Creating

- Verify address belongs to user
- Check inventory before order
- Review transaction errors in logs
- Ensure commission_rate on categories

### Address Not Saving

- Verify customer_id is current user
- Check all required fields filled
- Review validation errors

## Performance Optimization

### Recommended Database Indexes

```sql
ALTER TABLE carts ADD INDEX idx_user_id (user_id);
ALTER TABLE carts ADD INDEX idx_session_id (session_id);
ALTER TABLE cart_items ADD INDEX idx_cart_id (cart_id);
ALTER TABLE cart_items ADD INDEX idx_product_id (product_id);
ALTER TABLE customer_addresses ADD INDEX idx_customer_id (customer_id);
ALTER TABLE orders ADD INDEX idx_customer_id (customer_id);
ALTER TABLE order_items ADD INDEX idx_order_id (order_id);
ALTER TABLE vendor_orders ADD INDEX idx_vendor_id (vendor_id);
```

### Caching Strategies

- Cache cart count in session
- Cache product stock availability
- Cache vendor information in cart grouping

## Future Enhancements

- [ ] Coupon/discount code support
- [ ] Tax calculation integration
- [ ] Shipping method selection
- [ ] Payment gateway integration
- [ ] Order status notifications
- [ ] Invoice generation
- [ ] Return/refund management
- [ ] Customer reviews on orders
- [ ] Recommendation engine based on cart

---

**System Status**: ✅ Fully Implemented and Ready for Testing
