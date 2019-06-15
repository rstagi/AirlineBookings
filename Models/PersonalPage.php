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
        $reservationTable = \MVC\Model::RESERVATIONS_TABLE;
        $purchasesTable = \MVC\Model::PURCHASES_TABLE;
        $this->transactionBegin();
        $result = $this->query("SELECT * FROM $reservationTable WHERE SeatId = ? FOR UPDATE", $seatId);

        if ($result->num_rows > 0) {
            $rowsAffected = $this->execute("UPDATE $reservationTable SET UserId = ? WHERE SeatId = ?", $this->getLoggedUserId(), $seatId);
        } else {
            $rowsAffected = $this->execute( "INSERT INTO $reservationTable (SeatId, UserId) 
                                                       VALUES (?, ?)", $seatId, $this->getLoggedUserId());
        }

        $result = $this->query("SELECT * FROM $purchasesTable WHERE SeatId = ?", $seatId);
        if ($rowsAffected < 1 || $result->num_rows > 0) {
            $this->transactionRollback();
            return false;
        }

        $this->transactionCommit();
        return true;
    }

    /**
     * @param $seatId
     * @throws ModelException
     */
    public function freeSeat($seatId) {
       $this->execute("DELETE FROM ".\MVC\Model::RESERVATIONS_TABLE." WHERE SeatId = ? AND UserId = ?",
           $seatId, $this->getLoggedUserId());
    }

    /**
     * @param array $seats
     * @return bool
     * @throws ModelException
     */
    public function buySeats(array $seats) {
        $reservationTable = \MVC\Model::RESERVATIONS_TABLE;
        $purchasesTable = \MVC\Model::PURCHASES_TABLE;
        $userId = $this->getLoggedUserId();

        $this->transactionBegin();
        $seatsStr = implode("', '", $seats);
        $result = $this->query("SELECT * FROM $reservationTable
                                            WHERE SeatId IN [ ? ] 
                                            AND UserId = ? FOR DELETE",
                                            $seatsStr, $userId);

        if ($result->num_rows < sizeof($seats)) {
            $this->transactionRollback();
            return false;
        }

        $rowsAffected = 0;
        foreach($seats as $seatId) {
            $rowsAffected += $this->execute("INSERT INTO $purchasesTable (SeatId, UserId) 
                                                       VALUES (?, ?)", $seatId, $userId);
        }

        $result = $this->query("SELECT * FROM $purchasesTable WHERE SeatId = ?", $seatId);
        if ($rowsAffected < 1 || $result->num_rows > 0) {
            $this->transactionRollback();
            return false;
        }

        $this->transactionCommit();
        return true;
    }

    /**
     * @throws ModelException
     */
    public function removeReservationsForUser() {
        $this->execute("DELETE FROM ".\MVC\Model::RESERVATIONS_TABLE." WHERE UserId = ?", $this->getLoggedUserId());
    }
}