<?php
namespace SignIn;

use MVC\ControllerException;

/**
 * Class Controller
 * @package SignIn
 */
class Controller extends \MVC\Controller {

    /**
     * Controller constructor.
     * @param \MVC\Model $model
     */
    public function __construct(\MVC\Model $model)
    {
        parent::__construct($model);
    }

    /**
     * @param $email
     * @param $password
     * @throws ControllerException
     */
    public function login ($email, $password) {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $error = '<li>Invalid email</li>';

        if (isset($error)) throw new ControllerException($error, 400);

        if(! $this->model->login($email, $password) )
            throw new ControllerException('Wrong email or password!', 404);
    }

    /**
     * @param $email
     * @param $password
     * @throws ControllerException
     */
    public function register ($email, $password) {
        $error = "";

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $error .= '<li>Invalid email</li>';

        if($this->model->userExists($email))
            $error .= '<li>The email has already been used</li>';

        if (!preg_match($this->model->getPasswordRegex(), $password))
            $error .= '<li>Invalid password</li>';

        if (!empty($error))
            throw new ControllerException('<ul>'.$error.'</ul>', 400);

        $this->model->register($email, $this->model->hash($password));
    }
}