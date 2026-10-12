<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// 1. Buat instance Application terlebih dahulu menggunakan ->create()
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

// 2. Jika di Vercel, alihkan storage dan cache ke direktori /tmp yang memiliki izin tulis (writable)
if (getenv('VERCEL')) {
    $storagePath = '/tmp/storage';
    foreach (['', '/framework', '/framework/views', '/framework/cache', '/framework/sessions'] as $folder) {
        $path = $storagePath . $folder;
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
    }

    $bootstrapCacheDir = '/tmp/bootstrap/cache';
    if (!is_dir($bootstrapCacheDir)) {
        @mkdir($bootstrapCacheDir, 0755, true);
    }

    // Gunakan method yang sekarang sudah valid karena $app adalah instance Application
    $app->useStoragePath($storagePath);
    $app->useBootstrapPath('/tmp');
}

return $app;