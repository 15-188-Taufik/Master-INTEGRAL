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
        $middleware->redirectTo(
            guests: '/login',      // Kemana tamu diarahkan jika mencoba akses rute terproteksi
            users: '/dashboard'    // Kemana user diarahkan setelah login
        );

        // PASTIKAN TIDAK ADA rute global yang memaksa auth di sini
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
