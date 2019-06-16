<?php
// Classes dynamic inclusions
spl_autoload_register(function($name) {
    $parts = explode('\\', $name);
    $parts[0] = $parts[0] == 'AirlineBookings' ? 'Models' : $parts[0];
    require_once implode(DIRECTORY_SEPARATOR, $parts) . '.php';
});

session_start();

/* Check if cookies are enabled */
\Utils\AirlineBookingsUtils::checkCookies();

// if there's no javascript, die here
if ($_GET['page']=='noJavascript') {
    // script to redirect as soon as js get enabled
    echo '<script type="text/javascript"> window.location.replace("./?page=Homepage"); </script>';
    die('Javascript must be enabled in order to use this website. Please, enable it and refresh this page.');
}

// MVC Routing
try {
    $route = \MVC\Router::evaluateRoute($_GET['page'] ?? 'Homepage');

    $view = $route->getView();
}
catch (\MVC\MVCRoutingException $e){
    if ($e->getCode() === 401)
        $view = new \MVC\View(null, 'Templates/Errors/401Unauthorized.html.php');
    else if ($e->getCode() === 404)
        $view = new \MVC\View(null, 'Templates/Errors/404NotFound.html.php');
    else {
        http_response_code($e->getCode());
        die ("HTTP Error " . $e->getCode());
    }
}
catch (Exception $e) {
    $view = new \MVC\View(null, 'Templates/Errors/500InternalServerError.html.php');
}

/* catch actions */
try {
    $action = ($_POST['action'] ?? $_GET['action']) ?? '[]';
    $args = ($_POST['args'] ?? $_GET['args']) ?? '[]';
    if (isset($route) && \Utils\AirlineBookingsUtils::isNonEmpty($action)
        && ($_SESSION['action'] != $action || $_SESSION['args'] != $args) ) {
        \MVC\Dispatcher::dispatch($route->getController(), $action, $args ? json_decode($args, true) : []);

    }

}
catch (\Exception $e)
{
}
finally {
    $_SESSION['action'] = $action;
    $_SESSION['args'] = $args;
}

/* render view */
$render = $view->render();
foreach ($render['headers'] as $header) {
    header($header);
}
echo $render['body'];
