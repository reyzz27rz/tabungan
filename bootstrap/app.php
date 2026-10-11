<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

// PENGAMANAN MUTLAK VERCEL: Paksa registrasi instance View dan Routing agar tidak pernah missing
if (!$app->bound('view')) {
    $app->register(\Illuminate\View\ViewServiceProvider::class);
}
if (!$app->bound('router')) {
    $app->register(\Illuminate\Routing\RoutingServiceProvider::class);
}

// Alihkan storage ke /tmp agar writable di Vercel
$app->useStoragePath('/tmp/storage');

return $app;