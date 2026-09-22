<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorLoginRequest;
use App\Http\Requests\VendorRegisterStep1Request;
use App\Http\Requests\VendorRegisterStep2Request;
use App\Http\Requests\VendorRegisterStep3Request;
use App\Models\Category;
use App\Services\VendorAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VendorAuthController extends Controller
{
    /** Session keys that carry registration state between the 3 steps */
    private const REG_KEY  = 'vendor_registration_user_id';
    private const BANK_KEY = 'vendor_registration_bank';

    public function __construct(protected VendorAuthService $authService) {}

    /* =============================== STEP 1 =============================== */

    public function showStep1Form(): View
    {
        return view('auth.vendor.register-step1');
    }

    public function registerStep1(VendorRegisterStep1Request $request): RedirectResponse
    {
        $result = $this->authService->registerVendorStep1($request->validated());

        if (! $result['success']) {
            return back()->withInput()->withErrors(['message' => $result['message']]);
        }

        // Remember who is registering, so steps 2 and 3 know the user
        session([self::REG_KEY => $result['user']->id]);

        return redirect()
            ->route('vendor.register.step2')
            ->with('success', $result['message']);
    }

    /* =============================== STEP 2 =============================== */

    public function showStep2Form(): View|RedirectResponse
    {
        if (! session(self::REG_KEY)) {
            return redirect()->route('vendor.register.step1')
                ->withErrors(['message' => 'Please start from step 1.']);
        }

        return view('auth.vendor.register-step2');
    }

    public function registerStep2(VendorRegisterStep2Request $request): RedirectResponse
    {
        $userId = session(self::REG_KEY);

        if (! $userId) {
            return redirect()->route('vendor.register.step1');
        }

        $result = $this->authService->registerVendorStep2($userId, $request->validated());

        if (! $result['success']) {
            return back()->withInput()->withErrors(['message' => $result['message']]);
        }

        // Park the bank data until step 3 creates the vendor row it belongs to.
        // Requires SESSION_ENCRYPT=true — see 3.4.2.
        session([self::BANK_KEY => $result['bank_data']]);

        $redirect = redirect()->route('vendor.register.step3')->with('success', $result['message']);

        // Gentle nudge rather than a hard block
        if (empty($result['email_verified'])) {
            $redirect->with('warning', 'Reminder: verify your email before you can log in.');
        }

        return $redirect;
    }

    /* =============================== STEP 3 =============================== */

    public function showStep3Form(): View|RedirectResponse
    {
        if (! session(self::REG_KEY)) {
            return redirect()->route('vendor.register.step1');
        }

        if (! session(self::BANK_KEY)) {
            return redirect()->route('vendor.register.step2')
                ->withErrors(['message' => 'Please enter your bank details first.']);
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('auth.vendor.register-step3', compact('categories'));
    }

    public function registerStep3(VendorRegisterStep3Request $request): RedirectResponse
    {
        $userId = session(self::REG_KEY);

        if (! $userId) {
            return redirect()->route('vendor.register.step1');
        }

        $bankData = session(self::BANK_KEY, []);

        if (empty($bankData)) {
            return redirect()->route('vendor.register.step2')
                ->withErrors(['message' => 'Please enter your bank details first.']);
        }

        $data = $request->validated();
        $data['documents'] = $this->storeDocuments($request, $userId);

        $result = $this->authService->registerVendorStep3($userId, $data, $bankData);

        if (! $result['success']) {
            return back()->withInput()->withErrors(['message' => $result['message']]);
        }

        // Clear registration state — the account number must not linger in the session
        session()->forget([self::REG_KEY, self::BANK_KEY]);

        return redirect()->route('vendor.login')->with('success', $result['message']);
    }

    /**
     * Save uploaded documents to the PRIVATE disk.
     *
     * storage/app/private/documents/{userId}/... is NOT web accessible.
     * In Phase 4 the admin views them through a controller that checks the
     * admin role and streams the file with Storage::download().
     */
    private function storeDocuments(Request $request, $userId): array
    {
        $documents = [];

        $fields = [
            'cnic_front'      => 'cnic_front',
            'cnic_back'       => 'cnic_back',
            'tax_certificate' => 'tax_certificate',
            'bank_proof'      => 'bank_proof',
        ];

        foreach ($fields as $field => $type) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $file = $request->file($field);

            $path = $file->storeAs(
                "documents/{$userId}",
                $type . '_' . Str::random(20) . '.' . $file->getClientOriginalExtension(),
                'local'          // storage/app/private on Laravel 11+, storage/app on 10
            );

            $documents[] = [
                'type' => $type,
                'path' => $path,
                'name' => $file->getClientOriginalName(),
            ];
        }

        return $documents;
    }

    /* =============================== LOGIN =============================== */

    public function showLoginForm(): View
    {
        return view('auth.vendor.login');
    }

    public function login(VendorLoginRequest $request): RedirectResponse
    {
        $key = 'vendor-login:' . Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withInput($request->only('email'))->withErrors([
                'message' => 'Too many attempts. Try again in ' . RateLimiter::availableIn($key) . ' seconds.',
            ]);
        }

        $result = $this->authService->loginVendor(
            $request->email,
            $request->password,
            $request->boolean('remember')
        );

        if (! $result['success']) {
            RateLimiter::hit($key, 60);

            // Unverified email → offer a resend link on the login page
            if (($result['code'] ?? null) === 'unverified') {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['message' => $result['message']])
                    ->with('show_resend_for', $request->email);
            }

            return back()->withInput($request->only('email'))->withErrors(['message' => $result['message']]);
        }

        RateLimiter::clear($key);

        return redirect()
            ->route($result['redirect'] ?? 'vendor.dashboard')
            ->with('success', $result['message']);
    }

    public function logout(): RedirectResponse
    {
        $this->authService->logoutVendor();

        return redirect()->route('vendor.login')->with('success', 'You have been logged out.');
    }

    /** Status page for pending / rejected stores */
    public function pending(): View|RedirectResponse
    {
        $user = auth()->user();

        if (! $user->hasRole('vendor')) {
            return redirect()->route($user->dashboardRoute())
                ->withErrors(['message' => 'This page is only for vendor accounts.']);
        }

        $vendor = $user->vendor;

        return view('auth.vendor.pending', compact('vendor'));
    }
}