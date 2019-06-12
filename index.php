<?php
// Classes inclusions
spl_autoload_register(function($name) {
    $parts = explode('\\', $name);
    $parts[0] = $parts[0] == 'AirlineBookings' ? 'Models' : $parts[0];
    require_once implode(DIRECTORY_SEPARATOR, $parts) . '.php';
});

// Db Configuration
require_once 'Global/config.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_errno())
{
    die("Connect failed: <br>" .
        mysqli_connect_error());
}

// MVC Routing

try {
    $route = \MVC\Router::evaluateRoute($_GET['page'] ?? '');

    $model = $route->getModel($db);
    $controller = $route->getController($model);
    $view = $route->getView($model);
}
catch (\MVC\PageNotFoundException $e) {
    http_response_code(404);
    $view = new \MVC\View(null, 'Templates/404NotFound.html.php');
}
catch (Exception $e) {
    http_response_code(400);
    die ('Bad request for route "'.$_GET['page'].'": '.$e->getMessage().'<br />
            Trace: '.$e->getTraceAsString());
}


if (isset($controller) && isset($_GET['action']) && !empty($_GET['action']))
    \MVC\Dispatcher::dispatch($controller, $_GET['action'], $_GET['args'] ?? '');

$render = $view->render();
foreach ($render['headers'] as $header) {
    header($header);
}

echo $render['body'];

