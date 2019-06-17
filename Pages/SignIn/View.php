<?php
namespace Pages\SignIn;

/**
 * Class View
 * @package Model
 */
class View extends \MVC\View {

    /**
     * View constructor.
     * @param Model $model
     */
    public function __construct (Model $model) {
        parent::__construct($model, 'Templates/Content/SignIn.html.php', 'Sign in');
    }
}