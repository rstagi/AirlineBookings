<?php


namespace MVC;

use Throwable;

/**
 * Class DispatcherException
 * @package MVC
 */
class DispatcherException extends ControllerException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Class Dispatcher
 * @package MVC
 */
class Dispatcher
{
    /**
     * @param Controller $controller
     * @param $action
     * @param array|null $args
     * @throws DispatcherException
     * @throws \ReflectionException
     */
    public static function dispatch(Controller $controller, $action, ?array $args = null)
    {
        if (!method_exists($controller, $action))
            throw new DispatcherException("Invalid action", 400);


        $method = new \ReflectionMethod(get_class($controller), $action);
        if ($method->getNumberOfParameters()<1)
            $method->invoke($controller);
        else if ($args == null || empty($args))
            throw new DispatcherException("Invalid action", 400);
        else
           $method->invokeArgs($controller, $args);
    }
}