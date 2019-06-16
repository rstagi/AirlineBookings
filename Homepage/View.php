<?php
namespace Homepage;

/**
 * Class View
 * @package Homepage
 */
class View extends \MVC\View {

    /**
     * View constructor.
     * @param \MVC\Model $model
     */
    public function __construct (\MVC\Model $model) {
        parent::__construct($model, 'Templates/Homepage.html.php', 'Homepage');
    }

}