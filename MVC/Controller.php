<?php
namespace MVC;

class Controller {
    protected $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }
}