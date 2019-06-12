<?php
namespace Homepage;

class View extends \MVC\View {
    public function __construct (\MVC\Model $model) {
        parent::__construct($model, 'Templates/Homepage.html.php');
    }

}