<?php
namespace Pages\Homepage;

/**
 * Class Model
 * @package Pages\Homepage
 */
class Model extends \MVC\Model {

    /**
     * Model constructor.
     */
    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * @return array
     * @throws \MVC\Exceptions\ModelException
     */
    public function getReservedSeats () : array
    {
        $seats = [];
        $result = $this->query('SELECT * FROM Reservations
                                        WHERE SeatId NOT IN (
                                            SELECT SeatId FROM Purchases
                                        )');    // NOT IN added to prevent dirty data issues
        // (when bought, a seat should not be reserved anymore)
        while($row = $result->fetch_array()) {
            $seats[$row['SeatId']] = $row['UserId'];
        }
        $result->close();
        return $seats;
    }

    /**
     * @return array
     * @throws \MVC\Exceptions\ModelException
     */
    public function getBoughtSeats () : array
    {
        $seats = array();
        $result = $this->query('SELECT * FROM Purchases');
        while($row = $result->fetch_array()) {
            $seats[$row['SeatId']] = $row['UserId'];
        }
        $result->close();
        return $seats;
    }

    /**
     * @return array
     * @throws \MVC\Exceptions\ModelException
     */
    public function getNonFreeSeats () : array
    {
        $nonFreeSeats = array();
        $nonFreeSeats['reserved'] = $this->getReservedSeats();
        $nonFreeSeats['bought'] = $this->getBoughtSeats();
        return $nonFreeSeats;
    }

    /**
     * @return int
     * @throws \MVC\Exceptions\ModelException
     */
    public function getNumberOfBoughtSeats () : int
    {
        $result = parent::query('SELECT COUNT(*) FROM Purchases');
        $numberOfSeats = $result->fetch_array()[0];
        $result->close();
        return $numberOfSeats;
    }

    /**
     * @return int
     * @throws \MVC\Exceptions\ModelException
     */
    public function getNumberOfReservedSeats () : int
    {
        $result = $this->query('SELECT COUNT(*) FROM Reservations
                                        WHERE SeatId NOT IN (
                                            SELECT SeatId FROM Purchases
                                        )');    // NOT IN added to prevent dirty data issues
        // (when bought, a seat should not be reserved anymore)
        $numberOfSeats = $result->fetch_array()[0];
        $result->close();
        return $numberOfSeats;
    }

    /**
     * @return int
     * @throws \MVC\Exceptions\ModelException
     */
    public function getNumberOfFreeSeats () : int
    {
        return \Constants::COLS*\Constants::ROWS - $this->getNumberOfBoughtSeats() - $this->getNumberOfReservedSeats();
    }

    /**
     * @param string $seatId
     * @return bool
     * @throws \MVC\Exceptions\ModelException
     */
    public function isSeatBought(string $seatId) : bool
    {
        $result = $this->query("SELECT * FROM ".self::PURCHASES_TABLE." WHERE SeatId = ?", $seatId);

        $bought = false;
        if ($result->num_rows > 0)
            $bought = true;

        $result->close();

        return $bought;
    }
}