<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Services\AdminAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function __construct(protected AdminAuthService $authService) {}

    public function showLoginForm(): View
    {
        return view('auth.admin.login');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        // Rate limit: 5 failed attempts per email+IP per minute
        $key = 'admin-login:' . Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withInput($request->only('email'))->withErrors([
                'message' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $result = $this->authService->loginAdmin(
            $request->email,
            $request->password,
            $request->boolean('remember')
        );

        if (! $result['success']) {
            RateLimiter::hit($key, 60);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['message' => $result['message']]);
        }

        RateLimiter::clear($key);

        return redirect()
            ->intended(route('admin.dashboard'))
            ->with('success', $result['message']);
    }

    public function logout(): RedirectResponse
    {
        $this->authService->logoutAdmin();

        return redirect()->route('admin.login')->with('success', 'You have been logged out.');
    }
}