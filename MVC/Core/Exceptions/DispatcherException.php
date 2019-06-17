<?php


namespace MVC\Core\Exceptions;

use MVC\ControllerException;
use Throwable;

/**
 * Class DispatcherException
 * @package MVC
 */
class DispatcherException extends \MVC\Exceptions\ControllerException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}