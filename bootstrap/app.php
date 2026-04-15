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
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'superadmin' => \App\Http\Middleware\SuperadminMiddleware::class,
            'client.portal' => \App\Http\Middleware\ClientPortalMiddleware::class,
        ]);

        // Redirigir usuarios no autenticados según el portal que visitan
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if (str_starts_with($request->path(), 'portal')) {
                return route('portal.login');
            }
            return route('admin.login');
        });

        // Redirigir usuarios ya autenticados según su rol
        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            $user = auth()->user();
            if ($user && $user->isClient()) {
                return route('portal.dashboard');
            }
            return route('admin.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
