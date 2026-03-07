<?php
/**
 * Santhosh Air Travels - MVC Front Controller 
 * TPL --> JS --> Routes --> Controller --> Model
 */

require_once __DIR__ . '/config.php';

// Session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Autoload core classes
spl_autoload_register(function ($className) {
    $paths = [
        APP_PATH . 'core/' . $className . '.php',
        APP_PATH . 'models/' . $className . '.php',
        APP_PATH . 'controllers/' . $className . '.php' // Fallback for controllers not in modules
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load Routes Configuration
$routes = require_once APP_PATH . 'config/routes.php';

// Parse Current Route
$routeParam = isset($_GET['route']) ? trim($_GET['route'], '/') : '';

// Optional: Extract ID from route like view_ticket/1 -> route=view_ticket, id=1
$parts = explode('/', $routeParam);
$requestedRoute = $parts[0] ?: 'default';
$idParam = isset($parts[1]) ? $parts[1] : null;
if ($idParam !== null) {
    $_GET['id'] = $idParam;
}

// Match Route
if (!isset($routes[$requestedRoute])) {
    http_response_code(404);
    die("<h2>404 - Route '$requestedRoute' not found in routes.php</h2><a href='" . BASE_URL . "'>Go Home</a>");
}

$routeConfig = $routes[$requestedRoute];
$moduleName = $routeConfig['module'];
$controllerName = $routeConfig['controller'];
$methodName = $routeConfig['method'];

// Include Module Controller
$controllerPath = APP_PATH . 'modules/' . $moduleName . '/controllers/' . $controllerName . '.php';

// Temporary fallback to root controllers folder if modules folder doesn't exist yet
if (!file_exists($controllerPath)) {
    $controllerPath = APP_PATH . 'controllers/' . $controllerName . '.php';
}

if (!file_exists($controllerPath)) {
    http_response_code(404);
    die("<h2>404 - Controller File '$controllerPath' not found</h2>");
}

require_once $controllerPath;

if (!class_exists($controllerName)) {
    http_response_code(404);
    die("<h2>404 - Controller Class '$controllerName' not found in '$controllerPath'</h2>");
}

// Instantiate Controller & Call Method
$controller = new $controllerName();

if (!method_exists($controller, $methodName)) {
    http_response_code(404);
    die("<h2>404 - Method '$methodName' not found in '$controllerName'</h2>");
}

// Call Method (Pass ID if required)
if ($idParam !== null) {
    $controller->$methodName($idParam);
} else {
    $controller->$methodName();
}
?>
