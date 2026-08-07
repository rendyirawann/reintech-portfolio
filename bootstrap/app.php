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
        // Header keamanan (HSTS, CSP, X-Frame-Options, dll) untuk seluruh respons.
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->alias([
            'developer.auth' => \App\Http\Middleware\DeveloperAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
