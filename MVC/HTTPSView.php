<?php


namespace MVC;


class HTTPSView extends View
{
    /**
     * HTTPSView constructor.
     * @param Model|null $model
     * @param $template
     * @param string $title
     */
    public function __construct (Model $model = null, $template = "", $title = "") {
        parent::__construct($model, $template, $title);
    }

    /**
     * @return array
     */
    public function render () {
        HTTPSView::enforceHTTPS();

        return parent::render();
    }

    /**
     *
     */
    public static function enforceHTTPS()
    {
        if($_SERVER["HTTPS"] != "on")
        {
            header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
            exit();
        }
    }
}