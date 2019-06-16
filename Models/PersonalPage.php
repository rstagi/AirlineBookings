<?php
namespace AirlineBookings;

use MVC\ModelException;

/**
 * Class PersonalPage
 * @package AirlineBookings
 */
class PersonalPage extends SeatsMap {

    /**
     * PersonalPage constructor.
     */
    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * @return int
     * @throws \MVC\ModelException
     */
    public function getNumberOfSelectedSeats () : int
    {
        $result = $this->query('SELECT COUNT(*) FROM Reservations
                                        WHERE SeatId NOT IN (
                                            SELECT SeatId FROM Purchases
                                        ) AND UserId = ?', $this->getLoggedUserId());

        $numberOfSeats = $result->fetch_array()[0];
        $result->close();
        return $numberOfSeats;
    }

    /**
     * @param string $seatId
     * @return bool
     * @throws ModelException
     */
    public function reserveSeat(string $seatId) {
        $reservationTable = self::RESERVATIONS_TABLE;
        $purchasesTable = self::PURCHASES_TABLE;
        $this->transactionBegin();

        // check if the seat has been purchased by another user
        $result = $this->query("SELECT * FROM $purchasesTable WHERE SeatId = ?", $seatId);
        if ($result->num_rows > 0) {
            $this->transactionRollback();
            $this->error = "The seat <b>$seatId</b> has been purchased by another user.";
            return false;
        }

        // check if there are other reservations for the same seat (in case, update the one with the current user id)
        $result = $this->query("SELECT * FROM $reservationTable WHERE SeatId = ? FOR UPDATE", $seatId);

        if ($result->num_rows > 0) {
            $rowsAffected = $this->execute("UPDATE $reservationTable SET UserId = ? WHERE SeatId = ?", $this->getLoggedUserId(), $seatId);
        } else {
            $rowsAffected = $this->execute( "INSERT INTO $reservationTable (SeatId, UserId) 
                                                       VALUES (?, ?)", $seatId, $this->getLoggedUserId());
        }
        if ($rowsAffected < 1) {
            $this->transactionRollback();
            $this->error = "Something went wrong while reserving the seat $seatId. Please, try again.";
            return false;
        }

        $this->transactionCommit();
        $this->success = "The seat <b>$seatId</b> has been successfully reserved.";
        return true;
    }

    /**
     * @param $seatId
     * @throws ModelException
     */
    public function freeSeat($seatId) {
       $this->execute("DELETE FROM ".self::RESERVATIONS_TABLE." WHERE SeatId = ? AND UserId = ?",
           $seatId, $this->getLoggedUserId());
        $this->success = "The reservation for <b>$seatId</b> has been successfully canceled.";
    }

    /**
     * @param array $seats
     * @return bool
     * @throws ModelException
     */
    public function buySeats(array $seats) {
        $reservationTable = self::RESERVATIONS_TABLE;
        $purchasesTable = self::PURCHASES_TABLE;
        $userId = $this->getLoggedUserId(); // checks if user is logged in too (without updating the token)

        $this->transactionBegin();
        $seatsStr = "'".implode("', '", $seats)."'";

        // check if it has already been sold
        $result = $this->query("SELECT * FROM $purchasesTable WHERE SeatId IN ( $seatsStr )");
        if ($result->num_rows > 0) {
            $this->transactionRollback();
            $this->error = "One of the seats you were trying to buy has already been sold.<br />Sorry for the inconvenience.";
            return false;
        }

        // check if it's reserved by the current user
        $result = $this->query("SELECT * FROM $reservationTable ".
                                        "   WHERE SeatId IN ( $seatsStr ) ".
                                        "   AND UserId = ? FOR UPDATE",
                                            $userId);

        if ($result->num_rows < sizeof($seats)) {
            $this->transactionRollback();
            $this->error = "One of the seats you were trying to buy is not reserved to you anymore. Please, try again.";
            return false;
        }

        // finally, buy the selected seats
        $rowsAffected = 0;
        foreach($seats as $seatId) {
            $rowsAffected += $this->execute("INSERT INTO $purchasesTable (SeatId, UserId) 
                                                       VALUES (?, ?)", $seatId, $userId);
        }
        if ($rowsAffected < sizeof($seats)) {
            $this->transactionRollback();
            $this->error = "Something went wrong while finalizing the purchase of your seats. Please, try again.";
            return false;
        }

        $this->transactionCommit();
        $this->success = "Your purchase has been successfully completed. Purchased seats: <b>".implode('</b>, <b>', $seats).'</b>';
        return true;
    }

    /**
     * @throws ModelException
     */
    public function removeReservationsForUser() {
        $this->execute("DELETE FROM ".self::RESERVATIONS_TABLE." WHERE UserId = ?", $this->getLoggedUserId());
    }

}