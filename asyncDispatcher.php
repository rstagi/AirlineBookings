<?php

namespace MVC;
spl_autoload_register(function($name) {
    $parts = explode('\\', $name);
    $parts[0] = $parts[0] == 'AirlineBookings' ? 'Models' : $parts[0];
    require_once implode(DIRECTORY_SEPARATOR, $parts) . '.php';
});

try
{
    $page = $_POST['controller'] ?? '';
    $controller = Router::evaluateRoute($page)->getController();
    $action = $_POST['action'] ?? '';
    $args = $_POST['args'] ? json_decode($_POST['args'], true) : [];
    Dispatcher::dispatch($controller, $action, $args);
    http_response_code(200);
}
catch (\MVC\ControllerException $e)
{
    http_response_code($e->getCode());
    echo $e->getMessage();
}
catch (\Exception $e)
{
    http_response_code(500);
    echo $e->getMessage();
}