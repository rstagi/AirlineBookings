<?php
// Classes inclusions
spl_autoload_register(function($name) {
    $parts = explode('\\', $name);
    $parts[0] = $parts[0] == 'AirlineBookings' ? 'Models' : $parts[0];
    require_once implode(DIRECTORY_SEPARATOR, $parts) . '.php';
});

// MVC Routing

try {
    $route = \MVC\Router::evaluateRoute($_GET['page'] ?? 'Homepage');

    $view = $route->getView();
}
catch (\MVC\PageNotFoundException $e) {
    $view = new \MVC\View(null, 'Templates/Errors/404NotFound.html.php');
}
catch (\MVC\UnauthorizedException $e) {
    $view = new \MVC\View(null, 'Templates/Errors/401Unauthorized.html.php');
}
catch (Exception $e) {
    $view = new \MVC\View(null, 'Templates/Errors/500InternalServerError.html.php');
}

if (isset($route) && isset($_GET['action']) && !empty($_GET['action']))
    \MVC\Dispatcher::dispatch($route->getController(), $_GET['action'], $_GET['args'] ? json_decode($_GET['args']) : []);

$render = $view->render();
foreach ($render['headers'] as $header) {
    header($header);
}

echo $render['body'];
