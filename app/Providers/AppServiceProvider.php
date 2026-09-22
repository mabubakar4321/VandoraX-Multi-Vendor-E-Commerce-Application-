<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('update', function (Authenticatable $user, Product $product): bool {
            return $user->vendor?->id === $product->vendor_id;
        });

        Gate::define('delete', function (Authenticatable $user, Product $product): bool {
            return $user->vendor?->id === $product->vendor_id;
        });

        // 5 tries per email+IP, and a 20/minute ceiling for the whole IP.
        // The IP ceiling stops someone cycling through many emails from one machine.
        RateLimiter::for('login', function (Request $request) {
            $email = strtolower((string) $request->input('email'));

            return [
                Limit::perMinute(5)->by($email . '|' . $request->ip()),
                Limit::perMinute(20)->by($request->ip()),
            ];
        });

        // Registration and password-reset emails: stricter, since each one sends mail
        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));
        RateLimiter::for('password-email', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));
    }
}