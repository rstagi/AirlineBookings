<?php

namespace MVC;

use mysqli;

class ModelException extends \Exception
{
    public function __construct($msg)
    {
        parent::__construct($msg);
    }
}

class Model
{
    private $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function query($query)
    {
        if(!($result = $this->db->query($query)))
            throw new ModelException("Error in query: ".$query);

        return $result;
    }


    public function execute($statement)
    {
        $stmnt = $this->db->prepare($statement);
        $stmnt->execute();
    }

    public function __destruct()
    {
        $this->db->close();
    }
}