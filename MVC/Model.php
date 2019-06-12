<?php

namespace MVC;

use mysqli;
class Model
{
    private $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function query()
    {

    }

    public function update()
    {

    }
}