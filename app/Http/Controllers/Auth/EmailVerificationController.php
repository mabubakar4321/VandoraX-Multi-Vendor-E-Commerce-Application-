<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function __construct(protected UserRepository $userRepository) {}

    /** "We sent you an email" screen */
    public function notice(): View
    {
        return view('auth.verify-email');
    }

    /**
     * The link from the email lands here.
     *
     * Route is protected by the 'signed' middleware, which validates the
     * expiry + HMAC signature. We then verify the hash matches this user's
     * email, so an old link stops working if the user changes their email.
     *
     * Deliberately NOT behind 'auth' — people often open the email on a
     * different device where they aren't logged in.
     */
    public function verify(Request $request, $id, $hash): RedirectResponse
    {
        $user = $this->userRepository->find($id);

        if (! $user) {
            return redirect()->route('home')->withErrors(['message' => 'Invalid verification link.']);
        }

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('home')->withErrors(['message' => 'Invalid verification link.']);
        }

        if ($user->hasVerifiedEmail()) {
            if ($this->isIncompleteVendorRegistration($user)) {
                session(['vendor_registration_user_id' => $user->id]);

                return redirect()->route('vendor.register.step2')
                    ->with('success', 'Your email is already verified. Continue with your payout account.');
            }

            return redirect()->route($this->loginRouteFor($user))
                ->with('success', 'Your email is already verified. Please log in.');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        if ($this->isIncompleteVendorRegistration($user)) {
            session(['vendor_registration_user_id' => $user->id]);

            return redirect()->route('vendor.register.step2')
                ->with('success', 'Email verified! Continue with your payout account.');
        }

        return redirect()->route($this->loginRouteFor($user))
            ->with('success', 'Email verified! You can now log in.');
    }

    /** Resend, throttled by the route (6 per minute) */
    public function resend(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = $this->userRepository->findByEmail($request->email);

        // Always report success — do not reveal whether the account exists
        if ($user && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return back()->with('success', 'If that account needs verification, a new link is on its way.');
    }

    private function loginRouteFor($user): string
    {
        return match (true) {
            $user->hasRole('admin')  => 'admin.login',
            $user->hasRole('vendor') => 'vendor.login',
            $user->hasRole('customer') => 'customer.login',
            default                  => 'customer.login',
        };
    }

    private function isIncompleteVendorRegistration($user): bool
    {
        return $user->hasRole('vendor') && ! $user->vendor()->exists();
    }
}