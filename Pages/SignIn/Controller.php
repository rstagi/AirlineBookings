<?php
namespace Pages\SignIn;

use MVC\Exceptions as MVCE;

/**
 * Class Controller
 * @package Model
 */
class Controller extends \MVC\Controller {

    /**
     * Controller constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    /**
     * @param $email
     * @param $password
     * @throws MVCE\ControllerException
     */
    public function login ($email, $password) {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $error = '<li>Invalid email</li>';

        if (isset($error)) throw new ControllerException($error, 400);

        if(! $this->model->login($email, $password) )
            throw new MVCE\ControllerException($this->model->getError(), 404);
    }

    /**
     * @param $email
     * @param $password
     * @throws MVCE\ControllerException
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
            throw new MVCE\ControllerException($error, 400);
        }
        $this->model->register($email, $password);
    }
}