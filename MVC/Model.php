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
    /** Constants **/
    const TOKEN_TTL = 120; //secs
    const USER_ID_KEY = "UserId";
    const TOKEN_KEY = "Token";
    const EMAIL_KEY = "Email";
    const PURCHASES_TABLE = "Purchases";
    const RESERVATIONS_TABLE = "Reservations";
    const USERS_TABLE = "Users";

    private $db;
    protected $error;   // error messages to show in the view
    protected $info;    // info messages
    protected $success; // success messages

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

        $this->error = null;
        $this->info = null;
        $this->success = null;
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
        mysqli_autocommit($this->db, true);
    }

    /**
     *
     */
    protected function transactionCommit()
    {
        mysqli_commit($this->db);
        mysqli_autocommit($this->db, true);
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
                $paramsList[] = $this->sanitize($p);
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
            && \Utils\AirlineBookingsUtils::isNonEmpty($_SESSION[self::USER_ID_KEY])
            && $this->checkToken($updatedToken));
    }

    /**
     * @return int
     * @throws ModelException
     */
    public function getLoggedUserId() : int
    {
        session_start();
        if ( ! \Utils\AirlineBookingsUtils::isNonEmpty($_SESSION[self::USER_ID_KEY]) )
            throw new ModelException("User not logged in", 401);
        return $_SESSION[self::USER_ID_KEY];

    }

    /**
     * @return string
     * @throws ModelException
     */
    public function getLoggedUserEmail() : string
    {
        session_start();
        if ( ! \Utils\AirlineBookingsUtils::isNonEmpty($_SESSION[self::EMAIL_KEY]) )
            throw new ModelException("User not logged in", 401);
        return $_SESSION[self::EMAIL_KEY];

    }

    /**
     *
     */
    public function logout()
    {
        $_SESSION[self::USER_ID_KEY] = null;
        $_SESSION[self::TOKEN_KEY] = null;
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
        if ($result['Token_age'] > self::TOKEN_TTL || $result['Token'] != $_SESSION[self::TOKEN_KEY])
            return false;

        if ($update)
        {
            $this->updateToken();
        }
        return true;
    }

    /**
     * @throws ModelException
     */
    protected function updateToken()
    {
        $newToken = $this->generateToken();
        if ($this->execute("UPDATE Users SET Token=?, Token_age=NOW() WHERE UserId=?", $newToken, $this->getLoggedUserId()) )
            $_SESSION[self::TOKEN_KEY] = $newToken;
    }

    /**
     * @return string
     */
    public function getError() : string
    {
        return ($this->error) ?? "";
    }

    /**
     * @return string
     */
    public function getInfo() : string
    {
        return ($this->info) ?? "";
    }

    /**
     * @return string
     */
    public function getSuccess() : string
    {
        return ($this->success) ?? "";
    }

    /**
     * Model destructor.
     */
    public function __destruct()
    {
        $this->db->close();
    }

}