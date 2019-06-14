<?php

namespace MVC;

use mysqli;
use Throwable;

/**
 * Class ModelException
 * @package MVC
 */
class ModelException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Class Model
 * @package MVC
 */
class Model
{
    private $db;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        // Db Configuration
        require_once 'Global/config.php';

        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (mysqli_connect_errno())
        {
            die("Connect failed: <br>" .
                mysqli_connect_error());
        }
        $this->db = $db;
    }

    /**
     * @param $statement
     * @param mixed ...$params
     * @return \mysqli_result
     * @throws ModelException
     * @throws \ReflectionException
     */
    public function query($statement, ...$params) : \mysqli_result
    {

        $stmt = $this->prepareStatement($statement, $params);
        if (!$stmt->execute())
            throw new ModelException("Database Error");
        return $stmt->get_result();
    }

    /**
     * @param $statement
     * @param mixed ...$params
     * @return int
     * @throws ModelException
     * @throws \ReflectionException
     */
    public function execute($statement, ...$params) : int
    {
        $stmt = $this->prepareStatement($statement, $params);
        if (!$stmt->execute())
            throw new ModelException("Database Error");
        return $stmt->affected_rows;
    }

    /**
     * @param $statement
     * @param mixed ...$params
     * @return \mysqli_stmt
     * @throws ModelException
     * @throws \ReflectionException
     */
    private function prepareStatement($statement, ...$params) : \mysqli_stmt
    {
        if(! ($stmt = $this->db->prepare($statement)) )
            throw new ModelException("Database Error"); // query not printed on purpose: security issues

        if (sizeof($params)>0)
        {
            $paramsList = array();
            $paramTypes = "";
            foreach($params[0] as $k=>$p) {
                switch (gettype($p)) {
                    case 'string':
                        $p = $this->sanitize($p);
                        $paramTypes .= 's';
                        break;
                    case 'integer':
                        $paramTypes .= 'i';
                        break;
                    case 'double':
                        $paramTypes .= 'd';
                        break;
                    default:
                        throw new ModelException("Database error");
                }
                $paramsList[] = $p;
            }
            $stmt->bind_param($paramTypes, ...$paramsList);
        }

        return $stmt;
    }

    /**
     * @param string $input
     * @return string
     */
    public function sanitize(string $input) : string
    {
        return mysqli_real_escape_string($this->db, $input);
    }

    /**
     * @return bool
     */
    public function isUserLoggedIn() : bool
    {
        return false;
    }

    public function updateJWT() : void
    {

    }

    /**
     * Model destructor.
     */
    public function __destruct()
    {
        $this->db->close();
    }

}