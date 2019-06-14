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
     * @param string $email
     * @return bool
     * @throws \MVC\ModelException
     * @throws \ReflectionException
     */
    public function emailExists(string $email) : bool
    {
        $result = parent::query("SELECT * FROM Users WHERE Email=?", $email);
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
        $result = parent::query("SELECT Password FROM Users WHERE Email=?", $email);

        if ($result->num_rows > 0 && password_verify($password, $result->fetch_array()['Password'])) {
            if (password_needs_rehash($password, SignIn::HASH_ALGORITHM))
                parent::execute("UPDATE Users SET Password=?", password_hash($password, SignIn::HASH_ALGORITHM));
        }  else
            return false;


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
        $result = parent::query("SELECT * FROM Users WHERE Email=?", $email);

        return $result->num_rows > 0;
    }

    /**
     * @param string $email
     * @param string $hashed
     * @throws \MVC\ModelException
     * @throws \ReflectionException
     */
    public function register(string $email, string $hashed)
    {
        // TODO check if it does already exist
        parent::execute("INSERT INTO Users (Email, Password) VALUES (?, ?);", $email, $hashed);
    }

    /**
     * @return string
     */
    public static function getPasswordRegex() {
        return SignIn::PASSWORD_REGEX;
    }
}