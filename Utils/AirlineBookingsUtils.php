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

    /**
     *
     */
    public static function checkCookies()
    {
        session_start();

        if (!isset($_SESSION['cookies_enabled']) && !isset($_GET['check_cookies'])) {
            setcookie('test_cookies', 'test_cookies', time() + 20);
            header('Location:./?check_cookies=true&page='.($_GET['page'] ?? 'Homepage')); //set check_cookies to true
            //go to the page it was before
        } else if (isset($_GET['check_cookies'])) {
            if (count($_COOKIE) > 0){   // if cookies are enabled
                $_SESSION['cookies_enabled'] = true; // remember it and go to the previously requested page
                header('Location:./?page='.$_GET['page']);
            } else {    // else, die here
                die('Cookies must be enabled in order to use this website. Please, enable them and refresh this page.');
            }
        }
    }
}