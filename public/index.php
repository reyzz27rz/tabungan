<?php

use Illuminate\Http\Request;

// CEGAH ERROR PROVIDER HILANG DI VERCEL
if (getenv('VERCEL')) {
    $manifestPath = __DIR__ . '/../bootstrap/cache/packages.php';
    if (file_exists($manifestPath)) {
        $manifest = include $manifestPath;
        // Hapus semua entri provider yang error/tidak dikenal
        foreach ($manifest as $key => $value) {
            if (str_contains($key, 'Pail') || str_contains($key, 'Pao')) {
                unset($manifest[$key]);
            }
        }
        @file_put_contents($manifestPath, '<?php return ' . var_export($manifest, true) . ';');
    }
}

define('LARAVEL_START', microtime(true));

// Check if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());