<?php
namespace MVC;

/**
 * Class View
 * @package MVC
 */
class View {
    const TEMPLATE = "Templates/Layout/Template.html.php";
    private $model;
    private $template;
    private $title;

    /**
     * View constructor.
     * @param Model|null $model
     * @param string $template
     * @param string $title
     */
    public function __construct (Model $model = null, $template = "", $title = "") {
        $this->model = $model;
        $this->template = $template;
        $this->title = $title;
    }

    /**
     * @return array
     *
     * renders the given template
     */
    public function render () {
        $headers = [];
        $model = $this->model;
        $title = $this->title;
        $template = $this->template;

        ob_start();
        require self::TEMPLATE;
        $content = ob_get_clean();

        return ['headers' => $headers, 'body' => $content];
    }
}