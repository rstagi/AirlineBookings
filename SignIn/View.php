<?php
namespace SignIn;

/**
 * Class View
 * @package SignIn
 */
class View extends \MVC\View{

    /**
     * View constructor.
     * @param \MVC\Model $model
     */
    public function __construct (\MVC\Model $model) {
        parent::__construct($model, 'Templates/SignIn.html.php', 'Sign in');
    }
}