<?php

namespace MVC\API;

use MVC\Core as MVCC;
use MVC\Core\Exceptions as MVCCE;

/**
 * Class Utils
 */
class Utils
{

    /**
     * @return MVCC\Route
     */
    public static function evaluateRoute () : MVCC\Route
    {
        try {
            $route = MVCC\Router::evaluateRoute($_GET['page'] ?? 'Homepage');
        }
        catch (Exception $e) {
            $route = MVCC\Router::getServerErrorRoute();
        }
        return $route;
    }

    /**
     * @param MVCC\Route $route
     * @throws MVCCE\DispatcherException
     */
    public static function dispatchAction(MVCC\Route $route) {
        /* catch actions */
        session_start();
        try {
            $action = ($_POST['action'] ?? $_GET['action']) ?? '[]';
            $args = ($_POST['args'] ?? $_GET['args']) ?? '[]';
            if (isset($route) && \Utils::isNonEmpty($action)
                && ($_SESSION['action'] != $action || $_SESSION['args'] != $args) ) {
                MVCC\Dispatcher::dispatch($route->getController(), $action, $args ? json_decode($args, true) : []);
            }
        }
        catch (\Exception $e) {}
        finally {
            $_SESSION['action'] = $action;
            $_SESSION['args'] = $args;
        }
    }

    /**
     * @param MVCC\Route $route
     */
    public static function showView(MVCC\Route $route) {
        /* render view */
        $render = $route->getView()->render();
        foreach ($render['headers'] as $header) {
            header($header);
        }
        echo $render['body'];

    }

}