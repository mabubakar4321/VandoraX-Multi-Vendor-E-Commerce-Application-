<?php

use App\Http\Controllers\Admin\CategoryCommissionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\VendorCategoryRequestController;
use App\Http\Controllers\Admin\VendorApprovalController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\VendorAuthController;
use App\Http\Controllers\Storefront\ProductController as StorefrontProductController;
use App\Http\Controllers\Vendor\CategoryImageController;
use App\Http\Controllers\Vendor\CategoryRequestController;
use App\Http\Controllers\Vendor\ProductBulkImportController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\ProductImageController;
use App\Http\Controllers\Vendor\ProductVariantController;
use App\Http\Controllers\Vendor\StockManagementController;
use App\Http\Controllers\Vendor\VendorRegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', [\App\Http\Controllers\Storefront\HomeController::class, 'index'])->name('home');
Route::get('products', [StorefrontProductController::class, 'index'])->name('storefront.products.index');
Route::get('products/{product}', [StorefrontProductController::class, 'show'])->name('storefront.products.show');

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
        Route::get('dashboard', fn () => view('admin.dashboard'))->name('dashboard');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

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
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN VENDOR MANAGEMENT  (/admin/vendors/...)
    |--------------------------------------------------------------------------
    */
    Route::prefix('vendors')->name('vendors.')->middleware(['auth', 'admin'])->group(function () {
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
        Route::get('dashboard', function () {
            $categories = \App\Models\Category::active()
                ->with('parent')
                ->ordered()
                ->get();

            return view('vendor.dashboard', compact('categories'));
        })->name('dashboard');

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
    });

    Route::middleware(['auth', 'vendor'])->prefix('categories')->name('categories.')->group(function () {
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