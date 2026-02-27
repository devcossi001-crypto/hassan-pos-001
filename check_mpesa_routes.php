<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

$router = app()->make('router');
$routes = $router->getRoutes();

echo "=== Checking for MPesa Routes ===\n";
$found = false;
foreach ($routes as $route) {
    if (strpos($route->uri, 'mpesa') !== false) {
        echo "Found: " . $route->uri . " -> Methods: " . implode(',', $route->methods) . "\n";
        $found = true;
    }
}

if (!$found) {
    echo "NO MPESA ROUTES FOUND!\n";
    echo "\nAll registered route URIs:\n";
    foreach ($routes as $route) {
        if (strpos($route->uri, 'api') !== false) {
            echo "  " . $route->uri . "\n";
        }
    }
}

$kernel->terminate($request, $response);
?>
