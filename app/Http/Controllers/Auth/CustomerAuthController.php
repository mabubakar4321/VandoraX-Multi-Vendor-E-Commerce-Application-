<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerLoginRequest;
use App\Http\Requests\CustomerRegisterRequest;
use App\Services\CustomerAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function __construct(protected CustomerAuthService $authService) {}

    public function showRegistrationForm(): View
    {
        return view('auth.customer.register');
    }

    public function register(CustomerRegisterRequest $request): RedirectResponse
    {
        $result = $this->authService->registerCustomer($request->validated());

        if (! $result['success']) {
            return back()->withInput()->withErrors(['message' => $result['message']]);
        }

        return redirect()
            ->route('verification.notice')
            ->with('success', $result['message'])
            ->with('pending_email', $result['user']->email);
    }

    public function showLoginForm(): View
    {
        return view('auth.customer.login');
    }

    public function login(CustomerLoginRequest $request): RedirectResponse
    {
        $key = 'customer-login:' . Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withInput($request->only('email'))->withErrors([
                'message' => 'Too many attempts. Try again in ' . RateLimiter::availableIn($key) . ' seconds.',
            ]);
        }

        $result = $this->authService->loginCustomer(
            $request->email,
            $request->password,
            $request->boolean('remember')
        );

        if (! $result['success']) {
            RateLimiter::hit($key, 60);

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
            ->route('customer.dashboard')
            ->with('success', $result['message']);
    }

    public function logout(): RedirectResponse
    {
        $this->authService->logoutCustomer();

        return redirect()->route('customer.login')->with('success', 'You have been logged out.');
    }
}