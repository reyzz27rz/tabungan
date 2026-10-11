<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Hapus total manifes cache yang mencakup namespace Pao/Pail yang error
$manifestPath = __DIR__ . '/../bootstrap/cache/packages.php';
if (file_exists($manifestPath)) {
    $manifest = include $manifestPath;
    foreach ($manifest as $key => $value) {
        if (str_contains($key, 'Pail') || str_contains($key, 'Pao') || (is_array($value) && isset($value['providers']) && collect($value['providers'])->contains(fn($p) => str_contains($p, 'Pao')))) {
            unset($manifest[$key]);
        }
    }
    @file_put_contents($manifestPath, '<?php return ' . var_export($manifest, true) . ';');
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

// Konfigurasi Storage Vercel
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