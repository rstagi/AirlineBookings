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
     * logout is used by all the pages
     */
    public function logout() {
        $this->model->logout();
    }

}

