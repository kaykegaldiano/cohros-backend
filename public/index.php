<?php

require_once __DIR__ . '/../vendor/autoload.php';

$path = $_SERVER['PATH_INFO'] ?? '/';
$routes = require __DIR__ . '/../config/routes.php';

if (!isset($routes[$path])) {
    http_response_code(404);
    die(json_encode([
        'error' => 'Route not found',
    ]));
}

$controllerClass = $routes[$path];
$controller = new $controllerClass();
$controller->handle();
