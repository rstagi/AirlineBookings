<?php


namespace Utils;

/**
 * Class SeatsUtils
 * @package Utils
 */
class SeatsUtils
{

    /**
     * @param $cols
     * @param $rows
     * @param $nonFreeSeats
     * @param $logged
     * @return string
     */
    public static function formatSeats ($cols, $rows, $nonFreeSeats, $logged) {

        $formatted = '';

        $formatted .= '<table class="table w-100 text-center mx-auto">';

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
                $img = 'free_seat.png';
                if (AirlineBookingsUtils::isNonEmpty($nonFreeSeats['reserved'][$seatId])) {
                    if ($nonFreeSeats['reserved'][$seatId] != $_SESSION[\MVC\Model::USER_ID_KEY]) {
                        $class .= 'selected';
                        $img = 'selected_seat.png';
                    } else {
                        $class .= 'reserved';
                        $img = 'reserved_seat.png';
                    }
                } else if (AirlineBookingsUtils::isNonEmpty($nonFreeSeats['bought'][$seatId])) {
                    $class .= 'bought';
                    $img = 'bought_seat.png';
                } else
                    $class .= 'free';

                $formatted .= '<td><div class="mr-sm-2 seat-wrapper">'.
                        '<img id="'.$seatId.'" src="images/'.$img.'" style="width: 30px" class="'.$class.'" />';

                if ($logged && !AirlineBookingsUtils::isNonEmpty($nonFreeSeats['bought'][$seatId]))
                    $formatted .= "<input type=\"checkbox\" class=\"triggerAction $class\" action=\"reserve\"
                                    failure=\"reservationFailed\" id=\"seat$seatId\" name=\"$seatId\" hidden />";

                $formatted .= '</div></td>';

            }
            $formatted .= '<td></td></tr>';
        }
        $formatted .= '</tbody></table>';

        return $formatted;
    }

    public function calculateNumberOfFreeSeats(int $occupiedSeats) {
        return \AirlineBookings\SeatsModel::COLS * \AirlineBookings\SeatsModel::ROWS - $occupiedSeats;
    }
}