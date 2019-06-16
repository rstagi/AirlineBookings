<?php

namespace MVC\Core;
use MVC;
use Pages;

/**
 * Class Router
 * @package MVC
 */
class Router {
    const UNAUTHORIZED_TEMPLATE = 'Templates/Errors/401Unauthorized.html.php';
    const NOT_FOUND_TEMPLATE = 'Templates/Errors/404NotFound.html.php';
    const SERVER_ERROR_TEMPLATE = 'Templates/Errors/500InternalServerError.html.php';

    /**
     * @param $page
     * @return Route
     */
    public static function evaluateRoute($page) : Route
    {
        switch($page ? strtolower($page) : '400')
        {
            case 'homepage':
                $model = new Pages\Homepage\Model();
                $view = new Pages\Homepage\View($model);
                $controller = new Pages\Homepage\Controller($model);
                break;
            case 'personalpage':
                $model = new Pages\PersonalPage\Model();
                $view = new Pages\PersonalPage\View($model);
                $controller = new Pages\PersonalPage\Controller($model);
                break;
            case 'login':
            case 'register':
            case 'signin':
                $model = new Pages\SignIn\Model();
                $view = new Pages\SignIn\View($model);
                $controller = new Pages\SignIn\Controller($model);
                break;
            case '401unauthorized':
                $model = null;
                $view = new MVC\View(null, self::UNAUTHORIZED_TEMPLATE);
                $controller = new MVC\Controller();
                break;
            default:
                $model = null;
                $view = new MVC\View(null, self::NOT_FOUND_TEMPLATE);
                $controller = new MVC\Controller();
                break;
        }

        return new Route($model, $view, $controller);
    }

    public static function getServerErrorRoute() : Route
    {
        return new Route(null,  new MVC\View(null, self::SERVER_ERROR_TEMPLATE),
                                       new MVC\Controller(null) );
    }
}