<?php

require_once 'Constants.php';

/**
 * Class Utils
 * Contains useful static methods for the application
 */
class Utils
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
     * Check if cookies are enabled
     */
    public static function checkCookies()
    {
        session_start();
        if (!isset($_SESSION['cookies_enabled']) && !isset($_GET['check_cookies']))
        {
            setcookie('test_cookies', 'test_cookies', time() + 20);
            header('Location:./?check_cookies=true&page='.($_GET['page'] ?? 'Homepage')); //set check_cookies to true
            //go to the page it was before
        }
        else if (isset($_GET['check_cookies']))
        {
            if (count($_COOKIE) > 0)        // if cookies are enabled, save a flag in the session
            {
                $_SESSION['cookies_enabled'] = true;
                header('Location:./?page='.$_GET['page']);  //redirect to the requested page
            }
            else // else, die here
            {
                die('Cookies must be enabled in order to use this website. Please, enable them and refresh this page.');
            }
        }
    }

    /**
     * If javascript is disabled (checked in the template), die with an info message
     */
    public static function checkJavascript()
    {
        // if there's no javascript, the template will redirect to this page
        if ($_GET['page']=='noJavascript') {
            // script to redirect as soon as js get enabled
            echo '<script type="text/javascript"> window.location.replace("./?page=Homepage"); </script>';
            die('Javascript must be enabled in order to use this website. Please, enable it and refresh this page.');
        }
    }


    /**
     * @param \Pages\Homepage\Model $model
     * @param bool $showLogged
     * @return string
     * @throws \MVC\Exceptions\ModelException
     */
    public static function formatSeats (\Pages\Homepage\Model $model, bool $showLogged = false) {

        $formatted = '';

        $formatted .= '<table class="table w-100 text-center mx-auto">';

        $cols = Constants::COLS;
        $rows = Constants::ROWS;
        $nonFreeSeats = $model->getNonFreeSeats();

        // take letters integer values
        $firstLetter = ord('A');
        $middleLetter = $firstLetter + (int)($cols/2);
        $lastLetter = $firstLetter + $cols-1;

        // print letters in the first row
        $formatted .= '<thead><th></th>';
        for ($l = $firstLetter; $l < $middleLetter; $l++)
            $formatted .= '<th>'.chr($l).'</th>';
        $formatted .= '<th> </th><th> </th>';
        for ( ; $l <= $lastLetter; $l++)
            $formatted .= '<th>'.chr($l).'</th>';
        $formatted .= '<th></th></thead>';

        // show all the seats
        $formatted .= '<tbody>';
        for ($n = 1; $n <= $rows; $n++) // row numbers
        {
            $formatted .= '<tr><th>'.($n).'</th>';
            for ($l = $firstLetter; $l <= $lastLetter; $l++) // letters
            {
                if ($l == $middleLetter)
                    $formatted .= '<td> </td><td> </td>';

                // seat id is letter+row_number
                $seatId = chr($l).$n;

                // determine seat type (free, reserved, selected, bought)
                $class = 'seat ';
                if (Utils::isNonEmpty($nonFreeSeats['reserved'][$seatId])) {
                    // if it's logged in, it can be selected. Otherwise, it's just reserved
                    if ($showLogged && $model->isUserLoggedIn() && $nonFreeSeats['reserved'][$seatId] == $model->getLoggedUserId()) {
                        $class .= 'selected';
                    } else {
                        $class .= 'reserved';
                    }
                } else if (Utils::isNonEmpty($nonFreeSeats['bought'][$seatId])) {
                    $class .= 'bought';
                } else
                    $class .= 'free';

                // print the image
                $formatted .= '<td><div class="mr-sm-2 seat-wrapper">'.
                    '<img id="'.$seatId.'" class="'.$class.'" />';

                // and the checkbox, only if the user is logged in and we're on the PersonalPage ($showLogged = true)
                if ($model->isUserLoggedIn() && $showLogged && !Utils::isNonEmpty($nonFreeSeats['bought'][$seatId]))
                    $formatted .= "<input type=\"checkbox\" class=\"asyncTrigger $class\" name=\"seats[]\"
                                    id=\"seat$seatId\" value=\"$seatId\" hidden />";

                $formatted .= '</div></td>';

            }
            $formatted .= '<td></td></tr>';
        }
        $formatted .= '</tbody></table>';

        return $formatted;
    }

    /**
     * @param int $occupiedSeats
     * @return int
     */
    public function calculateNumberOfFreeSeats(int $occupiedSeats) : int
    {
        return Constants::COLS * Constants::ROWS - $occupiedSeats;
    }
}