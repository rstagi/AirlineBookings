<?php

namespace MVC\Exceptions;

/**
 * Class ModelException
 * @package MVC
 */
class ModelException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}