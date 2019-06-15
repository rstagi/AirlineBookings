<?php


namespace Utils;

/**
 * Class SeatsUtils
 * @package Utils
 */
class SeatsUtils
{

    /**
     * @param \AirlineBookings\SeatsMap $model
     * @param bool $showLogged
     * @return string
     * @throws \MVC\ModelException
     * @throws \ReflectionException
     */
    public static function formatSeats (\AirlineBookings\SeatsMap $model, bool $showLogged = false) {

        $formatted = '';

        $formatted .= '<table class="table w-100 text-center mx-auto">';

        $cols = \AirlineBookings\SeatsMap::COLS;
        $rows = \AirlineBookings\SeatsMap::ROWS;
        $nonFreeSeats = $model->getNonFreeSeats();

        // print letters
        $firstLetter = ord('A');
        $middleLetter = $firstLetter + $cols/2;
        $lastLetter = $firstLetter + $cols-1;

        $formatted .= '<thead><th></th>';
        for ($l = $firstLetter; $l < $middleLetter; $l++)
            $formatted .= '<th>'.chr($l).'</th>';
        $formatted .= '<th> </th>';
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
                    $formatted .= '<td> </td>';

                $seatId = chr($l).$n;
                $class = 'seat ';
                if (AirlineBookingsUtils::isNonEmpty($nonFreeSeats['reserved'][$seatId])) {
                    if ($model->isUserLoggedIn() && $nonFreeSeats['reserved'][$seatId] == $model->getLoggedUserId()) {
                        $class .= 'selected';
                    } else {
                        $class .= 'reserved';
                    }
                } else if (AirlineBookingsUtils::isNonEmpty($nonFreeSeats['bought'][$seatId])) {
                    $class .= 'bought';
                } else
                    $class .= 'free';

                $formatted .= '<td><div class="mr-sm-2 seat-wrapper">'.
                        '<img id="'.$seatId.'" class="'.$class.'" />';

                if ($model->isUserLoggedIn() && $showLogged && !AirlineBookingsUtils::isNonEmpty($nonFreeSeats['bought'][$seatId]))
                    $formatted .= "<input type=\"checkbox\" class=\"triggerAction $class\" 
                                    id=\"seat$seatId\" name=\"$seatId\" hidden />";

                $formatted .= '</div></td>';

            }
            $formatted .= '<td></td></tr>';
        }
        $formatted .= '</tbody></table>';

        return $formatted;
    }

    public function calculateNumberOfFreeSeats(int $occupiedSeats) {
        return \AirlineBookings\SeatsMap::COLS * \AirlineBookings\SeatsMap::ROWS - $occupiedSeats;
    }
}