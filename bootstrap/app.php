<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\UpdateUserOnlineStatus::class,
        ]);
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'admin_staff' => \App\Http\Middleware\AdminStaffMiddleware::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {

        $exceptions->render(function (AuthenticationException $e, $request) {

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated'
                ], 401);
            }

            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        });

    })->create();
    
