<?php
namespace PersonalPage;

use MVC\ControllerException;

/**
 * Class Controller
 * @package PersonalPage
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

    /**
     * @param string $seatId
     * @return bool
     * @throws ControllerException
     * @throws \MVC\ModelException
     */
    public function reserve (string $seatId) {
        if ( ! $this->model->isUserLoggedIn() )
            throw new ControllerException("User not logged in", 401);

        if ( $this->model->isSeatBought($seatId) )
            throw new ControllerException($this->model->getError(), 403);

        $this->model->reserveSeat($seatId);
        return true;
    }

    /**
     * @param array $seats
     * @return bool
     * @throws ControllerException
     * @throws \MVC\ModelException
     */
    public function buy (array $seats) {
        if ( ! $this->model->isUserLoggedIn() )
            throw new ControllerException("User not logged in", 401);

        if ( ! $this->model->buySeats($seats) ) {
            $this->model->removeReservationsForUser();
            throw new ControllerException($this->model->getError(), 409);
        }
        return true;
    }

    /**
     * @param $seatId
     * @return bool
     * @throws ControllerException
     * @throws \MVC\ModelException
     */
    public function free ($seatId) {
        if ( ! $this->model->isUserLoggedIn() )
            throw new ControllerException("User not logged in", 401);

        $this->model->freeSeat($seatId);
        return true;
    }
}