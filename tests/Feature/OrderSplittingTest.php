<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorOrder;
use App\Notifications\NewVendorOrderNotification;
use App\Notifications\OrderConfirmationNotification;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OrderSplittingTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $vendorUser1;
    private User $vendorUser2;
    private Vendor $vendor1;
    private Vendor $vendor2;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics-test',
            'commission_rate' => 15.00,
            'is_active' => true,
            'parent_id' => null,
        ]);

        $this->customer = User::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'phone_number' => '03001234567',
            'is_active' => true,
            'status' => 'active',
        ]);

        $this->vendorUser1 = User::create([
            'name' => 'Vendor One',
            'email' => 'vendor1@test.com',
            'password' => bcrypt('password'),
            'phone_number' => '03009990001',
            'is_active' => true,
            'status' => 'active',
        ]);

        $this->vendorUser2 = User::create([
            'name' => 'Vendor Two',
            'email' => 'vendor2@test.com',
            'password' => bcrypt('password'),
            'phone_number' => '03009990002',
            'is_active' => true,
            'status' => 'active',
        ]);

        $this->vendor1 = Vendor::create([
            'user_id' => $this->vendorUser1->id,
            'store_name' => 'Store One',
            'store_slug' => 'store-one-test',
            'category_id' => $this->category->id,
            'commission_rate' => 15.00,
            'status' => 'active',
            'is_verified' => true,
        ]);

        $this->vendor2 = Vendor::create([
            'user_id' => $this->vendorUser2->id,
            'store_name' => 'Store Two',
            'store_slug' => 'store-two-test',
            'category_id' => $this->category->id,
            'commission_rate' => 15.00,
            'status' => 'active',
            'is_verified' => true,
        ]);
    }

    private function makeProduct(int $vendorId, string $name, float $price = 100.00): Product
    {
        $product = Product::create([
            'vendor_id' => $vendorId,
            'category_id' => $this->category->id,
            'name' => $name,
            'slug' => \Str::slug($name) . '-' . time(),
            'base_price' => $price,
            'sku' => 'TEST-' . $name,
            'status' => 'published',
            'moderation_status' => 'approved',
            'is_visible' => true,
            'delivery_time_days' => 3,
        ]);

        ProductInventory::create([
            'product_id' => $product->id,
            'quantity' => 100,
            'reserved_quantity' => 0,
            'low_stock_threshold' => 5,
        ]);

        return $product;
    }

    private function makeCartWithItems(User $user, array $items): Cart
    {
        $cart = Cart::create([
            'user_id' => $user->id,
            'session_id' => 'test-session-' . $user->id,
            'last_activity' => now(),
        ]);

        foreach ($items as $item) {
            $cart->items()->create([
                'product_id' => $item['product']->id,
                'variant_id' => $item['variant_id'] ?? null,
                'quantity' => $item['quantity'],
                'price' => $item['price'] ?? $item['product']->base_price,
            ]);
        }

        return $cart;
    }

    public function test_order_is_split_into_vendor_sub_orders(): void
    {
        $product1 = $this->makeProduct($this->vendor1->id, 'Laptop', 1500.00);
        $product2 = $this->makeProduct($this->vendor2->id, 'Mouse', 50.00);

        $cart = $this->makeCartWithItems($this->customer, [
            ['product' => $product1, 'quantity' => 1],
            ['product' => $product2, 'quantity' => 2],
        ]);

        $address = CustomerAddress::create([
            'customer_id' => $this->customer->id,
            'full_name' => 'Test Customer',
            'phone' => '03001234567',
            'address_line_1' => '123 Test St',
            'city' => 'Lahore',
            'state_province' => 'Punjab',
            'postal_code' => '54000',
            'country' => 'Pakistan',
            'is_default' => true,
        ]);

        Notification::fake();
        $service = app(OrderService::class);
        $order = $service->createOrderFromCart($cart, $address, 'cod');

        // Verify main order
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(2, $order->vendor_count);
        $this->assertNotEmpty($order->order_number);
        $this->assertEquals(1600.00, (float) $order->total_amount);

        // Verify sub-orders created
        $this->assertDatabaseCount('vendor_orders', 2);
        $this->assertDatabaseCount('order_items', 3);

        $vendorOrders = $order->vendorOrders()->get();
        $this->assertCount(2, $vendorOrders);

        // Each sub-order should have correct vendor
        $vo1 = $vendorOrders->firstWhere('vendor_id', $this->vendor1->id);
        $vo2 = $vendorOrders->firstWhere('vendor_id', $this->vendor2->id);

        $this->assertNotNull($vo1);
        $this->assertNotNull($vo2);
        $this->assertEquals(1500.00, (float) $vo1->subtotal);
        $this->assertEquals(100.00, (float) $vo2->subtotal);

        // Verify sub-order numbers follow ORD-YYYY-MM-XXXX-A, -B format
        $this->assertStringContainsString($order->order_number . '-A', $vo1->vendor_order_number);
        $this->assertStringContainsString($order->order_number . '-B', $vo2->vendor_order_number);

        // All sub-orders should be pending
        $this->assertTrue($vendorOrders->every(fn ($vo) => $vo->status === 'pending'));

        // Verify commission calculated (15% on Electronics)
        $this->assertEquals(225.00, (float) $vo1->commission_amount);
        $this->assertEquals(15.00, (float) $vo2->commission_amount);
        $this->assertEquals(1350.00, (float) $vo1->vendor_amount);
        $this->assertEquals(85.00, (float) $vo2->vendor_amount);
    }

    public function test_order_items_are_linked_to_correct_vendor_order(): void
    {
        $product1 = $this->makeProduct($this->vendor1->id, 'Item V1');
        $product2 = $this->makeProduct($this->vendor2->id, 'Item V2');

        $cart = $this->makeCartWithItems($this->customer, [
            ['product' => $product1, 'quantity' => 1],
            ['product' => $product2, 'quantity' => 1],
        ]);

        $address = CustomerAddress::create([
            'customer_id' => $this->customer->id,
            'full_name' => 'Test',
            'phone' => '0300',
            'address_line_1' => 'Addr',
            'city' => 'City',
            'state_province' => 'Prov',
            'postal_code' => '1',
            'country' => 'Pakistan',
        ]);

        $order = app(OrderService::class)->createOrderFromCart($cart, $address, 'cod');
        $order->refresh();

        $items = $order->items()->get();
        $this->assertCount(2, $items);

        foreach ($items as $item) {
            $this->assertNotNull($item->vendor_order_id, 'Each item must be linked to a vendor_order_id');

            $vendorOrder = $item->vendorOrder;
            $this->assertNotNull($vendorOrder);
            $this->assertEquals($item->product->vendor_id, $vendorOrder->vendor_id);
        }
    }

    public function test_stock_is_reduced_when_order_is_created(): void
    {
        $product = $this->makeProduct($this->vendor1->id, 'StockedItem', 100.00);
        $initialStock = ProductInventory::where('product_id', $product->id)->first()->quantity;

        $cart = $this->makeCartWithItems($this->customer, [
            ['product' => $product, 'quantity' => 5],
        ]);

        $address = CustomerAddress::create([
            'customer_id' => $this->customer->id,
            'full_name' => 'Test',
            'phone' => '0300',
            'address_line_1' => 'Addr',
            'city' => 'City',
            'state_province' => 'Prov',
            'postal_code' => '1',
            'country' => 'Pakistan',
        ]);

        $order = app(OrderService::class)->createOrderFromCart($cart, $address, 'cod');

        $finalStock = ProductInventory::where('product_id', $product->id)->first()->quantity;
        $this->assertEquals($initialStock - 5, $finalStock);
    }

    public function test_insufficient_stock_prevents_order_creation(): void
    {
        $product = $this->makeProduct($this->vendor1->id, 'OutOfStock', 100.00);
        ProductInventory::where('product_id', $product->id)->update(['quantity' => 0]);

        $cart = $this->makeCartWithItems($this->customer, [
            ['product' => $product, 'quantity' => 1],
        ]);

        $address = CustomerAddress::create([
            'customer_id' => $this->customer->id,
            'full_name' => 'Test',
            'phone' => '0300',
            'address_line_1' => 'Addr',
            'city' => 'City',
            'state_province' => 'Prov',
            'postal_code' => '1',
            'country' => 'Pakistan',
        ]);

        try {
            app(OrderService::class)->createOrderFromCart($cart, $address, 'cod');
            $this->fail('Expected RuntimeException for insufficient stock.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Insufficient stock', $e->getMessage());
        }

        // No order should have been created
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('vendor_orders', 0);
    }

    public function test_vendor_notification_sent_on_order_creation(): void
    {
        $product1 = $this->makeProduct($this->vendor1->id, 'V1 Item');
        $product2 = $this->makeProduct($this->vendor2->id, 'V2 Item');

        $cart = $this->makeCartWithItems($this->customer, [
            ['product' => $product1, 'quantity' => 1],
            ['product' => $product2, 'quantity' => 1],
        ]);

        $address = CustomerAddress::create([
            'customer_id' => $this->customer->id,
            'full_name' => 'Test',
            'phone' => '0300',
            'address_line_1' => 'Addr',
            'city' => 'City',
            'state_province' => 'Prov',
            'postal_code' => '1',
            'country' => 'Pakistan',
        ]);

        Notification::fake();

        $order = app(OrderService::class)->createOrderFromCart($cart, $address, 'cod');

        // Verify each vendor got a notification
        Notification::assertSentTo($this->vendorUser1, NewVendorOrderNotification::class, function ($notification) use ($order) {
            return $notification->vendorOrder->order_id === $order->id
                && $notification->vendorOrder->vendor_id === $this->vendor1->id;
        });

        Notification::assertSentTo($this->vendorUser2, NewVendorOrderNotification::class, function ($notification) use ($order) {
            return $notification->vendorOrder->order_id === $order->id
                && $notification->vendorOrder->vendor_id === $this->vendor2->id;
        });

        // Customer got confirmation
        Notification::assertSentTo($this->customer, OrderConfirmationNotification::class);
    }

    public function test_order_status_history_records_creation(): void
    {
        $product = $this->makeProduct($this->vendor1->id, 'History Test');

        $cart = $this->makeCartWithItems($this->customer, [
            ['product' => $product, 'quantity' => 1],
        ]);

        $address = CustomerAddress::create([
            'customer_id' => $this->customer->id,
            'full_name' => 'Test',
            'phone' => '0300',
            'address_line_1' => 'Addr',
            'city' => 'City',
            'state_province' => 'Prov',
            'postal_code' => '1',
            'country' => 'Pakistan',
        ]);

        $order = app(OrderService::class)->createOrderFromCart($cart, $address, 'cod');

        $history = OrderStatusHistory::where('order_id', $order->id)->get();
        $this->assertTrue($history->isNotEmpty(), 'Order status history should have at least one entry');

        $creationEntry = $history->firstWhere('new_status', 'created');
        $this->assertNotNull($creationEntry);
        $this->assertEquals($order->id, $creationEntry->order_id);
    }

    public function test_cart_is_cleared_after_order_creation(): void
    {
        $product = $this->makeProduct($this->vendor1->id, 'Cart Clear Test');

        $cart = $this->makeCartWithItems($this->customer, [
            ['product' => $product, 'quantity' => 1],
        ]);

        $address = CustomerAddress::create([
            'customer_id' => $this->customer->id,
            'full_name' => 'Test',
            'phone' => '0300',
            'address_line_1' => 'Addr',
            'city' => 'City',
            'state_province' => 'Prov',
            'postal_code' => '1',
            'country' => 'Pakistan',
        ]);

        $order = app(OrderService::class)->createOrderFromCart($cart, $address, 'cod');

        $this->assertEquals(0, $cart->items()->count());
    }

    public function test_single_vendor_order_creates_one_sub_order(): void
    {
        $product1 = $this->makeProduct($this->vendor1->id, 'V1 A');
        $product2 = $this->makeProduct($this->vendor1->id, 'V1 B');

        $cart = $this->makeCartWithItems($this->customer, [
            ['product' => $product1, 'quantity' => 2],
            ['product' => $product2, 'quantity' => 1],
        ]);

        $address = CustomerAddress::create([
            'customer_id' => $this->customer->id,
            'full_name' => 'Test',
            'phone' => '0300',
            'address_line_1' => 'Addr',
            'city' => 'City',
            'state_province' => 'Prov',
            'postal_code' => '1',
            'country' => 'Pakistan',
        ]);

        $order = app(OrderService::class)->createOrderFromCart($cart, $address, 'cod');

        $this->assertEquals(1, $order->vendor_count);
        $this->assertCount(1, $order->vendorOrders()->get());
    }
}
