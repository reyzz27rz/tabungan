<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Jika berjalan di Vercel, arahkan direktori cache framework ke /tmp yang writable
if (getenv('VERCEL')) {
    $storagePath = '/tmp/storage';
    foreach (['', '/framework', '/framework/views', '/framework/cache', '/framework/sessions'] as $folder) {
        $path = $storagePath . $folder;
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
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
    });

// Override path storage dan path cache framework agar aman di Vercel Serverless
if (getenv('VERCEL')) {
    $app->useStoragePath('/tmp/storage');
    
    // Alihkan path cache agar Laravel tidak menulis ke /var/task/user/bootstrap/cache
    $app->useBootstrapPath('/tmp');
}

return $app->create();