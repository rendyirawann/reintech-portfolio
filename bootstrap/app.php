<?php

use Illuminate\Http\Request;

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
        /*
         * TLS diterminasi di proxy depan, jadi PHP melihat koneksi biasa.
         * Tanpa ini url()->current() menghasilkan http:// — dan alamat kanonis
         * yang berskema salah membuat mesin pencari memperlakukan versi http
         * dan https sebagai dua halaman berbeda dengan isi yang sama.
         *
         * Header X-Forwarded-* hanya dipercaya karena satu-satunya jalan masuk
         * ke aplikasi ini adalah lewat nginx di mesin yang sama.
         */
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO,
        );

        // Header keamanan (HSTS, CSP, X-Frame-Options, dll) untuk seluruh respons.
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\DeveloperAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
