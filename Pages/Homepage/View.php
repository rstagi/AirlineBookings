<?php
namespace Pages\Homepage;

/**
 * Class View
 * @package Homepage
 */
class View extends \MVC\View {

    /**
     * View constructor.
     * @param Model $model
     */
    public function __construct (Model $model) {
        parent::__construct($model, 'Templates/Content/Homepage.html.php', 'Homepage');
    }

}