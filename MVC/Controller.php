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
    public function __construct(Model $model = null) {
        $this->model = $model;
    }

    /**
     *
     */
    public function logout() {
        $this->model->logout();
    }

}

