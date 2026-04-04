<?php

/**
 * This is the central route handler of the application.
 * It uses FastRoute to map URLs to controller methods.
 * 
 * See the documentation for FastRoute for more information: https://github.com/nikic/FastRoute
 */

// CORS headers for localhost requests
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (preg_match('/^https?:\/\/(localhost|127\.0\.0\.1|::1)(:\d+)?$/', $origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    // Specifies which HTTP methods are allowed when accessing the resource from the origin
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    // Specifies which HTTP headers can be used when making the actual request
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    // Allows cookies and authentication credentials to be sent with cross-origin requests
    header('Access-Control-Allow-Credentials: true');
    // Specifies how long (in seconds) the browser can cache the preflight response (24 hours)
    header('Access-Control-Max-Age: 86400');
}

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

/**
 * Define the routes for the application.
 */
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // Auth routes
    $r->addRoute('POST', '/auth/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('POST', '/auth/register', ['App\Controllers\AuthController', 'register']);
    $r->addRoute('POST', '/auth/logout', ['App\Controllers\AuthController', 'logout']);

    // Tasks routes
    $r->addRoute('GET', '/tasks/dashboard', ['App\Controllers\TaskController', 'dashboard']);
    $r->addRoute('GET', '/tasks', ['App\Controllers\TaskController', 'getAll']);
    $r->addRoute('GET', '/tasks/{id}', ['App\Controllers\TaskController', 'get']);
    $r->addRoute('POST', '/tasks', ['App\Controllers\TaskController', 'create']);
    $r->addRoute('PUT', '/tasks/change-status', ['App\Controllers\TaskController', 'changeTaskStatus']);
    $r->addRoute('PUT', '/tasks/{id}', ['App\Controllers\TaskController', 'update']);
    $r->addRoute('DELETE', '/tasks/{id}', ['App\Controllers\TaskController', 'delete']);

    //Categories routes
    $r->addRoute('GET', '/categories', ['App\Controllers\CategoryController', 'getAll']);
    $r->addRoute('GET', '/categories/{id}', ['App\Controllers\CategoryController', 'get']);
    $r->addRoute('POST', '/categories', ['App\Controllers\CategoryController', 'create']);
    $r->addRoute('PUT', '/categories/{id}', ['App\Controllers\CategoryController', 'update']);
    $r->addRoute('DELETE', '/categories/{id}', ['App\Controllers\CategoryController', 'delete']);
});


/**
 * Get the request method and URI from the server variables and invoke the dispatcher.
 */
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

/**
 * Switch on the dispatcher result and call the appropriate controller method if found.
 */
switch ($routeInfo[0]) {
    // Handle not found routes
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo 'Not Found';
        break;
    // Handle routes that were invoked with the wrong HTTP method
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo 'Method Not Allowed';
        break;
    // Handle found routes
    case FastRoute\Dispatcher::FOUND:
        $class = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $controller = new $class();
        $vars = $routeInfo[2];
        $controller->$method($vars);
        break;
}