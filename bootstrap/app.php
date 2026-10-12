<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// 1. Jika di Vercel, arahkan dan buat folder writable di /tmp
if (getenv('VERCEL')) {
    $storagePath = '/tmp/storage';
    foreach (['', '/framework', '/framework/views', '/framework/cache', '/framework/sessions'] as $folder) {
        $path = $storagePath . $folder;
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
    }

    $bootstrapCachePath = '/tmp/bootstrap/cache';
    if (!is_dir($bootstrapCachePath)) {
        @mkdir($bootstrapCachePath, 0755, true);
    }

    // Set environment path cache secara runtime agar Laravel tidak menulis ke /var/task
    putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
    putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
}

// 2. Inisialisasi aplikasi Laravel 13
$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    
    ->withMiddleware(function (Middleware $middleware) {
        // Tambahan agar Vercel membaca skema HTTPS dan URL dengan benar
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

// 3. Set storage path ke /tmp yang diizinkan
if (getenv('VERCEL')) {
    $app->useStoragePath('/tmp/storage');
}

return $app;