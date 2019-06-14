<?php


namespace MVC;

/**
 * Class Route
 * @package MVC
 */
class Route
{
    private $model;
    private $view;
    private $controller;

    /**
     * Route constructor.
     * @param $model
     * @param $view
     * @param $controller
     */
    public function __construct($model, $view, $controller) {
        $this->model = new $model();
        $this->view = new $view($this->model);
        $this->controller = new $controller($this->model);
    }

    /**
     * @return mixed
     */
    public function getModel() {
        return $this->moodel;
    }

    /**
     * @return mixed
     */
    public function getView() {
        return $this->view;
    }

    /**
     * @return mixed
     */
    public function getController() {
        return $this->controller;
    }
}