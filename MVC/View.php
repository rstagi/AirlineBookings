<?php
namespace MVC;

/**
 * Class View
 * @package MVC
 */
class View {
    const TEMPLATE = "Templates/Common/Template.html.php";
    private $model;
    private $template;
    private $title;

    /**
     * View constructor.
     * @param Model|null $model
     * @param $template
     */
    public function __construct (Model $model = null, $template = "", $title = "") {
        $this->model = $model;
        $this->template = $template;
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function render () {
        $headers = [];
        $model = $this->model;
        $title = $this->title;
        $template = $this->template;

        ob_start();
        require View::TEMPLATE;
        $content = ob_get_clean();

        return ['headers' => $headers, 'body' => $content];
    }
}