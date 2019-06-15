<?php


namespace AirlineBookings;


class SignIn extends \MVC\Model
{
    /**
     * @const HASH_ALGORITHM
     * Default password hash algorithm to safely store the password
     */
    private const HASH_ALGORITHM = PASSWORD_DEFAULT;

    /**
     * @const PASSWORD_REGEX
     * Passwords must contain at least one lower-case alphabetic character,
     * and at least one other character that is either alphabetical uppercase
     * or numeric.
     */
    private const PASSWORD_REGEX = "/[a-z]+(.*)[A-Z0-9]+/";

    /**
     * SignIn constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $email
     * @return bool
     * @throws \MVC\ModelException
     * @throws \ReflectionException
     */
    public function emailExists(string $email) : bool
    {
        $result = $this->query("SELECT * FROM Users WHERE Email=?", $email);
        return $result->num_rows() > 0;
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     * @throws \MVC\ModelException
     * @throws \ReflectionException
     */
    public function login(string $email, string $password) : bool
    {
        $result = $this->query("SELECT * FROM Users WHERE Email=?", $email);

        if ($result->num_rows < 1)
            return false;

        $res = $result->fetch_array();

        if (password_verify($password, $res['Password'])) {
            $token = $this->generateToken();
            if (password_needs_rehash($password, SignIn::HASH_ALGORITHM))
                $this->execute("UPDATE Users SET Password=?, Token=?, Token_age=NOW()",
                            password_hash($password, SignIn::HASH_ALGORITHM),
                            $token);
            else
                $this->execute("UPDATE Users SET Token=?, Token_age=NOW()", $token);

        }  else
            return false;

        session_start();
        $_SESSION[parent::USER_ID_KEY] = $res['UserId'];
        $_SESSION[parent::TOKEN_KEY] = $token;
        return true;
    }

    /**
     * @param string $email
     * @return bool
     * @throws \MVC\ModelException
     * @throws \ReflectionException
     */
    public function userExists(string $email) : bool
    {
        $result = $this->query("SELECT * FROM Users WHERE Email=?", $email);

        return $result->num_rows > 0;
    }

    /**
     * @param string $email
     * @param string $password
     * @throws \MVC\ModelException
     * @throws \ReflectionException
     */
    public function register(string $email, string $password)
    {
        if ($this->userExists($email)) throw new ModelException("User already exist");

        $this->execute("INSERT INTO Users (Email, Password) VALUES (?, ?);", $email, password_hash($password, SignIn::HASH_ALGORITHM));

        $this->login($email, $password);
    }

    /**
     * @return string
     */
    public static function getPasswordRegex() {
        return SignIn::PASSWORD_REGEX;
    }
}