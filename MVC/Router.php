<?php

namespace MVC;

class PageNotFoundException extends \Exception {
    private $pageName;

    public function __construct($pageName) {
        parent::__construct("The page " . $pageName . " is not a valid route.");
        $this->pageName = $pageName;
    }

    public function getPageName() {
        return $this->pageName;
    }
}

class Router {

    public static function evaluateRoute($page)
    {
        switch($_GET['page'] ?? 'Homepage')
        {
            case 'Homepage':
                $model = '\\AirlineBookings\\Homepage';
                $view = '\\Homepage\\View';
                $controller = '\\Homepage\\View';
                break;
            case 'PersonalPage':
                $model = '\\AirlineBookings\\PersonalPage';
                $view = '\\PersonalPage\\View';
                $controller = '\\PersonalPage\\View';
                break;
            default:
                throw new PageNotFoundException($_GET['page']);
                break;
        }

        return new Route($model, $view, $controller);
    }
}