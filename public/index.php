<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Autoload composer
require __DIR__.'/../vendor/autoload.php';

try {
    // Bootstrap Laravel application
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $kernel = $app->make(Kernel::class);

    $response = $kernel->handle(
        $request = Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    // Tangkap MENTAH-MENTAH semua error tanpa memanggil sistem View Laravel yang rusak
    http_response_code(500);
    echo "<div style='font-family: monospace; padding: 20px; background: #ffe6e6; color: #cc0000;'>";
    echo "<h2>FATAL ERROR TERTANGKAP:</h2>";
    echo "<p><strong>Pesan:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Baris " . $e->getLine() . ")</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
    exit;
}