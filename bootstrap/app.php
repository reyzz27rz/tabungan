<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// SOLUSI ALTERNATIF: Hapus baris PailServiceProvider langsung dari file manifes cache saat booting di Vercel
if (getenv('VERCEL')) {
    $manifestPath = __DIR__ . '/../bootstrap/cache/packages.php';
    if (file_exists($manifestPath)) {
        $manifest = include $manifestPath;
        if (isset($manifest['laravel/pail'])) {
            unset($manifest['laravel/pail']);
            // Tulis ulang file manifes tanpa Pail
            @file_put_contents($manifestPath, '<?php return ' . var_export($manifest, true) . ';');
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

// Alihkan storage ke /tmp agar writable di Vercel
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