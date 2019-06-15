<div class="row">
    <!-- seats map -->
    <div id="seats-map" class="col text-center mx-auto">

        <h5 class="my-3">Seats map</h5>

        <?php
        $nonFreeSeats = $model->getNonFreeSeats();
        $boughtSeats = sizeOf($nonFreeSeats['bought']);
        $reservedSeats = sizeOf($nonFreeSeats['reserved']);
        $leftSeats = \Utils\SeatsUtils::calculateNumberOfFreeSeats($boughtSeats + $reservedSeats);
        ?>
        <p><b>Left: </b><?= $leftSeats ?> <b>Reserved: </b><?= $reservedSeats ?> <b>Bought: </b><?= $boughtSeats ?></p>

        <?php
        echo Utils\SeatsUtils::formatSeats(
            AirlineBookings\SeatsModel::COLS,
            AirlineBookings\SeatsModel::ROWS,
            $nonFreeSeats,
            false);
        ?>
        <p>If you would like to <b>reserve</b> or to <b>buy some seats</b>, you must be logged in!</p>
    </div>
</div>