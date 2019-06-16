<?php


namespace Pages\SignIn;

use \MVC\Exceptions as MVCEE;


class Model extends \MVC\Model
{
    /**
     * @const HASH_ALGORITHM
     * Default password hash algorithm to safely store the password
     */
    const HASH_ALGORITHM = PASSWORD_DEFAULT;

    /**
     * @const PASSWORD_REGEX
     * Passwords must contain at least one lower-case alphabetic character,
     * and at least one other character that is either alphabetical uppercase
     * or numeric.
     */
    const PASSWORD_REGEX = "/(?=.*[a-z])(?=.*[A-Z0-9]).+/";

    /**
     * Model constructor.
     */
    public function __construct()
    {
        parent::__construct(true);
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     * @throws \MVC\Exceptions\ModelException
     */
    public function login(string $email, string $password) : bool
    {
        $result = $this->query("SELECT * FROM Users WHERE Email=?", $email);

        if ($result->num_rows < 1) {
            $this->error = "Wrong email or passwords.";
            return false;
        }

        $res = $result->fetch_array();

        if (password_verify($password, $res['Password'])) {
            $token = $this->generateToken();
            $this->execute("UPDATE Users SET Token=?, Token_age=NOW() WHERE UserId=?",
                                            $token, $res['UserId']);
        }  else {
            $this->error = "Wrong email or passwords.";
            return false;
        }

        session_start();
        $_SESSION[parent::USER_ID_KEY] = $res['UserId'];
        $_SESSION[parent::TOKEN_KEY] = $token;
        $_SESSION[parent::EMAIL_KEY] = $email;
        $this->success = "Successfully logged in!";
        return true;
    }

    /**
     * @param string $email
     * @return bool
     * @throws \MVC\Exceptions\ModelException
     */
    public function userExists(string $email) : bool
    {
        $result = $this->query("SELECT * FROM Users WHERE Email=?", $email);

        return $result->num_rows > 0;
    }

    /**
     * @param string $email
     * @param string $password
     * @throws \MVC\Exceptions\ModelException
     */
    public function register(string $email, string $password)
    {
        if ($this->userExists($email)){
            $this->error = "User already exist.";
            throw new MVCEE\ModelException("User already exist");
        }

        $this->execute("INSERT INTO Users (Email, Password) VALUES (?, ?);", $email, password_hash($password, Model::HASH_ALGORITHM));

        $this->login($email, $password);
        $this->success = "Successfully registered and logged in!";
    }

    /**
     * @return string
     */
    public static function getPasswordRegex() {
        return Model::PASSWORD_REGEX;
    }
}