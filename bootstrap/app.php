<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Hapus file manifest lama yang error jika ada di environment Vercel
if (getenv('VERCEL')) {
    $manifestPath = '/tmp/storage/framework/packages.php';
    if (file_exists($manifestPath)) {
        @unlink($manifestPath);
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