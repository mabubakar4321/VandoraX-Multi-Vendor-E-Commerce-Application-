<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('customer.login')
                ->withErrors(['message' => 'Please log in to continue.']);
        }

        if (! auth()->user()->hasRole('customer')) {
            abort(403, 'You do not have permission to access this area.');
        }

        if (! auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('customer.login')
                ->withErrors(['message' => 'Your account has been deactivated.']);
        }

        return $next($request);
    }
}