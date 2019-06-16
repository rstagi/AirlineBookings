<?php

namespace MVC\Core\Exceptions;

use Throwable;

/**
 * Class MVCRoutingException
 * @package MVC
 */
class RoutingException extends \Exception {

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}