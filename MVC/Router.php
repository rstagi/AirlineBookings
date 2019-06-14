<?php

namespace MVC;

use Throwable;

/**
 * Class PageNotFoundException
 * @package MVC
 */
class MVCRoutingException extends \Exception {

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Class Router
 * @package MVC
 */
class Router {

    /**
     * @param $page
     * @return Route
     * @throws MVCRoutingException
     */
    public static function evaluateRoute($page)
    {
        switch($page ? strtolower($page) : '400')
        {
            case 'homepage':
                $model = '\\AirlineBookings\\Homepage';
                $view = '\\Homepage\\View';
                $controller = '\\Homepage\\Controller';
                break;
            case 'personalpage':
                $model = '\\AirlineBookings\\PersonalPage';
                $view = '\\PersonalPage\\View';
                $controller = '\\PersonalPage\\Controller';
                break;
            case 'login':
            case 'register':
            case 'signin':
                $model = '\\AirlineBookings\\SignIn';
                $view = '\\SignIn\\View';
                $controller = '\\SignIn\\Controller';
                break;
            case '401unauthorized':
                throw new MVCRoutingException("Unauthorized", 401);
                break;
            default:
                throw new MVCRoutingException("Page not found", 404);
                break;
        }

        return new Route($model, $view, $controller);
    }
}