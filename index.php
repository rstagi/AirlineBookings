<?php
// Classes inclusions
spl_autoload_register(function($name) {
    $parts = explode('\\', $name);
    $parts[0] = $parts[0] == 'AirlineBookings' ? 'Models' : $parts[0];
    require_once implode(DIRECTORY_SEPARATOR, $parts) . '.php';
});

session_set_cookie_params(1800);
session_start();
if (!isset($_SESSION['cookies_enabled']) && !isset($_GET['check_cookies'])) {
    setcookie('test_cookies', 'test_cookies', time() + 20);
    header('Location:./?check_cookies=true&page='.($_GET['page'] ?? 'Homepage'));
} else if (isset($_GET['check_cookies'])) {
    if (count($_COOKIE) > 0){
        $_SESSION['cookies_enabled'] = true;
        header('Location:./?page='.$_GET['page']);
    } else {
        die("Please enable cookies to use this website.");
    }
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

if (isset($route) && isset($_GET['action']) && !empty($_GET['action']))
    \MVC\Dispatcher::dispatch($route->getController(), $_GET['action'], $_GET['args'] ? json_decode($_GET['args']) : []);

$render = $view->render();
foreach ($render['headers'] as $header) {
    header($header);
}

echo $render['body'];
