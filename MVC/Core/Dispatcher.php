<?php


namespace MVC\Core;

/**
 * Class Dispatcher
 * @package MVC\Core
 *
 * Responsible for dispatching the actions in a proper way to the controller
 */
class Dispatcher
{
    /**
     * @param \MVC\Controller $controller
     * @param $action
     * @param array|null $args
     * @throws Exceptions\DispatcherException
     * @throws \ReflectionException
     */
    public static function dispatch(\MVC\Controller $controller, $action, array $args = null)
    {
        if (!method_exists($controller, $action))
            throw new Exceptions\DispatcherException("Invalid action", 400);

        $method = new \ReflectionMethod(get_class($controller), $action);

        if ($method->getNumberOfParameters()<1)
            $method->invoke($controller);

        else if ($args == null || empty($args))
            throw new DispatcherException("Invalid action", 400);

        else
           $method->invokeArgs($controller, $args);
    }
}