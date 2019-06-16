<?php

use MVC\Core as MVCC;
use MVC\Core\Exceptions as MVCCE;

/**
 * Class Utils
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

        // print letters
        $firstLetter = ord('A');
        $middleLetter = $firstLetter + (int)($cols/2);
        $lastLetter = $firstLetter + $cols-1;

        $formatted .= '<thead><th></th>';
        for ($l = $firstLetter; $l < $middleLetter; $l++)
            $formatted .= '<th>'.chr($l).'</th>';
        $formatted .= '<th> </th><th> </th>';
        for ( ; $l <= $lastLetter; $l++)
            $formatted .= '<th>'.chr($l).'</th>';
        $formatted .= '<th></th></thead>';
        // show all the seats
        /// $nonFreeSeats = $model->getNonFreeSeats();

        $formatted .= '<tbody>';
        for ($n = 1; $n <= $rows; $n++)
        {
            $formatted .= '<tr><th>'.($n).'</th>';
            for ($l = $firstLetter; $l <= $lastLetter; $l++)
            {
                if ($l == $middleLetter)
                    $formatted .= '<td> </td><td> </td>';

                $seatId = chr($l).$n;
                $class = 'seat ';
                if (Utils::isNonEmpty($nonFreeSeats['reserved'][$seatId])) {
                    if ($showLogged && $model->isUserLoggedIn() && $nonFreeSeats['reserved'][$seatId] == $model->getLoggedUserId()) {
                        $class .= 'selected';
                    } else {
                        $class .= 'reserved';
                    }
                } else if (Utils::isNonEmpty($nonFreeSeats['bought'][$seatId])) {
                    $class .= 'bought';
                } else
                    $class .= 'free';

                $formatted .= '<td><div class="mr-sm-2 seat-wrapper">'.
                    '<img id="'.$seatId.'" class="'.$class.'" />';

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
     * @return float|int
     */
    public function calculateNumberOfFreeSeats(int $occupiedSeats) {
        return Constants::COLS * Constants::ROWS - $occupiedSeats;
    }
}