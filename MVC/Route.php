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
     * @return Model
     */
    public function getModel()
    {
        return $this->moodel;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return bool
     */
    public function hasModel() : bool
    {
        return $this->model != null;
    }

    /**
     * @return bool
     */
    public function hasController() : bool
    {
        return $this->controller != null;
    }

    /**
     * @return bool
     */
    public function hasView() : bool
    {
        return $this->view != null;
    }
}