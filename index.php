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

switch($_GET['page'] ?? 'Homepage')
{
    case 'Homepage':
        $model = new AirlineBookings\Homepage($db);
        $controller = new Homepage\Controller($model);
        $view = new Homepage\View($model);
        break;
    case 'PersonalPage':
        $model = new AirlineBookings\PersonalPage($db);
        $controller = new PersonalPage\Controller($model);
        $view = new PersonalPage\View($model);
        break;
    default:
        http_response_code(404);
        $view = new MVC\View(null, "Templates/404NotFound.html.php");
        break;
}

if (isset($controller) && isset($_GET['action']) && !empty($_GET['action']))
{
    $controller->{$_GET['action']}();
}

$render = $view->render();
foreach ($render['headers'] as $header) {
    header($header);
}

echo $render['body'];

