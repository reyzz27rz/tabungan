<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// BERSIHKAN TOTAL MANIFES LAMA DI SEMUA KEMUNGKINAN LOKASI SAAT DI VERCEL
if (getenv('VERCEL')) {
    $possibleManifests = [
        __DIR__ . '/../bootstrap/cache/packages.php',
        '/tmp/storage/framework/packages.php',
        __DIR__ . '/../storage/framework/packages.php',
    ];
    
    foreach ($possibleManifests as $manifest) {
        if (file_exists($manifest)) {
            @unlink($manifest);
        }
    }
}

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

// PENGATURAN VERCEL STORAGE & TEMPORARY PATH:
if (getenv('VERCEL')) {
    $storagePath = '/tmp/storage';
    
    foreach ([
        '',
        '/framework',
        '/framework/views',
        '/framework/cache',
        '/framework/sessions',
    ] as $folder) {
        $path = $storagePath . $folder;
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
    }

    $app->useStoragePath($storagePath);
}

return $app;