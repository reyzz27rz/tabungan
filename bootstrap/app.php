<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// 1. Tangani writable storage khusus untuk Vercel Serverless
if (getenv('VERCEL')) {
    $storagePath = '/tmp/storage';
    foreach (['', '/framework', '/framework/views', '/framework/cache', '/framework/sessions'] as $folder) {
        $path = $storagePath . $folder;
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
    }
}

// 2. Inisialisasi aplikasi Laravel 13
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

// 3. Arahkan path storage ke /tmp yang writable
if (getenv('VERCEL')) {
    $app->useStoragePath('/tmp/storage');
}

return $app;