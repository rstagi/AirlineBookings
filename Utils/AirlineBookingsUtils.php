<?php


namespace Utils;

/**
 * Class AirlineBookingsUtils
 * @package Utils
 */
class AirlineBookingsUtils
{
    /**
     * @param $var
     * @return bool
     */
    public static function isNonEmpty($var) : bool
    {
        return isset($var) && !empty($var);
    }
}