<?php
namespace MVC;
class View {
    private $model;
    private $template;

    public function __construct (?Model $model, $template) {
        $this->model = $model;
        $this->template = $template;
    }

    public function render () {
        $headers = [];
        $model = $this->model;

        ob_start();
        require $this->template;
        $content = ob_get_clean();

        return ['headers' => $headers, 'body' => $content];
    }
}