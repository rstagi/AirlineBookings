<?php
if (!$model->isUserLoggedIn()):
    $headers[] = 'refresh:0;url=./?page=401Unauthorized';
    $headers[] = 'HTTP/1.1 401 Unauthorized';
else:
?>
<div class="row">
    <!-- seats map -->
    <div id="seats-map" class="col text-center mx-auto">

        <h5 class="my-3">Seats map</h5>

        <?php
        $boughtSeats = $model->getNumberOfBoughtSeats();
        $reservedSeats = $model->getNumberOfReservedSeats();
        $leftSeats = \Utils\SeatsUtils::calculateNumberOfFreeSeats($boughtSeats + $reservedSeats);
        ?>
        <p><b>Left: </b><?= $leftSeats ?> <b>Reserved: </b><?= $boughtSeats ?> <b>Bought: </b><?= $reservedSeats ?></p>

        <form class="controller" action="buy">
        <?php
        echo Utils\SeatsUtils::formatSeats(
            AirlineBookings\SeatsModel::COLS,
            AirlineBookings\SeatsModel::ROWS,
            $model->getNonFreeSeats(),
            true);
        ?>
        </form>

        <div class="mx-auto">
            <p>
                <b>Selected: </b>
                <span id="selectedSeats">0</span>
            </p>
            <p>
                <button type="submit"></button>
            </p>
        </div>
    </div>
</div>

<?php endif; ?>