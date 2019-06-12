<?php


namespace MVC;


class Route
{
    private $__model;
    private $__view;
    private $__controller;

    public function __construct($model, $view, $controller) {
        $this->__model = $model;
        $this->__view = $view;
        $this->__controller = $controller;
    }

    public function getModel(\mysqli $db) {
        return new $this->__model($db);
    }

    public function getView(Model $model) {
        return new $this->__view($model);
    }

    public function getController(Model $model) {
        return new $this->__controller($model);
    }
}