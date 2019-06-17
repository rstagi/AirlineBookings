<?php


namespace MVC\Core;

/**
 * Class Route
 * @package MVC
 *
 * Result of the routing operation. Contains model, view and controller
 */
class Route
{
    private $model;
    private $view;
    private $controller;

    /**
     * Route constructor.
     * @param Model|null $model
     * @param View|null $view
     * @param Controller|null $controller
     */
    public function __construct(\MVC\Model $model = null, \MVC\View $view, \MVC\Controller $controller) {
        $this->model = $model;
        $this->view = $view;
        $this->controller = $controller;
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
    public function getView() : \MVC\View
    {
        return $this->view;
    }

    /**
     * @return Controller
     */
    public function getController() : \MVC\Controller
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

}