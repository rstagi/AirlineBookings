<?php

namespace MVC;

use Throwable;

/**
 * Class PageNotFoundException
 * @package MVC
 */
class PageNotFoundException extends \Exception {
    private $pageName;

    /**
     * PageNotFoundException constructor.
     * @param $pageName
     */
    public function __construct($pageName) {
        parent::__construct("The page " . $pageName . " is not a valid route.");
        $this->pageName = $pageName;
    }

    /**
     * @return mixed
     */
    public function getPageName() {
        return $this->pageName;
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
     * @throws PageNotFoundException
     * @throws UnauthorizedException
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
                throw new UnauthorizedException();
                break;
            default:
                throw new PageNotFoundException($_GET['page']);
                break;
        }

        return new Route($model, $view, $controller);
    }
}