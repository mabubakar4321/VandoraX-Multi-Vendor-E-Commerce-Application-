<?php
/**
 * VandoraX — End-to-End Review & Vendor-QC Test
 *
 * Run:  php tests/e2e_review_test.php
 *
 * What it does (each step prints PASS/FAIL):
 *   1. Migrations are current (nothing pending).
 *   2. Fresh isolated data is created (vendor, product, delivered order).
 *   3. Customer CAN review a delivered product.
 *   4. 1-star review is created + bad-review tracked.
 *   5. Customer cannot re-review the same product.
 *   6. Helpful vote + vendor response recorded.
 *   7. Product & vendor ratings recalculate correctly.
 *   8. Driving bad reviews to the threshold AUTO-BLOCKS the vendor.
 *   9. Vendor files an unblock request (pending).
 *  10. Admin approves -> vendor active, bad_reviews_count reset,
 *      VendorBadReview rows cleared.
 *  11. Admin rejects a second request -> admin_response stored.
 *  12. Admin deletes a review -> ratings recompute.
 *  13. Cleanup restores the database to its prior state.
 *
 * Exits 0 if every check passes, 1 otherwise.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->handle(new Symfony\Component\Console\Input\ArrayInput([]), new Symfony\Component\Console\Output\NullOutput());

use App\Services\ReviewService;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Review;
use App\Models\VendorBadReview;
use App\Models\VendorUnblockRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReviewResponse;
use App\Models\ReviewHelpfulVote;
use App\Models\VendorBlock;

$service = new ReviewService();
$failures = 0;
$passed   = 0;

function check(string $label, bool $ok): void
{
    global $failures, $passed;
    $line = ($ok ? "  [PASS] " : "  [FAIL] ") . $label;
    echo $line . "\n";
    $ok ? $passed++ : $failures++;
}

echo "=============================================================\n";
echo " VandoraX E2E — Reviews & Vendor Quality Control\n";
echo "=============================================================\n\n";

/* ----------------------------------------------------------------
 * STEP 1 — Migrations are current
 * ---------------------------------------------------------------- */
echo "STEP 1: Migrations current\n";
$output = shell_exec('cd ' . escapeshellarg(dirname(__DIR__)) . ' && php artisan migrate --pretend 2>&1');
$pending = (bool) preg_match('/Migrating/i', $output ?? '');
check('No pending migrations', !$pending);
echo "\n";

/* ----------------------------------------------------------------
 * STEP 2 — Create isolated test data
 * ---------------------------------------------------------------- */
echo "STEP 2: Create isolated test data\n";
$categoryId = \App\Models\Category::first()?->id ?? 1;
$admin    = User::role('admin')->first();
$customer = User::role('customer')->first();
$vendorUser = User::role('vendor')->first();

if (!$admin || !$customer || !$vendorUser) {
    echo "  [FAIL] Could not find admin/customer/vendor users in seed data.\n\n";
    echo "Aborting — seed the database first (php artisan db:seed).\n";
    exit(1);
}

$testVendor = Vendor::create([
    'user_id' => $vendorUser->id,
    'store_name' => 'E2E Vendor ' . uniqid(),
    'store_slug' => 'e2e-' . uniqid(),
    'category_id' => $categoryId,
    'commission_rate' => 10,
    'total_sales' => 0, 'total_orders' => 0, 'average_rating' => 0,
    'total_followers' => 0, 'status' => 'active', 'is_verified' => true,
    'bad_reviews_count' => 0, 'requested_unblock' => false,
]);

$testProduct = Product::create([
    'vendor_id' => $testVendor->id, 'category_id' => $categoryId,
    'name' => 'E2E Product ' . uniqid(), 'slug' => 'e2e-p-' . uniqid(),
    'description' => 'e2e test product', 'base_price' => 100,
    'sku' => 'E2E-' . uniqid(), 'status' => 'published',
    'moderation_status' => 'approved', 'is_visible' => true,
    'average_rating' => 0, 'reviews_count' => 0,
]);

$testOrder = Order::create([
    'order_number' => 'E2E-' . uniqid(), 'customer_id' => $customer->id,
    'total_amount' => 100, 'commission_amount' => 10, 'vendor_count' => 1,
    'status' => 'delivered', 'payment_status' => 'completed',
]);

$testOrderItem = OrderItem::create([
    'order_id' => $testOrder->id, 'product_id' => $testProduct->id,
    'quantity' => 1, 'price_at_purchase' => 100, 'total_amount' => 100,
    'status' => 'delivered',
]);

$vendor = $testVendor;
$product = $testProduct;
$order = $testOrder;

check("Created vendor #{$vendor->id}, product #{$product->id}, order #{$order->id}",
    $vendor->id > 0 && $product->id > 0 && $order->id > 0);
echo "\n";

/* ----------------------------------------------------------------
 * STEP 3 — Customer CAN review a delivered product
 * ---------------------------------------------------------------- */
echo "STEP 3: Customer eligible to review\n";
check('Customer with delivered order can review',
    $service->canCustomerReview($customer->id, $product->id));
echo "\n";

/* ----------------------------------------------------------------
 * STEP 4 — Create a 1-star (bad) review
 * ---------------------------------------------------------------- */
echo "STEP 4: Bad (1-star) review\n";
$review = $service->createReview([
    'product_id' => $product->id, 'order_id' => $order->id,
    'vendor_id' => $vendor->id, 'customer_id' => $customer->id,
    'product_rating' => 1, 'vendor_rating' => 1,
    'review_title' => 'Terrible',
    'review_text' => 'strawberry avocado kiwi mango lemon very bad',
    'is_verified_purchase' => true, 'status' => 'approved',
]);
check('Review row created', $review->id > 0);
check('Bad review tracked in vendor_bad_reviews',
    VendorBadReview::where('vendor_id', $vendor->id)->where('review_id', $review->id)->exists());
check('Vendor bad_reviews_count = 1', (int) $vendor->fresh()->bad_reviews_count === 1);
check('Vendor NOT blocked (1 < threshold)', !$vendor->fresh()->isBlocked());
echo "\n";

/* ----------------------------------------------------------------
 * STEP 5 — One review per customer per product
 * ---------------------------------------------------------------- */
echo "STEP 5: No duplicate review\n";
check('Customer can no longer re-review same product',
    !$service->canCustomerReview($customer->id, $product->id));
echo "\n";

/* ----------------------------------------------------------------
 * STEP 6 — Helpful vote + vendor response
 * ---------------------------------------------------------------- */
echo "STEP 6: Helpful vote + vendor response\n";
ReviewHelpfulVote::create(['review_id' => $review->id, 'user_id' => $customer->id, 'vote_type' => 'helpful']);
$review->increment('is_helpful_count');
check('Helpful vote recorded (count = 1)', (int) $review->fresh()->is_helpful_count === 1);

ReviewResponse::create(['review_id' => $review->id, 'vendor_id' => $vendor->id, 'response_text' => 'We apologize and will improve.']);
check('Vendor response saved & linked', $review->fresh()->hasResponded());
echo "\n";

/* ----------------------------------------------------------------
 * STEP 7 — Rating recalculation (product + vendor)
 * ---------------------------------------------------------------- */
echo "STEP 7: Rating recalculation\n";
$service->recalculateProductRating($product->id);
$service->recalculateVendorRating($vendor->id);
check('Product average_rating > 0', $product->fresh()->average_rating > 0);
check('Product reviews_count >= 1', $product->fresh()->reviews_count >= 1);
check('Vendor average_rating > 0', $vendor->fresh()->average_rating > 0);
$bd = $service->getProductRatingBreakdown($product->id);
check('Rating breakdown has 1-star bucket', $bd[1] >= 1 && isset($bd[5]));
echo "\n";

/* ----------------------------------------------------------------
 * STEP 8 — Drive to threshold -> AUTO-BLOCK
 * ---------------------------------------------------------------- */
echo "STEP 8: Auto-block at threshold\n";
$threshold = config('vandorax.bad_review_threshold', 10);
echo "  (threshold = $threshold)\n";
$current = (int) $vendor->fresh()->bad_reviews_count;
for ($i = $current; $i < $threshold; $i++) {
    $service->createReview([
        'product_id' => $product->id, 'order_id' => $order->id,
        'vendor_id' => $vendor->id, 'customer_id' => $customer->id,
        'product_rating' => 2, 'vendor_rating' => 2,
        'review_text' => 'strawberry avocado kiwi mango pear plum bad review',
        'is_verified_purchase' => true, 'status' => 'approved',
    ]);
}
check("bad_reviews_count reached {$threshold}",
    (int) $vendor->fresh()->bad_reviews_count >= $threshold);
check('Vendor AUTO-BLOCKED', $vendor->fresh()->isBlocked());
check('vendor_blocks row (auto_bad_reviews) created',
    VendorBlock::where('vendor_id', $vendor->id)->where('block_type', 'auto_bad_reviews')->exists());
echo "\n";

/* ----------------------------------------------------------------
 * STEP 9 — Vendor files unblock request
 * ---------------------------------------------------------------- */
echo "STEP 9: Unblock request submitted\n";
$ur = VendorUnblockRequest::create([
    'vendor_id' => $vendor->id,
    'reason' => "Improvements:\nWe fixed quality control.\n\nReason to unblock:\nWe now inspect every order.",
    'status' => 'pending',
]);
$vendor->update(['requested_unblock' => true]);
check('Unblock request created & pending', $ur->fresh()->isPending());
echo "\n";

/* ----------------------------------------------------------------
 * STEP 10 — Admin approves -> unblock + reset
 * ---------------------------------------------------------------- */
echo "STEP 10: Admin approves unblock request\n";
$ur->approve($admin->id);
check('Vendor status restored to active', $vendor->fresh()->isActive());
check('bad_reviews_count reset to 0', (int) $vendor->fresh()->bad_reviews_count === 0);
check('Unblock request marked approved', $ur->fresh()->status === 'approved');
check('All vendor_bad_reviews cleared',
    !VendorBadReview::where('vendor_id', $vendor->id)->exists());
echo "\n";

/* ----------------------------------------------------------------
 * STEP 11 — Admin rejects a second request
 * ---------------------------------------------------------------- */
echo "STEP 11: Admin rejects unblock request\n";
$vendor->update(['status' => 'blocked', 'bad_reviews_count' => 3]);
$ur2 = VendorUnblockRequest::create(['vendor_id' => $vendor->id, 'reason' => 'test reject', 'status' => 'pending']);
$ur2->reject($admin->id, 'Not yet improved');
check('Reject sets status=rejected + admin_response',
    $ur2->fresh()->status === 'rejected' && $ur2->fresh()->admin_response === 'Not yet improved');
echo "\n";

/* ----------------------------------------------------------------
 * STEP 12 — Admin deletes a review -> recompute
 * ---------------------------------------------------------------- */
echo "STEP 12: Admin deletes review (ratings recompute)\n";
$before = (int) $product->fresh()->reviews_count;
$service->deleteReview($review->fresh());
$after = (int) $product->fresh()->reviews_count;
check("Deleting review lowers reviews_count ({$before} -> {$after})", $after < $before);
echo "\n";

/* ----------------------------------------------------------------
 * STEP 13 — Cleanup
 * ---------------------------------------------------------------- */
echo "STEP 13: Cleanup\n";
VendorUnblockRequest::where('vendor_id', $vendor->id)->delete();
VendorBlock::where('vendor_id', $vendor->id)->delete();
VendorBadReview::where('vendor_id', $vendor->id)->delete();
ReviewResponse::where('vendor_id', $vendor->id)->delete();
ReviewHelpfulVote::whereIn('review_id', Review::where('vendor_id', $vendor->id)->pluck('id'))->delete();
Review::where('vendor_id', $vendor->id)->delete();
OrderItem::where('order_id', $order->id)->delete();
$order->delete();
$product->delete();
$vendor->delete();
check('Test data removed',
    !Vendor::where('id', $vendor->id)->exists()
    && !Product::where('id', $product->id)->exists()
    && !Order::where('id', $order->id)->exists());
echo "\n";

/* ----------------------------------------------------------------
 * SUMMARY
 * ---------------------------------------------------------------- */
echo "=============================================================\n";
echo " RESULT: {$passed} passed, {$failures} failed\n";
echo "=============================================================\n";

exit($failures === 0 ? 0 : 1);
