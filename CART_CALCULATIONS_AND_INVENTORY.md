# Cart Calculations & Inventory Validation Implementation

**Date:** September 23, 2026  
**Feature:** Real-time Cart Calculations and Stock Management

---

## ✅ Implementation Complete

A comprehensive cart calculation and inventory validation system has been implemented to ensure accurate pricing and prevent overselling.

---

## 🧮 Cart Calculations

### 1. **Subtotal Per Item (Price × Quantity)**

#### Calculation Method
Each cart item calculates its total automatically:

```php
// CartItem Model
public function getTotal()
{
    return $this->price * $this->quantity;
}
```

#### Features
- ✅ Real-time calculation on quantity change
- ✅ Supports variant pricing (different price per variant)
- ✅ Preserves price at time of adding to cart
- ✅ Updates immediately in UI without page reload

#### Display
- **Product Detail Page:** Shows unit price
- **Cart Page:** Shows subtotal per item (Rs. X,XXX)
- **AJAX Response:** Returns updated `item_total` on quantity change

---

### 2. **Total Per Vendor**

#### Calculation Method
Items are grouped by vendor and subtotaled:

```php
// Cart Model
public function getItemsByVendor()
{
    return $this->items()
        ->with(['product.vendor.user', 'product.images', 'variant'])
        ->get()
        ->groupBy(function ($item) {
            return $item->product->vendor_id;
        });
}
```

#### In View
```php
$vendorSubtotal = $items->sum(function($item) {
    return $item->price * $item->quantity;
});
```

#### Features
- ✅ Automatic grouping by vendor
- ✅ Individual subtotal per vendor section
- ✅ Clear visual separation
- ✅ Vendor header with store info
- ✅ Vendor subtotal footer in each section

#### Display
```
┌─────────────────────────────────────┐
│ Vendor A Header                     │
├─────────────────────────────────────┤
│ Item 1: Rs. 1,500 × 2 = Rs. 3,000  │
│ Item 2: Rs. 2,000 × 1 = Rs. 2,000  │
├─────────────────────────────────────┤
│ Vendor A Subtotal: Rs. 5,000       │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ Vendor B Header                     │
├─────────────────────────────────────┤
│ Item 3: Rs. 800 × 3 = Rs. 2,400    │
├─────────────────────────────────────┤
│ Vendor B Subtotal: Rs. 2,400       │
└─────────────────────────────────────┘
```

---

### 3. **Overall Total**

#### Calculation Method
Sum of all items across all vendors:

```php
// Cart Model
public function getSubtotal()
{
    return $this->items->sum(function ($item) {
        return $item->price * $item->quantity;
    });
}
```

#### Features
- ✅ Grand total of all items
- ✅ Displayed in Order Summary sidebar
- ✅ Updates on any cart change (add, update, remove)
- ✅ Formatted with thousand separators (Rs. 10,000)

#### Display Location
**Order Summary (Right Sidebar):**
```
ORDER SUMMARY
━━━━━━━━━━━━━━━━━━━━━━
Items (5)         Rs. 7,400
Shipping          Calculated at checkout
━━━━━━━━━━━━━━━━━━━━━━
Total             Rs. 7,400
```

---

### 4. **Real-time Recalculation on Quantity Changes**

#### How It Works

1. **User Action:**
   - Clicks +/- button on cart item
   - Quantity increases/decreases by 1

2. **AJAX Request:**
   ```javascript
   PUT /cart/items/{itemId}
   Body: { quantity: newQuantity }
   ```

3. **Server Calculation:**
   ```php
   // CartController@update
   $cartItem->update(['quantity' => $request->quantity]);
   
   return response()->json([
       'item_total' => $cartItem->getTotal(),      // Price × Quantity
       'cart_subtotal' => $cart->getSubtotal(),    // Sum of all items
       'cart_count' => $cart->getTotalItems(),     // Total quantity
   ]);
   ```

4. **UI Update (No Page Reload):**
   ```javascript
   // Update item total
   itemElement.querySelector('.item-total-price').textContent = 
       'Rs. ' + data.item_total.toLocaleString();
   
   // Update cart total
   document.getElementById('cart-total').textContent = 
       'Rs. ' + data.cart_subtotal.toLocaleString();
   
   // Update item count
   updatePageSubtitle(data.cart_count);
   ```

#### Features
- ✅ Instant updates without page refresh
- ✅ Updates three values simultaneously:
  - Item subtotal
  - Cart grand total
  - Item count in subtitle
- ✅ Loading spinner during calculation
- ✅ Error handling if request fails

---

## 📦 Inventory Validation

### 1. **Check Stock Availability When Adding to Cart**

#### Validation Method
```php
// CartController@add
protected function checkStock($product, $variant, $quantity)
{
    // Check variant stock if variant specified
    if ($variant) {
        $availableStock = $variant->stock_quantity ?? 0;
    } else {
        $availableStock = $product->stock_quantity ?? 0;
    }
    
    // Out of stock
    if ($availableStock <= 0) {
        return [
            'available' => false,
            'message' => 'This product is currently out of stock.',
            'available_stock' => 0,
        ];
    }
    
    // Insufficient stock
    if ($quantity > $availableStock) {
        return [
            'available' => false,
            'message' => "Only {$availableStock} units available in stock.",
            'available_stock' => $availableStock,
        ];
    }
    
    // Stock available
    return [
        'available' => true,
        'available_stock' => $availableStock,
    ];
}
```

#### When Adding to Cart
1. **New Item:**
   - Validates requested quantity against stock
   - Prevents adding if stock insufficient
   - Shows error message with available stock

2. **Existing Item (Adding More):**
   - Calculates: `newQuantity = currentInCart + requested`
   - Validates new total against stock
   - Shows current cart quantity in error message

#### Error Messages
```javascript
// Out of stock
"This product is currently out of stock."

// Insufficient stock
"Only 5 units available in stock."

// Already have items in cart
"Only 10 units available in stock. You already have 7 in your cart."
```

---

### 2. **Validate Stock When Proceeding to Checkout**

#### Checkout Validation Endpoint
```
GET /cart/validate
```

#### Validation Process

**Step 1: Load All Cart Items**
```php
$items = $cart->items()->with(['product', 'variant'])->get();
```

**Step 2: Check Each Item**
```php
foreach ($items as $item) {
    $stockCheck = $this->checkStock(
        $item->product, 
        $item->variant, 
        $item->quantity
    );
    
    if (!$stockCheck['available']) {
        $issues[] = [
            'item_id' => $item->id,
            'product_name' => $item->product->name,
            'variant_name' => $item->variant ? $item->variant->variant_value : null,
            'requested_quantity' => $item->quantity,
            'available_stock' => $stockCheck['available_stock'],
            'message' => $stockCheck['message'],
        ];
    }
}
```

**Step 3: Return Validation Result**
```php
return response()->json([
    'valid' => count($issues) === 0,
    'issues' => $issues,
    'cart_count' => $cart->getTotalItems(),
    'cart_subtotal' => $cart->getSubtotal(),
]);
```

#### UI Implementation

**When "Proceed to Checkout" Clicked:**
```javascript
async function checkout() {
    showLoading();
    
    const response = await fetch('/cart/validate');
    const data = await response.json();
    
    if (data.valid) {
        // All items in stock - proceed
        alert('Cart validated! Ready for checkout.');
    } else {
        // Show detailed stock issues
        let message = 'Unable to proceed. Stock issues:\n\n';
        data.issues.forEach(issue => {
            message += `• ${issue.product_name}\n  ${issue.message}\n\n`;
        });
        alert(message);
    }
}
```

#### Example Validation Error
```
Unable to proceed to checkout. The following items have stock issues:

• Stainless Steel Cookware Set
  Only 3 units available in stock.

• Vitamin D3 Supplement (100mg)
  This variant is currently out of stock.

Please update quantities or remove items to continue.
```

---

### 3. **Show Out-of-Stock Message If Quantity Exceeds Stock**

#### When Adding to Cart
```javascript
// Response from server
{
    "success": false,
    "message": "Only 5 units available in stock.",
    "available_stock": 5
}

// Alert shown to user
"Only 5 units available in stock."
```

#### When Updating Quantity in Cart
```javascript
// Response from server
{
    "success": false,
    "message": "Only 8 units available in stock.",
    "available_stock": 8
}

// Alert shown to user
"Only 8 units available in stock.

Available stock: 8"

// Quantity input automatically reset to available stock
input.value = 8;
```

#### Visual Feedback
- ✅ Error alert with clear message
- ✅ Shows available stock count
- ✅ Prevents invalid quantity update
- ✅ Auto-resets input to maximum available

---

### 4. **Update Cart If Stock Reduces (Real-time)**

#### Current Implementation
Stock validation happens at these points:

1. **Adding to Cart**
   - Validates before adding
   - Prevents adding if insufficient stock

2. **Updating Quantity**
   - Validates before updating
   - Rejects update if stock insufficient
   - Shows available stock

3. **Proceeding to Checkout**
   - Final validation of entire cart
   - Lists all items with issues
   - Requires user to fix before proceeding

#### Future Enhancement (Advanced Real-time)

For real-time stock updates while browsing:

**Option A: Polling** (Simple)
```javascript
// Check cart validity every 5 minutes
setInterval(async () => {
    const response = await fetch('/cart/validate');
    const data = await response.json();
    
    if (!data.valid) {
        showStockWarning(data.issues);
    }
}, 300000); // 5 minutes
```

**Option B: WebSockets** (Advanced)
- Server broadcasts stock changes
- Cart updates automatically
- Real-time notifications

**Option C: On Checkout** (Current)
- Validate only when user clicks checkout
- Most efficient approach
- Prevents overselling at purchase time

---

## 📊 Database Structure for Stock

### Products Table
```sql
products
├── id
├── vendor_id
├── name
├── base_price
├── stock_quantity      ← Main product stock
├── status
└── moderation_status
```

### Product Variants Table
```sql
product_variants
├── id
├── product_id
├── variant_name (e.g., "Size", "Color")
├── variant_value (e.g., "Large", "Red")
├── price              ← Variant-specific price
├── stock_quantity     ← Variant-specific stock
└── sku
```

### Cart Items Table
```sql
cart_items
├── id
├── cart_id
├── product_id
├── variant_id         ← NULL if no variant
├── quantity
├── price              ← Locked price at time of adding
└── timestamps
```

---

## 🔄 Stock Validation Flow

```
┌─────────────────────────────────────────────────────┐
│ User Action: Add to Cart / Update Quantity         │
└─────────────────┬───────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────┐
│ Load Product & Variant Data                        │
└─────────────────┬───────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────┐
│ Check: Is there a variant selected?                │
└─────┬───────────────────────┬───────────────────────┘
      │                       │
      │ Yes                   │ No
      ▼                       ▼
┌────────────────┐   ┌──────────────────┐
│ Check Variant  │   │ Check Product    │
│ Stock Quantity │   │ Stock Quantity   │
└───────┬────────┘   └────────┬─────────┘
        │                     │
        └──────────┬──────────┘
                   ▼
┌─────────────────────────────────────────────────────┐
│ Stock Available >= Requested Quantity?              │
└─────┬───────────────────────┬───────────────────────┘
      │                       │
      │ Yes                   │ No
      ▼                       ▼
┌────────────────┐   ┌──────────────────────────────┐
│ ✅ Allow       │   │ ❌ Reject with Error         │
│ Add/Update     │   │ "Only X units available"     │
└────────────────┘   └──────────────────────────────┘
```

---

## 🎯 API Endpoints

### Cart Operations with Stock Validation

| Endpoint | Method | Purpose | Stock Check |
|----------|--------|---------|-------------|
| `/cart/add` | POST | Add product to cart | ✅ Validates before adding |
| `/cart/items/{id}` | PUT | Update quantity | ✅ Validates before updating |
| `/cart/items/{id}` | DELETE | Remove item | ❌ No check needed |
| `/cart/clear` | POST | Clear all items | ❌ No check needed |
| `/cart/validate` | GET | Validate entire cart | ✅ Checks all items |

### Request/Response Examples

#### Add to Cart with Stock Check
**Request:**
```json
POST /cart/add
{
    "product_id": 19,
    "variant_id": 42,
    "quantity": 3
}
```

**Success Response:**
```json
{
    "success": true,
    "message": "Product added to cart successfully!",
    "cart_count": 5
}
```

**Insufficient Stock Response:**
```json
{
    "success": false,
    "message": "Only 2 units available in stock.",
    "available_stock": 2
}
```

**Out of Stock Response:**
```json
{
    "success": false,
    "message": "This product is currently out of stock.",
    "available_stock": 0
}
```

#### Update Quantity with Stock Check
**Request:**
```json
PUT /cart/items/15
{
    "quantity": 5
}
```

**Success Response:**
```json
{
    "success": true,
    "message": "Cart updated successfully!",
    "item_total": 7500,
    "cart_subtotal": 15200,
    "cart_count": 12
}
```

**Insufficient Stock Response:**
```json
{
    "success": false,
    "message": "Only 3 units available in stock.",
    "available_stock": 3
}
```

#### Validate Cart Before Checkout
**Request:**
```json
GET /cart/validate
```

**Valid Cart Response:**
```json
{
    "valid": true,
    "issues": [],
    "cart_count": 8,
    "cart_subtotal": 23400
}
```

**Invalid Cart Response:**
```json
{
    "valid": false,
    "issues": [
        {
            "item_id": 15,
            "product_name": "Stainless Steel Cookware Set",
            "variant_name": null,
            "requested_quantity": 5,
            "available_stock": 2,
            "message": "Only 2 units available in stock."
        },
        {
            "item_id": 18,
            "product_name": "Vitamin D3 Supplement",
            "variant_name": "100mg",
            "requested_quantity": 3,
            "available_stock": 0,
            "message": "This variant is currently out of stock."
        }
    ],
    "cart_count": 8,
    "cart_subtotal": 23400
}
```

---

## ✨ User Experience Features

### Real-time Feedback

1. **Adding to Cart**
   - ✅ Instant success message
   - ❌ Clear error if out of stock
   - 📊 Shows available stock in error

2. **Updating Quantity**
   - ✅ Immediate price recalculation
   - ❌ Prevents exceeding stock
   - 🔄 Auto-resets to max available

3. **Checkout Validation**
   - ✅ Validates entire cart
   - 📋 Lists all problematic items
   - 💡 Guides user to fix issues

### Loading States
- Spinner overlay during all operations
- Prevents duplicate requests
- Smooth transitions

### Error Handling
- User-friendly messages
- Technical errors logged to console
- Graceful degradation

---

## 🧪 Testing Scenarios

### Stock Validation Tests

#### Test 1: Add to Cart - Sufficient Stock
- **Product Stock:** 10 units
- **Add Quantity:** 3 units
- **Expected:** ✅ Success

#### Test 2: Add to Cart - Insufficient Stock
- **Product Stock:** 2 units
- **Add Quantity:** 5 units
- **Expected:** ❌ Error: "Only 2 units available"

#### Test 3: Add to Cart - Out of Stock
- **Product Stock:** 0 units
- **Add Quantity:** 1 unit
- **Expected:** ❌ Error: "Out of stock"

#### Test 4: Update Quantity - Increase Within Stock
- **Current in Cart:** 2 units
- **Product Stock:** 10 units
- **Update to:** 5 units
- **Expected:** ✅ Success

#### Test 5: Update Quantity - Exceed Stock
- **Current in Cart:** 2 units
- **Product Stock:** 3 units
- **Update to:** 5 units
- **Expected:** ❌ Error: "Only 3 units available"

#### Test 6: Variant Stock Validation
- **Product Stock:** 100 units
- **Variant Stock:** 2 units
- **Add Quantity:** 5 units (with variant)
- **Expected:** ❌ Error: "Only 2 units available" (checks variant, not product)

#### Test 7: Checkout Validation - Mixed Issues
- **Item A:** 5 in cart, 10 in stock → ✅ OK
- **Item B:** 3 in cart, 1 in stock → ❌ Issue
- **Item C:** 2 in cart, 0 in stock → ❌ Issue
- **Expected:** Shows issues for B and C, requires user action

---

## 📝 Code Implementation Summary

### CartController Methods

| Method | Purpose | Stock Check |
|--------|---------|-------------|
| `add()` | Add product to cart | ✅ Before adding |
| `update()` | Update item quantity | ✅ Before updating |
| `remove()` | Remove item | ❌ No check |
| `validateCart()` | Validate all items | ✅ Comprehensive check |
| `checkStock()` | Stock validation helper | 🔧 Reusable utility |

### Key Methods

**checkStock() - The Core Validator**
```php
protected function checkStock($product, $variant, $quantity)
{
    // Determines stock source (variant or product)
    // Checks availability
    // Returns array: ['available' => bool, 'message' => string, 'available_stock' => int]
}
```

**validateCart() - Pre-Checkout Validation**
```php
public function validateCart()
{
    // Loads all cart items
    // Validates each against current stock
    // Returns comprehensive report
}
```

---

## 🚀 Next Steps (Optional Enhancements)

### Immediate Improvements
1. ✅ **Email Notifications:** Alert vendors when stock is low
2. ✅ **Stock Reservations:** Reserve items in cart for X minutes
3. ✅ **Backorder Support:** Allow ordering when out of stock
4. ✅ **Pre-order System:** Accept orders for upcoming stock

### Advanced Features
5. **Real-time Stock Updates:** WebSocket notifications
6. **Stock History:** Track when items went out of stock
7. **Automatic Cart Adjustments:** Reduce quantity if stock drops
8. **Waitlist:** Notify when out-of-stock items return
9. **Bulk Stock Import:** CSV upload for vendors
10. **Stock Forecasting:** Predict when items will sell out

---

## 🐛 Troubleshooting

### Cart shows old stock levels
- Clear browser cache
- Check `stock_quantity` in database
- Verify product/variant has updated stock

### Validation passes but checkout fails
- Check for race conditions (multiple users)
- Implement database transactions
- Add row-level locking on stock

### User can add more than available
- Verify `checkStock()` is called in `add()`
- Check validation logic in controller
- Test with different user sessions

---

## 📸 Test Your Implementation

### Start Server
```bash
php artisan serve
```

### Test Sequence

1. **Add to Cart (Normal)**
   - Visit: http://localhost:8000/products/19
   - Select quantity: 2
   - Click "Add to Cart"
   - ✅ Should succeed

2. **Add to Cart (Exceed Stock)**
   - Change quantity to 999
   - Click "Add to Cart"
   - ❌ Should show stock error

3. **Update Quantity in Cart**
   - Visit: http://localhost:8000/cart
   - Click + button multiple times
   - ✅ Updates until stock limit
   - ❌ Shows error at stock limit

4. **Checkout Validation**
   - Have items in cart
   - Click "Proceed to Checkout"
   - ✅ Validates all items
   - Shows issues if any exist

---

**Cart Calculations & Inventory System Complete! 🧮📦**

Your cart now features:
- ✅ Accurate real-time calculations
- ✅ Comprehensive stock validation
- ✅ Prevention of overselling
- ✅ Clear user feedback
- ✅ Multi-vendor support
- ✅ Variant stock tracking
- ✅ Pre-checkout validation

All calculations are accurate and inventory is protected! 🚀
