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

                $class = 'seat ' . ($nonFreeSeats[$l][$n] ?? '');

                if ($logged)
                    $formatted .= '<td><div id="'.chr($l).$n.'" class="custom-control custom-checkbox mr-sm-2 seat-wrapper">'.
                        '<img src="images/free_seat.png" style="width: 30px" class="'.$class.'" />'.
                        '<input type="checkbox" class="'.$class.' custom-control-input" id="'.chr($l).$n.'"/></div></td>';
                else
                    $formatted .= '<td><div id="'.chr($l).$n.'" class="mr-sm-2 seat-wrapper">'.
                                    '<img src="images/free_seat.png" style="width: 30px" class="'.$class.'" /></div></td>';

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