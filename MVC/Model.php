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
     */
    private function prepareStatement($statement, ...$params) : \mysqli_stmt
    {
        if(! ($stmt = $this->db->prepare($statement)) )
            throw new ModelException("Database Error"); // query not printed on purpose: security issues

        if (\Utils\AirlineBookingsUtils::isNonEmpty($params[0]))
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
     * @param bool $updatedToken
     * @return bool
     * @throws ModelException
     * @throws \ReflectionException
     */
    public function isUserLoggedIn(bool $updatedToken = true) : bool
    {
        session_start();
        return (session_status() === PHP_SESSION_ACTIVE
            && \Utils\AirlineBookingsUtils::isNonEmpty($_SESSION[Model::USER_ID_KEY])
            && $this->checkToken($_SESSION[Model::TOKEN_KEY], $updatedToken));
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
    public function generateToken() : string
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
        $result = $this->query("SELECT Token, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Token_age)) Token_age FROM Users WHERE UserId=?", (int)$_SESSION['UserId']);
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