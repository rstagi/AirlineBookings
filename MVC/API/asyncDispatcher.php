<?php
namespace MVC\API;
require_once '../../Global/Constants.php';
require_once '../../Global/Utils.php';

// dynamically load classes with ../../../ prefix
spl_autoload_register(function($name) {
    $parts = explode('\\', $name);
    $prefix = ['..', '..'];
    $parts = array_merge( $prefix, $parts);
    require_once implode(DIRECTORY_SEPARATOR, $parts) . '.php';
});

try
{
    $page = $_POST['controller'] ?? '';
    $controller = \MVC\Core\Router::evaluateRoute($page)->getController();
    $action = $_POST['action'] ?? '';
    $args = $_POST['args'] ? json_decode($_POST['args'], true) : [];
    \MVC\Core\Dispatcher::dispatch($controller, $action, $args);
    http_response_code(200);
}
catch (\MVC\Exceptions\ControllerException $e)
{
    http_response_code($e->getCode());
    echo $e->getMessage();
}
catch (\Exception $e)
{
    http_response_code(500);
    echo $e->getMessage();
}