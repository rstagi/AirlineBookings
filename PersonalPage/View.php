<?php
namespace PersonalPage;

/**
 * Class View
 * @package PersonalPage
 */
class View extends \MVC\HTTPSView {

    /**
     * View constructor.
     * @param \MVC\Model $model
     */
    public function __construct (\MVC\Model $model) {
        parent::__construct($model, 'Templates/PersonalPage.html.php', 'Personal Page');
    }
}