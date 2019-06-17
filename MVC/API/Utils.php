<?php

namespace MVC\API;

use MVC\Core as MVCC;
use MVC\Core\Exceptions as MVCCE;

/**
 * Class Utils
 * @package MVC\API
 *
 * Useful functions within the MVC Framework
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
     */
    public static function dispatchAction(MVCC\Route $route) {
        session_start();
        try {
            $action = ($_POST['action'] ?? $_GET['action']) ?? '[]';
            $args = ($_POST['args'] ?? $_GET['args']) ?? '[]';
            if (isset($route) && \Utils::isNonEmpty($action)
                && ($_SESSION['action'] != $action || $_SESSION['args'] != $args) ) {

                // if action and args exist and they are new ones, dispatch the action
                MVCC\Dispatcher::dispatch($route->getController(), $action, $args ? json_decode($args, true) : []);
            }
            else
                return;
        }
        catch (\Exception $e) {
            // nothing done here. Models should set their errors within themselves
        }
        finally {
            // save request parameters to avoid double requests
            $_SESSION['action'] = $action;
            $_SESSION['args'] = $args;
        }
    }

    /**
     * @param MVCC\Route $route
     */
    public static function showView(MVCC\Route $route) {
        // render the view and get headers and body
        $render = $route->getView()->render();
        foreach ($render['headers'] as $header) {
            header($header);
        }
        echo $render['body'];

    }

}