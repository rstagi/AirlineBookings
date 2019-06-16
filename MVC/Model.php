<?php

namespace MVC;

use mysqli;

/**
 * Class Model
 * @package MVC
 *
 * It uses MySQL db to store the data
 */
class Model
{
    /** Constants **/
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
     * @param bool $sshEnabled
     */
    protected function __construct(bool $sshEnabled = false)
    {
        // Db Configuration
        $db = new mysqli(\Constants::DB_HOST, \Constants::DB_USER, \Constants::DB_PASS, \Constants::DB_NAME);
        if (mysqli_connect_errno())
        {
            die("Connect failed: <br>" .
                mysqli_connect_error());
        }
        $this->db = $db;

        $this->error = null;
        $this->info = null;
        $this->success = null;

        if ($sshEnabled || $this->isUserLoggedIn()) self::enforceHTTPS();
    }

    /**
     * @param $statement
     * @param mixed ...$params
     * @return \mysqli_result
     * @throws Exceptions\ModelException
     */
    protected function query($statement, ...$params) : \mysqli_result
    {
        $stmt = $this->prepareStatement($statement, $params);
        if (!$stmt->execute())
            throw new Exceptions\ModelException("Database Error", 500);
        return $stmt->get_result();
    }

    /**
     * @param $statement
     * @param mixed ...$params
     * @return int
     * @throws Exceptions\ModelException
     */
    protected function execute($statement, ...$params) : int
    {
        $stmt = $this->prepareStatement($statement, $params);
        if (!$stmt->execute())
            throw new Exceptions\ModelException("Database Error", 500);
        return $stmt->affected_rows;
    }

    /**
     * @return void
     */
    protected function transactionBegin()
    {
        mysqli_autocommit($this->db, false);
    }

    /**
     * @return bool
     */
    protected function transactionRollback() : bool
    {
        $result = mysqli_rollback($this->db);
        mysqli_autocommit($this->db, true);
        return $result;
    }

    /**
     * @return bool
     */
    protected function transactionCommit() : bool
    {
        $result = mysqli_commit($this->db);
        mysqli_autocommit($this->db, true);
        return $result;
    }


    /**
     * @param $statement
     * @param mixed ...$params
     * @return \mysqli_stmt
     * @throws Exceptions\ModelException
     *
     * It dynamically prepares a correct statement, given
     */
    private function prepareStatement($statement, ...$params) : \mysqli_stmt
    {
        if(! ($stmt = $this->db->prepare($statement)) )
            throw new Exceptions\ModelException("Database Error", 500); // query not printed on purpose: security issues

        if (\Utils::isNonEmpty($params[0]))
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

    /**
     * @param $param
     * @return string
     * @throws Exceptions\ModelException
     *
     * Get param type of param, for the prepared statement
     */
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
                throw new Exceptions\ModelException("Database Error: Not recognized Object", 500);
        }
        return $paramType;
    }

    /**
     * @param $input
     * @return mixed
     *
     * Sanitizes the input before sending it to the db
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
     */
    public function isUserLoggedIn(bool $updatedToken = true) : bool
    {
        session_start();
        try {
            return (session_status() === PHP_SESSION_ACTIVE
                && \Utils::isNonEmpty($_SESSION[self::USER_ID_KEY])
                && $this->checkToken($updatedToken));
        }
        catch (Exceptions\ModelException $e) {
            return false;
        }
    }

    /**
     * @return int
     * @throws Exceptions\ModelException
     */
    public function getLoggedUserId() : int
    {
        session_start();
        if ( ! \Utils::isNonEmpty($_SESSION[self::USER_ID_KEY]) )
            throw new Exceptions\ModelException("User not logged in", 401);
        return $_SESSION[self::USER_ID_KEY];

    }

    /**
     * @return string
     * @throws Exceptions\ModelException
     */
    public function getLoggedUserEmail() : string
    {
        session_start();
        if ( ! \Utils::isNonEmpty($_SESSION[self::EMAIL_KEY]) )
            throw new Exceptions\ModelException("User not logged in", 401);
        return $_SESSION[self::EMAIL_KEY];

    }

    /**
     * @return bool
     */
    public function logout() : bool
    {
        $_SESSION[self::USER_ID_KEY] = null;
        $_SESSION[self::TOKEN_KEY] = null;
        return true;
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
     * @throws Exceptions\ModelException
     */
    public function checkToken(bool $update = true) : bool
    {
        $result = $this->query("SELECT Token, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Token_age)) Token_age FROM Users WHERE UserId=?",
                                        $this->getLoggedUserId());
        $result = $result->fetch_array();
        if ($result['Token_age'] > \Constants::TOKEN_TTL || $result['Token'] != $_SESSION[self::TOKEN_KEY])
            return false;

        if ($update)
        {
            $this->updateToken();
        }
        return true;
    }

    /**
     * @throws Exceptions\ModelException
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

    /**
     * Redirects to the https:// if not there
     */
    public static function enforceHTTPS()
    {
        if($_SERVER["HTTPS"] != "on") {
            header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
            exit();
        }
    }
}