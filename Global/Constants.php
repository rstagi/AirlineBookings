<?php

require_once 'dbconfig.php';

abstract class Constants
{
    /** @var int ROWS rows of seat map */
    const ROWS = 10;
    /** @var int COLS columns of seat map */
    const COLS = 6;

    /** @var int TTL login token expiration time */
    const TOKEN_TTL = 120; //secs

    /** DB connection constants */
    /** @var string database host */
    const DB_HOST = DB_HOST;
    /** @var string database user */
    const DB_USER = DB_USER;
    /** @var string database password */
    const DB_PASS = DB_PASS;
    /** @var string database name */
    const DB_NAME = DB_NAME;

}