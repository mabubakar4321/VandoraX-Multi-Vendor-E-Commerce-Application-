<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('admin.login')
                ->withErrors(['message' => 'Please log in to continue.']);
        }

        if (! auth()->user()->hasRole('admin')) {
            abort(403, 'You do not have permission to access the admin area.');
        }

        if (! auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('admin.login')
                ->withErrors(['message' => 'Your account has been deactivated.']);
        }

        return $next($request);
    }
}