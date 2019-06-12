<?php
namespace PersonalPage;

class View extends \MVC\View{

    public function __construct (\MVC\Model $model) {
        parent::__construct($model, 'Templates/PersonalPage.html.php');
    }
}