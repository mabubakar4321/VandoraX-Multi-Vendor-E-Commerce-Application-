<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('vendor.login')
                ->withErrors(['message' => 'Please log in to continue.']);
        }

        $user = auth()->user();

        if (! $user->hasRole('vendor')) {
            abort(403, 'You do not have permission to access the vendor area.');
        }

        if (! $user->is_active) {
            auth()->logout();
            return redirect()->route('vendor.login')
                ->withErrors(['message' => 'Your account has been deactivated.']);
        }

        $vendor = $user->vendor;

        // Never finished the wizard
        if (! $vendor) {
            return redirect()->route('vendor.register.step3')
                ->withErrors(['message' => 'Please complete your store application.']);
        }

        // Pending or rejected → status page (which is NOT inside this middleware group)
        if ($vendor->status !== 'active') {
            return redirect()->route('vendor.pending');
        }

        return $next($request);
    }
}