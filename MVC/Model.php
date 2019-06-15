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
    public const TOKEN_TTL = 120; //secs
    protected const USER_ID_KEY = "UserId";
    protected const TOKEN_KEY = "Token";
    protected const TOKEN_AGE_KEY = "Token_age";
    protected const PURCHASES_TABLE = "Purchases";
    protected const RESERVATIONS_TABLE = "Reservations";
    protected const USERS_TABLE = "Users";

    /**
     * Model constructor.
     */
    protected function __construct()
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
     */
    protected function query($statement, ...$params) : \mysqli_result
    {

        $stmt = $this->prepareStatement($statement, $params);
        if (!$stmt->execute())
            throw new ModelException("Database Error", 500);
        return $stmt->get_result();
    }

    /**
     * @param $statement
     * @param mixed ...$params
     * @return int
     * @throws ModelException
     */
    protected function execute($statement, ...$params) : int
    {
        $stmt = $this->prepareStatement($statement, $params);
        if (!$stmt->execute())
            throw new ModelException("Database Error", 500);
        return $stmt->affected_rows;
    }

    /**
     *
     */
    protected function transactionBegin()
    {
        mysqli_autocommit($this->db, false);
    }

    /**
     *
     */
    protected function transactionRollback()
    {
        mysqli_rollback($this->db);
    }

    /**
     *
     */
    protected function transactionCommit()
    {
        mysqli_commit($this->db);
    }


    /**
     * @param $statement
     * @param mixed ...$params
     * @return \mysqli_stmt
     * @throws ModelException
     */
    private function prepareStatement($statement, ...$params) : \mysqli_stmt
    {
        if(! ($stmt = $this->db->prepare($statement)) )
            throw new ModelException("Database Error", 500); // query not printed on purpose: security issues

        if (\Utils\AirlineBookingsUtils::isNonEmpty($params[0]))
        {
            $paramsList = array();
            $paramTypes = "";
            foreach($params[0] as $p) {
                $paramTypes .= $this->getParamType($p);
                $sanitizedValue = $this->sanitize($p);
                if (gettype($sanitizedValue) == 'array')
                    $paramsList = array_merge($paramsList, $sanitizedValue);
                else
                    $paramsList[] = $sanitizedValue;
            }
            $stmt->bind_param($paramTypes, ...$paramsList);
        }

        return $stmt;
    }

    private function getParamType($param) : string
    {
        $paramType = '';
        switch(gettype($param))
        {
            case 'string':
                $paramType .= 's';
                break;
            case 'integer':
                $paramType .= 'i';
                break;
            case 'double':
                $paramType .= 'd';
                break;
            case 'array':
                $paramType .= str_repeat(getParamType($param[0]), sizeof($param));
                break;
            default:
                throw new ModelException("Database Error: Not recognized Object", 500);
        }
        return $paramType;
    }

    /**
     * @param $input
     * @return mixed
     */
    protected function sanitize($input)
    {
        if (gettype($input) == 'string')
            return mysqli_real_escape_string($this->db, $input);
        else if (gettype($input) == 'array' && gettype($input[0]) == 'string') {
            $arr = array();
            foreach($input as $str)
                $arr[] = $this->sanitize($str);
            return $arr;
        }
        return $input;
    }

    /**
     * @param bool $updatedToken
     * @return bool
     * @throws ModelException
     */
    public function isUserLoggedIn(bool $updatedToken = true) : bool
    {
        session_start();
        return (session_status() === PHP_SESSION_ACTIVE
            && \Utils\AirlineBookingsUtils::isNonEmpty($_SESSION[Model::USER_ID_KEY])
            && $this->checkToken($_SESSION[Model::TOKEN_KEY], $updatedToken));
    }

    public function getLoggedUserId() : int
    {
        if ( ! \Utils\AirlineBookingsUtils::isNonEmpty($_SESSION[Model::USER_ID_KEY]) )
            throw new ModelException("User not logged in", 401);
        return $_SESSION[Model::USER_ID_KEY];

    }

    /**
     *
     */
    public function logout() : void
    {
        $_SESSION[Model::USER_ID_KEY] = null;
        $_SESSION[Model::TOKEN_KEY] = null;
        $_SESSION[Model::TOKEN_AGE_KEY] = null;
    }

    /**
     * @return string
     */
    protected function generateToken() : string
    {
        return uniqid();
    }

    /**
     * @param bool $update
     * @return bool
     * @throws ModelException
     */
    public function checkToken(bool $update = true) : bool
    {
        $result = $this->query("SELECT Token, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Token_age)) Token_age FROM Users WHERE UserId=?",
                                        $this->getLoggedUserId());
        $result = $result->fetch_array();
        if ($result['Token_age'] > Model::TOKEN_TTL || $result['Token'] != $_SESSION[Model::TOKEN_KEY])
            return false;

        if ($update)
        {
            $newToken = $this->generateToken();
            $this->execute("UPDATE Users SET Token=?, Token_age=NOW()", $newToken);
            $_SESSION[Model::TOKEN_KEY] = $newToken;
        }
        return true;
    }

    /**
     * Model destructor.
     */
    public function __destruct()
    {
        $this->db->close();
    }

}