<?php
namespace Pages\Homepage;

/**
 * Class Controller
 * @package Homepage
 */
class Controller extends \MVC\Controller {

    /**
     * Controller constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }
}
