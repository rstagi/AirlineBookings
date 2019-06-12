<?php
namespace AirlineBookings;

class Homepage extends \MVC\Model {

    public function __construct (\mysqli $db)
    {
        parent::__construct($db);
    }

    public function getReservedSeats () : array
    {
        $seats = [];
        $result = parent::query("SELECT * FROM ReservedSeats R");
        while($row = $result->fetch_array())
            $seats[$row['letter']][$row['number']] = "reserved";
        $result->close();
        return $seats;
    }

    public function getBoughtSeats () : array
    {
        $seats = [];
        $result = parent::query("SELECT * FROM BoughtSeats");
        while($row = $result->fetch_array())
            $seats[$row['letter']][$row['number']] = "bought";
        $result->close();
        return $seats;
    }

    public function getNonFreeSeats () : array
    {
        return $this->getReservedSeats() + $this->getBoughtSeats();
    }
}