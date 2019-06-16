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
            throw new ControllerException($this->model->getError(), 404);
    }

    /**
     * @param $email
     * @param $password
     * @throws ControllerException
     */
    public function register ($email, $password) {
        $error = "";

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $error .= '<br /><b>Invalid email</b>';

        if($this->model->userExists($email))
            $error .= '<br /><b>The email has already been used</b>';

        if (!preg_match($this->model->getPasswordRegex(), $password))
            $error .= '<br /><b>Invalid password</b>';

        if (!empty($error)) {
            $error = 'Some errors occurred while validating your registration:'.$error;
            throw new ControllerException($error, 400);
        }
        $this->model->register($email, $password);
    }
}