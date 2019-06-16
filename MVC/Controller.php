<?php
namespace MVC;

use Throwable;

/**
 * Class Controller
 * @package MVC
 */
class Controller {
    protected $model;

    /**
     * Controller constructor.
     * @param Model $model
     */
    public function __construct(Model $model) {
        $this->model = $model;
    }

    /**
     *
     */
    public function logout() {
        $this->model->logout();
    }

}

class ControllerException extends \Exception {
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
