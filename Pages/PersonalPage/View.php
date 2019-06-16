<?php
namespace Pages\PersonalPage;

/**
 * Class View
 * @package PersonalPage
 */
class View extends \MVC\View {

    /**
     * View constructor.
     * @param Model $model
     */
    public function __construct (Model $model) {
        parent::__construct($model, 'Templates/Content/PersonalPage.html.php', 'Personal Page');
    }
}