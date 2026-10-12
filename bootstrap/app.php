<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// 1. Buat direktori writable di /tmp jika dijalankan di Vercel
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
}

// 2. Inisialisasi instance aplikasi Laravel
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

// 3. Override path storage dan path cached services/packages ke /tmp
if (getenv('VERCEL')) {
    $app->useStoragePath('/tmp/storage');
    
    // Alihkan target penyimpanan cache container bootstrap ke /tmp
    $app->useCachedServicesPath('/tmp/bootstrap/cache/services.php');
    $app->useCachedPackagesPath('/tmp/bootstrap/cache/packages.php');
}

return $app;