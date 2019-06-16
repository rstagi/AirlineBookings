<?php
require_once 'Global/Constants.php';

// Classes dynamic inclusions
spl_autoload_register();

session_start();

require_once 'Global/Utils.php';

/* Check if cookies and JS are enabled */
Utils::checkCookies();
Utils::checkJavascript();

// MVC Routing
$route = \MVC\API\Utils::evaluateRoute();

// MVC dispatch action (if any)
\MVC\API\Utils::dispatchAction($route);

// MVC show the view result
\MVC\API\Utils::showView($route);
