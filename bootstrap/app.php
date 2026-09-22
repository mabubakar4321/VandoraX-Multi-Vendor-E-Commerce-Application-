<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // --- our four middleware, given short names ---
        $middleware->alias([
            'admin'    => \App\Http\Middleware\AdminMiddleware::class,
            'vendor'   => \App\Http\Middleware\VendorMiddleware::class,
            'customer' => \App\Http\Middleware\CustomerMiddleware::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,

            // --- Spatie's own, for @can and middleware('role:admin') ---
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // --- where to send a LOGGED-IN user who opens a login/register page ---
        $middleware->redirectUsersTo(function () {
            return route(auth()->user()?->dashboardRoute() ?? 'customer.dashboard');
        });

        // --- where to send a GUEST who opens a protected page ---
        $middleware->redirectGuestsTo(function ($request) {
            return match (true) {
                $request->is('admin/*')  => route('admin.login'),
                $request->is('vendor/*') => route('vendor.login'),
                default                  => route('customer.login'),
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();