<?php

use Illuminate\Http\Request;

$manifestPath = __DIR__ . '/../bootstrap/cache/packages.php';
if (file_exists($manifestPath)) {
    @unlink($manifestPath);
}


define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());