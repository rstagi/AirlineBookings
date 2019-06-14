<?php


namespace AirlineBookings;

/**
 * Class SeatsModel
 * @package AirlineBookings
 */
class SeatsModel extends \MVC\Model
{
    const COLS = 6, ROWS = 10;

    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * @return array
     * @throws \MVC\ModelException
     */
    public function getReservedSeats () : array
    {
        $seats = [];
        $result = parent::query('SELECT SeatId FROM Reservations');
        while($row = $result->fetch_array())
            $seats[$row['letter']][$row['number']] = "reserved";
        $result->close();
        return $seats;
    }

    /**
     * @return array
     * @throws \MVC\ModelException
     */
    public function getBoughtSeats () : array
    {
        $seats = [];
        $result = parent::query('SELECT SeatId FROM Purchases');
        while($row = $result->fetch_array())
            $seats[$row['letter']][$row['number']] = "bought";
        $result->close();
        return $seats;
    }

    /**
     * @return array
     * @throws \MVC\ModelException
     */
    public function getNonFreeSeats () : array
    {
        return $this->getReservedSeats() + $this->getBoughtSeats();
    }

    /**
     * @return int
     * @throws \MVC\ModelException
     */
    public function getNumberOfBoughtSeats () : int
    {
        $result = parent::query('SELECT COUNT(*) FROM Purchases');
        $numberOfSeats = $result->fetch_array()[0];
        return $numberOfSeats;
    }

    /**
     * @return int
     * @throws \MVC\ModelException
     */
    public function getNumberOfReservedSeats () : int
    {
        $result = parent::query('SELECT COUNT(*) FROM Reservations
                                        WHERE SeatId NOT IN (
                                            SELECT SeatId FROM Purchases
                                        )');    // NOT IN added to prevent dirty data issues
                                                // (when bought, a seat should not be reserved anymore)
        $numberOfSeats = $result->fetch_array()[0];
        return $numberOfSeats;
    }

    /**
     * @return int
     * @throws \MVC\ModelException
     */
    public function getNumberOfFreeSeats () : int
    {
        return COLS*ROWS - $this->getNumberOfBoughtSeats() - $this->getNumberOfReservedSeats();
    }
}