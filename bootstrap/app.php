<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// 1. Hapus paksa file cache manifest agar package dev yang hilang tidak dibaca
$manifestPath = __DIR__ . '/../bootstrap/cache/packages.php';
if (file_exists($manifestPath)) {
    @unlink($manifestPath);
}

// 2. Konfigurasi aplikasi Laravel 13
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
    });

// 3. Tangani writable storage path khusus di Vercel Serverless
if (getenv('VERCEL')) {
    $storagePath = '/tmp/storage';
    foreach (['', '/framework', '/framework/views', '/framework/cache', '/framework/sessions'] as $folder) {
        $path = $storagePath . $folder;
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
    }
    
    // Binding path storage agar mengarah ke /tmp yang memiliki izin tulis
    $app->useStoragePath($storagePath);
}

return $app->create();