<?php
namespace Homepage;

/**
 * Class Controller
 * @package Homepage
 */
class Controller extends \MVC\Controller {

    /**
     * Controller constructor.
     * @param \MVC\Model $model
     */
    public function __construct(\MVC\Model $model)
    {
        parent::__construct($model);
    }
}
