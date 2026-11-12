<?php

use App\Http\Controllers\Admin\CategoryCommissionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\VendorManagementController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\VendorCategoryRequestController;
use App\Http\Controllers\Admin\VendorApprovalController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\VendorAuthController;
use App\Http\Controllers\Storefront\ProductController as StorefrontProductController;
use App\Http\Controllers\Storefront\CollectionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Vendor\CategoryImageController;
use App\Http\Controllers\Vendor\CategoryRequestController;
use App\Http\Controllers\Vendor\ProductBulkImportController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\ProductImageController;
use App\Http\Controllers\Vendor\ProductVariantController;
use App\Http\Controllers\Vendor\StockManagementController;
use App\Http\Controllers\Vendor\VendorOrderController;
use App\Http\Controllers\Vendor\VendorRegistrationController;
use App\Http\Controllers\Storefront\ReviewController as StorefrontReviewController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\UnblockRequestController as AdminUnblockRequestController;
use App\Http\Controllers\Vendor\UnblockRequestController as VendorUnblockRequestController;
use App\Http\Controllers\Vendor\ReviewResponseController;
use App\Http\Controllers\Vendor\ReviewController as VendorReviewController;
use App\Http\Controllers\Admin\ReviewDashboardController;
use App\Http\Controllers\Admin\ReviewDeletionRequestController as AdminReviewDeletionRequestController;
use App\Http\Controllers\Vendor\ReviewDeletionRequestController as VendorReviewDeletionRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', [\App\Http\Controllers\Storefront\HomeController::class, 'index'])->name('home');
Route::get('products', [StorefrontProductController::class, 'index'])->name('storefront.products.index');
Route::get('products/{product}', [StorefrontProductController::class, 'show'])->name('storefront.products.show');

// Collection Routes
Route::get('collections/new-arrivals', [CollectionController::class, 'newArrivals'])->name('collections.new-arrivals');
Route::get('collections/bestsellers', [CollectionController::class, 'bestsellers'])->name('collections.bestsellers');
Route::get('collections/summer', [CollectionController::class, 'summer'])->name('collections.summer');
Route::get('collections/category/{category}', [CollectionController::class, 'category'])->name('collections.category');

// Cart Routes
Route::get('cart', [CartController::class, 'index'])->name('cart.index');
Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
Route::put('cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('cart/items/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('cart/count', [CartController::class, 'count'])->name('cart.count');
Route::get('cart/validate', [CartController::class, 'validateCart'])->name('cart.validate');
Route::post('cart/items/{cartItem}/move-to-wishlist', [CartController::class, 'moveToWishlist'])->name('cart.moveToWishlist');

// Cart Sidebar Routes (AJAX)
Route::prefix('cart-sidebar')->name('cart-sidebar.')->group(function () {
    Route::get('items', [\App\Http\Controllers\CartSidebarController::class, 'items'])->name('items');
    Route::get('summary', [\App\Http\Controllers\CartSidebarController::class, 'summary'])->name('summary');
});

// Wishlist Routes (require authentication)
Route::post('wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add'); // Before auth middleware

Route::middleware('auth')->group(function () {
    Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::delete('wishlist/{wishlist}', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('wishlist/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');
    Route::get('wishlist/count', [WishlistController::class, 'count'])->name('wishlist.count');
});

// Checkout Routes (require authentication)
Route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('index');
    Route::post('address', [\App\Http\Controllers\CheckoutController::class, 'storeAddress'])->name('address.store');
    Route::post('address/{address}/default', [\App\Http\Controllers\CheckoutController::class, 'setDefaultAddress'])->name('address.default');
    Route::delete('address/{address}', [\App\Http\Controllers\CheckoutController::class, 'deleteAddress'])->name('address.delete');
    Route::post('process', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('process');
});

// Payment Routes (require authentication)
Route::middleware('auth')->prefix('payment')->name('payment.')->group(function () {
    Route::get('history', [\App\Http\Controllers\PaymentHistoryController::class, 'index'])->name('history');
    Route::post('jazzcash/initiate', [\App\Http\Controllers\PaymentController::class, 'initiateJazzCashPayment'])->name('jazzcash.initiate');
    Route::post('bank-transfer/create', [\App\Http\Controllers\PaymentController::class, 'initiateBankTransfer'])->name('bank-transfer.create');
    Route::post('bank-transfer/verify', [\App\Http\Controllers\PaymentController::class, 'verifyBankTransfer'])->name('bank-transfer.verify');
    Route::get('bank-details', [\App\Http\Controllers\PaymentController::class, 'getBankDetails'])->name('bank-details');
    Route::post('validate-method', [\App\Http\Controllers\PaymentController::class, 'validatePaymentMethod'])->name('validate-method');
    // Stripe card payment endpoints
    Route::post('stripe/create-intent', [\App\Http\Controllers\StripePaymentController::class, 'createIntent'])->name('stripe.create-intent');
    Route::post('stripe/retry', [\App\Http\Controllers\StripePaymentController::class, 'retry'])->name('stripe.retry');
    Route::post('stripe/confirm', [\App\Http\Controllers\StripePaymentController::class, 'confirm'])->name('stripe.confirm');
    Route::get('stripe/status/{payment}', [\App\Http\Controllers\StripePaymentController::class, 'status'])->name('stripe.status');
});

Route::middleware('auth')->group(function () {
    Route::get('orders/{order}/invoice', [\App\Http\Controllers\InvoiceController::class, 'view'])->name('invoice.view');
    Route::get('orders/{order}/invoice/download', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('invoice.download');
});

// Payment Webhooks (public, no authentication needed)
Route::post('webhooks/jazzcash', [\App\Http\Controllers\PaymentController::class, 'handleJazzCashCallback'])->name('webhook.jazzcash');
Route::post('webhooks/stripe', \App\Http\Controllers\StripeWebhookController::class)->name('webhook.stripe');

// Order Routes (require authentication)
Route::middleware('auth')->prefix('orders')->name('order.')->group(function () {
    Route::get('/', [\App\Http\Controllers\OrderController::class, 'index'])->name('index');
    Route::get('confirmation/{order}', [\App\Http\Controllers\OrderController::class, 'confirmation'])->name('confirmation');
    Route::get('{order}/edit', [\App\Http\Controllers\OrderController::class, 'edit'])->name('edit');
    Route::put('{order}', [\App\Http\Controllers\OrderController::class, 'update'])->name('update');
    Route::delete('{order}', [\App\Http\Controllers\OrderController::class, 'destroy'])->name('destroy');
    Route::post('{order}/cancel', [\App\Http\Controllers\OrderController::class, 'cancel'])->name('cancel');
    Route::post('{order}/items/cancel', [\App\Http\Controllers\OrderController::class, 'cancelItems'])->name('items.cancel');
    Route::get('{order}', [\App\Http\Controllers\OrderController::class, 'show'])->name('show');
});

Route::middleware(['auth', 'role:customer'])->prefix('returns')->name('returns.')->group(function () {
    Route::get('', [\App\Http\Controllers\ReturnController::class, 'index'])->name('index');
    Route::get('{order}/items/{item}/create', [\App\Http\Controllers\ReturnController::class, 'create'])->name('create');
    Route::post('{order}/items/{item}', [\App\Http\Controllers\ReturnController::class, 'store'])->name('store');
    Route::get('{return}', [\App\Http\Controllers\ReturnController::class, 'show'])->name('show');
    Route::post('{return}/ship-back', [\App\Http\Controllers\ReturnController::class, 'shipBack'])->name('ship-back');
    Route::post('{return}/appeal', [\App\Http\Controllers\ReturnController::class, 'appeal'])->name('appeal');
});

Route::middleware(['auth', 'role:customer'])->prefix('customer/complaints')->name('customer.complaints.')->group(function () {
    Route::get('', [\App\Http\Controllers\CustomerComplaintController::class, 'index'])->name('index');
    Route::get('create', [\App\Http\Controllers\CustomerComplaintController::class, 'create'])->name('create');
    Route::post('', [\App\Http\Controllers\CustomerComplaintController::class, 'store'])->name('store');
});

/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION
|--------------------------------------------------------------------------
*/
Route::get('email/verify', [EmailVerificationController::class, 'notice'])
    ->middleware('guest')
    ->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');
Route::post('email/verification-notification', [EmailVerificationController::class, 'resend'])
    ->middleware(['guest', 'throttle:6,1'])
    ->name('verification.send');

/*
|--------------------------------------------------------------------------
| ADMIN  (/admin/...)
|--------------------------------------------------------------------------
| Admins are created by seeder/super-admin only — no public register route.
*/
Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])
             ->middleware('throttle:10,1');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('', [AdminOrderController::class, 'index'])->name('index');
            Route::get('{order}', [AdminOrderController::class, 'show'])->name('show');
            Route::patch('{order}/status', [AdminOrderController::class, 'updateStatus'])->name('status');
            Route::post('{order}/cancel', [AdminOrderController::class, 'cancel'])->name('cancel');
            Route::post('{order}/return-stock', [AdminOrderController::class, 'returnStock'])->name('return-stock');
            Route::delete('{order}', [AdminOrderController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('', [AdminCustomerController::class, 'index'])->name('index');
            Route::get('{customer}', [AdminCustomerController::class, 'show'])->name('show');
            Route::put('{customer}', [AdminCustomerController::class, 'update'])->name('update');
            Route::delete('{customer}', [AdminCustomerController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('products')->name('products.')->group(function () {
            Route::get('', [AdminProductController::class, 'index'])->name('index');
            Route::get('{product}', [AdminProductController::class, 'show'])->name('show');
            Route::post('{product}/approve', [AdminProductController::class, 'approve'])->name('approve');
            Route::post('{product}/reject', [AdminProductController::class, 'reject'])->name('reject');
            Route::post('{product}/flag', [AdminProductController::class, 'flag'])->name('flag');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('', [CategoryController::class, 'index'])->name('index');
            Route::get('create', [CategoryController::class, 'create'])->name('create');
            Route::post('', [CategoryController::class, 'store'])->name('store');
            Route::get('tree', [CategoryController::class, 'tree'])->name('tree');
            Route::get('{category}', [CategoryController::class, 'edit'])->name('edit');
            Route::put('{category}', [CategoryController::class, 'update'])->name('update');
            Route::delete('{category}', [CategoryController::class, 'destroy'])->name('destroy');
            Route::post('{category}/deactivate', [CategoryController::class, 'deactivate'])->name('deactivate');
            Route::post('{category}/activate', [CategoryController::class, 'activate'])->name('activate');
            Route::get('{category}/subcategories', [CategoryController::class, 'getSubcategories'])->name('subcategories');
        });

        Route::prefix('categories')->name('commission.')->group(function () {
            Route::put('{category}/commission', [CategoryCommissionController::class, 'update'])->name('update');
            Route::get('{category}/commission/history', [CategoryCommissionController::class, 'history'])->name('history');
            Route::get('{category}/commission/rate', [CategoryCommissionController::class, 'getCurrentRate'])->name('current-rate');
        });

        Route::prefix('category-requests')->name('category-requests.')->group(function () {
            Route::get('', [VendorCategoryRequestController::class, 'index'])->name('index');
            Route::get('{request}', [VendorCategoryRequestController::class, 'show'])->name('show');
            Route::post('{categoryRequest}/approve', [VendorCategoryRequestController::class, 'approve'])->name('approve');
            Route::post('{categoryRequest}/reject', [VendorCategoryRequestController::class, 'reject'])->name('reject');
            Route::post('bulk/approve', [VendorCategoryRequestController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('bulk/reject', [VendorCategoryRequestController::class, 'bulkReject'])->name('bulk-reject');
        });

        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('index');
            Route::get('export', [\App\Http\Controllers\Admin\PaymentController::class, 'export'])->name('export');
            Route::get('{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('show');
            Route::post('{payment}/refund', [\App\Http\Controllers\Admin\PaymentController::class, 'refund'])->name('refund');
            Route::post('{payment}/release-escrow', [\App\Http\Controllers\Admin\PaymentController::class, 'releaseEscrow'])->name('release-escrow');
        });

        Route::prefix('returns')->name('returns.')->group(function () {
            Route::get('', [\App\Http\Controllers\Admin\ReturnController::class, 'index'])->name('index');
            Route::get('{return}', [\App\Http\Controllers\Admin\ReturnController::class, 'show'])->name('show');
            Route::post('{return}/force-refund', [\App\Http\Controllers\Admin\ReturnController::class, 'forceRefund'])->name('force-refund');
            Route::post('{return}/uphold', [\App\Http\Controllers\Admin\ReturnController::class, 'uphold'])->name('uphold');
            Route::post('{return}/request-info', [\App\Http\Controllers\Admin\ReturnController::class, 'requestInfo'])->name('request-info');
        });

        Route::prefix('refund-requests')->name('refund-requests.')->group(function () {
            Route::get('', [\App\Http\Controllers\Admin\RefundRequestController::class, 'index'])->name('index');
            Route::get('{refundAppeal}', [\App\Http\Controllers\Admin\RefundRequestController::class, 'show'])->name('show');
        });

        Route::prefix('vendor-complaints')->name('vendor-complaints.')->group(function () {
            Route::get('', [\App\Http\Controllers\Admin\VendorComplaintController::class, 'index'])->name('index');
            Route::get('{vendorComplaint}', [\App\Http\Controllers\Admin\VendorComplaintController::class, 'show'])->name('show');
        });

        Route::prefix('customer-complaints')->name('customer-complaints.')->group(function () {
            Route::get('', [\App\Http\Controllers\Admin\CustomerComplaintController::class, 'index'])->name('index');
            Route::get('{customerComplaint}', [\App\Http\Controllers\Admin\CustomerComplaintController::class, 'show'])->name('show');
        });

        Route::get('admin-collections', [\App\Http\Controllers\Admin\AdminCollectionsController::class, 'index'])->name('admin-collections.index');

        Route::prefix('payouts')->name('payouts.')->group(function () {
            Route::get('', [\App\Http\Controllers\Admin\PayoutController::class, 'index'])->name('index');
            Route::get('export', [\App\Http\Controllers\Admin\PayoutController::class, 'export'])->name('export');
            Route::post('process', [\App\Http\Controllers\Admin\PayoutController::class, 'process'])->name('process');
            Route::post('{payout}/retry', [\App\Http\Controllers\Admin\PayoutController::class, 'retry'])->name('retry');
            Route::get('withdrawals', [\App\Http\Controllers\Admin\PayoutController::class, 'withdrawals'])->name('withdrawals.index');
            Route::post('withdrawals/{withdrawal}/approve', [\App\Http\Controllers\Admin\PayoutController::class, 'approveWithdrawal'])->name('withdrawals.approve');
            Route::post('withdrawals/{withdrawal}/reject', [\App\Http\Controllers\Admin\PayoutController::class, 'rejectWithdrawal'])->name('withdrawals.reject');
        });

        Route::prefix('commission-reports')->name('commission.')->group(function () {
            Route::get('', [\App\Http\Controllers\Admin\CommissionReportController::class, 'index'])->name('index');
            Route::get('export', [\App\Http\Controllers\Admin\CommissionReportController::class, 'export'])->name('export');
            Route::post('{commission}/adjust', [\App\Http\Controllers\Admin\CommissionReportController::class, 'adjust'])->name('adjust');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN VENDOR MANAGEMENT  (/admin/vendors/...)
    |--------------------------------------------------------------------------
    */
    Route::prefix('vendors')->name('vendors.')->middleware(['auth', 'admin'])->group(function () {
        Route::get('', [VendorManagementController::class, 'index'])->name('index');
        Route::get('{vendor}/manage', [VendorManagementController::class, 'show'])->name('manage');
        Route::put('{vendor}/manage', [VendorManagementController::class, 'update'])->name('update');
        Route::delete('{vendor}/manage', [VendorManagementController::class, 'destroy'])->name('destroy');
        Route::post('{vendor}/documents', [VendorManagementController::class, 'uploadDocument'])->name('documents.upload');
        Route::put('{vendor}/documents/{document}', [VendorManagementController::class, 'replaceDocument'])->name('documents.replace');
        Route::delete('{vendor}/documents/{document}', [VendorManagementController::class, 'deleteDocument'])->name('documents.delete');

        // Pending vendors
        Route::get('pending', [VendorApprovalController::class, 'pendingVendors'])->name('pending');

        // Vendor details
        Route::get('{vendor}/detail', [VendorApprovalController::class, 'showVendor'])->name('detail');

        // Approve/Reject
        Route::post('{vendor}/approve', [VendorApprovalController::class, 'approve'])->name('approve');
        Route::post('{vendor}/reject', [VendorApprovalController::class, 'reject'])->name('reject');

        // Document management
        Route::post('document/{document}/approve', [VendorApprovalController::class, 'approveDocument'])->name('document.approve');
        Route::post('document/{document}/reject', [VendorApprovalController::class, 'rejectDocument'])->name('document.reject');
        Route::get('document/{document}/view', [VendorApprovalController::class, 'viewDocument'])->name('document.view');

        // Statistics
        Route::get('statistics', [VendorApprovalController::class, 'statistics'])->name('statistics');
    });
});

/*
|--------------------------------------------------------------------------
| VENDOR  (/vendor/...)
|--------------------------------------------------------------------------
*/
Route::prefix('vendor')->name('vendor.')->group(function () {

    // --- Guest: registration wizard + login ---
    Route::middleware('guest')->group(function () {
        Route::get('register',        [VendorRegistrationController::class, 'showStep1Form'])->name('register');
        Route::get('register/step1',  [VendorRegistrationController::class, 'showStep1Form'])->name('register.step1');
        Route::post('register/step1', [VendorRegistrationController::class, 'submitStep1']);

        Route::get('register/step2',  [VendorRegistrationController::class, 'showStep2Form'])->name('register.step2');
        Route::post('register/step2', [VendorRegistrationController::class, 'submitStep2'])->name('register.step2.store');

        Route::get('register/step3',  [VendorRegistrationController::class, 'showStep3Form'])->name('register.step3');
        Route::post('register/step3', [VendorRegistrationController::class, 'submitStep3'])->name('register.step3.store');

        // Completion
        Route::get('register/complete', [VendorRegistrationController::class, 'complete'])->name('register.complete');

        // Resend verification email
        Route::post('register/resend-verification', [VendorRegistrationController::class, 'resendVerificationEmail'])->name('register.resend-verification');

        // Check progress (AJAX) - accessible without auth
        Route::get('register/progress', [VendorRegistrationController::class, 'checkProgress'])->name('register.progress');

        Route::get('login',  [VendorAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [VendorAuthController::class, 'login'])->middleware('throttle:10,1');
    });

    // --- Logged-in vendor, ANY status (pending/rejected/active) ---
    Route::middleware('auth')->group(function () {
        Route::get('pending', [VendorAuthController::class, 'pending'])->name('pending');
    });

    Route::middleware(['auth', 'role:vendor'])->group(function () {
        Route::post('logout', [VendorAuthController::class, 'logout'])->name('logout');
    });

    // --- Logged-in AND approved vendor only ---
    Route::middleware(['auth', 'vendor', 'verified'])->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Vendor\DashboardController::class, 'index'])->name('dashboard');

        // Products Management
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('', [ProductController::class, 'index'])->name('index');
            Route::get('create', [ProductController::class, 'create'])->name('create');
            Route::post('', [ProductController::class, 'store'])->name('store');
            Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');
            Route::post('{product}/publish', [ProductController::class, 'publish'])->name('publish');
            Route::post('{product}/unpublish', [ProductController::class, 'unpublish'])->name('unpublish');

            // Bulk Import
            Route::get('bulk-import', [ProductBulkImportController::class, 'create'])->name('bulk-import');
            Route::post('bulk-import', [ProductBulkImportController::class, 'store'])->name('bulk-import.store');
            Route::get('import-template', [ProductBulkImportController::class, 'template'])->name('import-template');
            Route::get('{product}', [ProductController::class, 'show'])->name('show');
        });

        // Orders Management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('', [VendorOrderController::class, 'index'])->name('index');
            Route::get('{vendorOrder}/print', [VendorOrderController::class, 'print'])->name('print');
            Route::get('{vendorOrder}', [VendorOrderController::class, 'show'])->name('show');
            Route::patch('{vendorOrder}/status', [VendorOrderController::class, 'updateStatus'])->name('status');
            Route::patch('{vendorOrder}/payment/verify', [VendorOrderController::class, 'verifyPayment'])->name('payment.verify');
        });

        Route::prefix('returns')->name('returns.')->group(function () {
            Route::get('', [\App\Http\Controllers\Vendor\ReturnController::class, 'index'])->name('index');
            Route::get('{return}', [\App\Http\Controllers\Vendor\ReturnController::class, 'show'])->name('show');
            Route::post('{return}/approve', [\App\Http\Controllers\Vendor\ReturnController::class, 'approve'])->name('approve');
            Route::post('{return}/reject', [\App\Http\Controllers\Vendor\ReturnController::class, 'reject'])->name('reject');
            Route::post('{return}/receive', [\App\Http\Controllers\Vendor\ReturnController::class, 'receive'])->name('receive');
            Route::post('{return}/refund', [\App\Http\Controllers\Vendor\ReturnController::class, 'refund'])->name('refund');
        });

        Route::prefix('refund-appeals')->name('refund-appeals.')->group(function () {
            Route::get('', [\App\Http\Controllers\Vendor\RefundAppealController::class, 'index'])->name('index');
            Route::get('create', [\App\Http\Controllers\Vendor\RefundAppealController::class, 'create'])->name('create');
            Route::post('', [\App\Http\Controllers\Vendor\RefundAppealController::class, 'store'])->name('store');
        });

        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('', [\App\Http\Controllers\Vendor\ComplaintController::class, 'index'])->name('index');
            Route::get('create', [\App\Http\Controllers\Vendor\ComplaintController::class, 'create'])->name('create');
            Route::post('', [\App\Http\Controllers\Vendor\ComplaintController::class, 'store'])->name('store');
        });

        // Product Variants
        Route::prefix('products/{product}/variants')->name('variants.')->group(function () {
            Route::post('', [ProductVariantController::class, 'store'])->name('store');
            Route::put('{variant}', [ProductVariantController::class, 'update'])->name('update');
            Route::delete('{variant}', [ProductVariantController::class, 'destroy'])->name('destroy');
        });

        // Product Images
        Route::prefix('products/{product}/images')->name('images.')->group(function () {
            Route::post('', [ProductImageController::class, 'upload'])->name('upload');
            Route::delete('{image}', [ProductImageController::class, 'delete'])->name('delete');
            Route::post('reorder', [ProductImageController::class, 'reorder'])->name('reorder');
        });

        // Stock Management
        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('dashboard', [StockManagementController::class, 'dashboard'])->name('dashboard');
            Route::get('{product}', [StockManagementController::class, 'show'])->name('show');
            Route::post('{product}/adjust', [StockManagementController::class, 'adjust'])->name('adjust');
            Route::post('{product}/set-quantity', [StockManagementController::class, 'setQuantity'])->name('set-quantity');
            Route::post('{product}/set-threshold', [StockManagementController::class, 'setThreshold'])->name('set-threshold');
            Route::get('{product}/history', [StockManagementController::class, 'history'])->name('history');
            Route::get('{product}/status', [StockManagementController::class, 'getStatus'])->name('status');
            Route::post('bulk-adjust', [StockManagementController::class, 'bulkAdjust'])->name('bulk-adjust');
        });

        // Payments — vendor sees payouts after admin releases amount (deducted commission)
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('', [\App\Http\Controllers\Vendor\PaymentController::class, 'index'])->name('index');
            Route::get('{payout}', [\App\Http\Controllers\Vendor\PaymentController::class, 'show'])->name('show');
            Route::delete('{payout}', [\App\Http\Controllers\Vendor\PaymentController::class, 'destroy'])->name('destroy');
            // On-demand withdrawal requests
            Route::post('withdraw', [\App\Http\Controllers\Vendor\PaymentController::class, 'requestWithdrawal'])->name('withdraw.request');
            Route::get('withdrawals', [\App\Http\Controllers\Vendor\PaymentController::class, 'withdrawals'])->name('withdrawals');
        });
    });

    Route::middleware(['auth', 'vendor'])->prefix('categories')->name('categories.')->group(function () {
        Route::get('my', [\App\Http\Controllers\Vendor\VendorCategoryController::class, 'index'])->name('my');
        Route::get('my/{category}/edit', [\App\Http\Controllers\Vendor\VendorCategoryController::class, 'edit'])->name('edit');
        Route::put('my/{category}', [\App\Http\Controllers\Vendor\VendorCategoryController::class, 'update'])->name('update');
        Route::get('all', [CategoryRequestController::class, 'all'])->name('all');
        Route::get('requests', [CategoryRequestController::class, 'index'])->name('requests');
        Route::get('request/create', [CategoryRequestController::class, 'create'])->name('request.create');
        Route::post('request', [CategoryRequestController::class, 'store'])->name('request.store');
        Route::get('request/{request}', [CategoryRequestController::class, 'show'])->name('request.show');
        Route::get('{category}/subcategories', [\App\Http\Controllers\Admin\CategoryController::class, 'getSubcategories'])->name('subcategories');

        // Category Image Upload (only for approved categories)
        Route::get('request/{categoryRequest}/image', [CategoryImageController::class, 'edit'])->name('image.edit');
        Route::post('request/{categoryRequest}/image', [CategoryImageController::class, 'store'])->name('image.store');
        Route::delete('request/{categoryRequest}/image', [CategoryImageController::class, 'destroy'])->name('image.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| CUSTOMER  (/customer/...)
|--------------------------------------------------------------------------
*/
Route::prefix('customer')->name('customer.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('register',  [CustomerAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [CustomerAuthController::class, 'register'])->middleware('throttle:register');

        Route::get('login',  [CustomerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [CustomerAuthController::class, 'login'])->middleware('throttle:login');
    });

    Route::middleware(['auth', 'customer'])->group(function () {
        Route::post('logout', [CustomerAuthController::class, 'logout'])->name('logout');

        Route::middleware('verified')->group(function () {
            Route::get('dashboard', fn () => view('customer.dashboard'))->name('dashboard');
        });
    });
});

/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION  (shared by all roles)
|--------------------------------------------------------------------------
| 'signed' validates the HMAC + expiry that VerifyEmailNotification generated.
| No 'auth' here on purpose: users often open the email on another device.
*/
Route::get('email/verify', [EmailVerificationController::class, 'notice'])
     ->name('verification.notice');

Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
     ->middleware(['signed', 'throttle:6,1'])
     ->name('verification.verify');

Route::post('email/verification-notification', [EmailVerificationController::class, 'resend'])
     ->middleware('throttle:3,1')
     ->name('verification.send');

/*
|--------------------------------------------------------------------------
| STOREFRONT REVIEWS  (public + customer)
|--------------------------------------------------------------------------
*/
Route::prefix('products/{product}/reviews')->name('storefront.reviews.')->group(function () {
    Route::get('/create', [StorefrontReviewController::class, 'create'])->middleware(['auth', 'customer'])->name('create');
    Route::post('/', [StorefrontReviewController::class, 'store'])->middleware(['auth', 'customer'])->name('store');
    Route::get('/{review}/edit', [StorefrontReviewController::class, 'edit'])->middleware(['auth', 'customer'])->name('edit');
    Route::put('/{review}', [StorefrontReviewController::class, 'update'])->middleware(['auth', 'customer'])->name('update');
    Route::delete('/{review}', [StorefrontReviewController::class, 'destroy'])->middleware(['auth', 'customer'])->name('destroy');
    Route::post('/{review}/vote', [StorefrontReviewController::class, 'vote'])->middleware(['auth', 'customer'])->name('vote');
});

/*
|--------------------------------------------------------------------------
| ADMIN REVIEWS & UNBLOCK REQUESTS
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('reviews', AdminReviewController::class)->only(['index', 'show', 'destroy']);
    Route::post('reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
    Route::post('reviews/{review}/flag', [AdminReviewController::class, 'flag'])->name('reviews.flag');

    Route::get('review-deletion-requests', [AdminReviewDeletionRequestController::class, 'index'])->name('review-deletion-requests.index');
    Route::get('review-deletion-requests/{reviewDeletionRequest}', [AdminReviewDeletionRequestController::class, 'show'])->name('review-deletion-requests.show');
    Route::post('review-deletion-requests/{reviewDeletionRequest}/approve', [AdminReviewDeletionRequestController::class, 'approve'])->name('review-deletion-requests.approve');
    Route::post('review-deletion-requests/{reviewDeletionRequest}/reject', [AdminReviewDeletionRequestController::class, 'reject'])->name('review-deletion-requests.reject');

    Route::resource('unblock-requests', AdminUnblockRequestController::class)->only(['index', 'show']);
    Route::post('unblock-requests/{unblockRequest}/approve', [AdminUnblockRequestController::class, 'approve'])->name('unblock-requests.approve');
    Route::post('unblock-requests/{unblockRequest}/reject', [AdminUnblockRequestController::class, 'reject'])->name('unblock-requests.reject');

    Route::get('reviews-dashboard', [ReviewDashboardController::class, 'index'])->name('reviews-dashboard');
});

/*
|--------------------------------------------------------------------------
| VENDOR UNBLOCK & REVIEW RESPONSES
|--------------------------------------------------------------------------
*/
Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'vendor'])->group(function () {
    Route::resource('unblock', VendorUnblockRequestController::class)->only(['index', 'create', 'store', 'show']);

    // Vendor-facing review dashboard
    Route::get('reviews', [VendorReviewController::class, 'index'])->name('reviews.index');
    Route::get('review-deletion-requests', [VendorReviewDeletionRequestController::class, 'index'])->name('reviews.deletion-requests.index');
    Route::get('reviews/{review}', [VendorReviewController::class, 'show'])->name('reviews.show');
    Route::get('reviews/{review}/request-deletion', [VendorReviewDeletionRequestController::class, 'create'])->name('reviews.deletion-requests.create');
    Route::post('reviews/{review}/request-deletion', [VendorReviewDeletionRequestController::class, 'store'])->name('reviews.deletion-requests.store');

    Route::post('reviews/{review}/respond', [ReviewResponseController::class, 'store'])->name('reviews.respond');
    Route::put('reviews/{review}/respond', [ReviewResponseController::class, 'update'])->name('reviews.respond.update');
    Route::delete('reviews/{review}/respond', [ReviewResponseController::class, 'destroy'])->name('reviews.respond.destroy');
});

/*
|--------------------------------------------------------------------------
| PASSWORD RESET  (shared by all roles)
|--------------------------------------------------------------------------
| Route NAMES must stay exactly these — Laravel's password broker and our
| ResetPasswordNotification both reference 'password.reset'.
*/
Route::middleware('guest')->group(function () {
    Route::get('forgot-password',  [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink'])
         ->middleware('throttle:5,1')->name('password.email');

    Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password',        [PasswordResetController::class, 'resetPassword'])->name('password.update');
});
