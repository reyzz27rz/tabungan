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

// ENGINEERING BYPASS: Jika class provider eksternal tidak ada di server, cegah agar tidak di-load
if (!class_exists(\Laravel\Pail\PailServiceProvider::class)) {
    // Hapus atau cegah pendaftaran provider yang hilang dari container
    app()->offsetUnset('Laravel\Pail\PailServiceProvider');
}

// Konfigurasi Storage & Cache agar writable di Vercel Serverless
if (getenv('VERCEL')) {
    $storagePath = '/tmp/storage';
    foreach (['', '/framework', '/framework/views', '/framework/cache', '/framework/sessions'] as $folder) {
        $path = $storagePath . $folder;
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
    }
    $app->useStoragePath($storagePath);
}

return $app;