<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function __construct(protected PasswordResetService $passwordResetService) {}

    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $result = $this->passwordResetService->sendResetLink($request->email);

        return back()->with('success', $result['message']);
    }

    /** Token comes from the URL, email from the query string */
    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $result = $this->passwordResetService->resetPassword($request->validated() + [
            'password_confirmation' => $request->password_confirmation,
        ]);

        if (! $result['success']) {
            return back()->withInput($request->only('email'))->withErrors(['message' => $result['message']]);
        }

        return redirect()->route('customer.login')->with('success', $result['message']);
    }
}