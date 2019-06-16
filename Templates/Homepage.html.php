<div class="row">
    <!-- seats map -->
    <div id="seats-map" class="col text-center mx-auto">

        <h5 class="my-3">Seats map</h5>

        <?php
        $nonFreeSeats = $model->getNonFreeSeats();
        $boughtSeats = sizeOf($nonFreeSeats['bought']);
        $reservedSeats = sizeOf($nonFreeSeats['reserved']);
        $freeSeats = \Utils\SeatsUtils::calculateNumberOfFreeSeats($boughtSeats + $reservedSeats);
        ?>
        <div class="row">
            <div class="text-center col-4">
                <img src="images/free_seat.png" class="seat-legend" /><br />
                <b>Free: </b><span id="freeSeats"><?= $freeSeats ?></span>
            </div>
            <div class="text-center col-4">
                <img src="images/reserved_seat.png" class="seat-legend" /><br />
                <b>Reserved: </b><span id="reservedSeats"><?= $reservedSeats ?></span>
            </div>
            <div class="text-center col-4">
                <img src="images/bought_seat.png" class="seat-legend" /><br />
                <b>Bought: </b><span id="boughtSeats"><?= $boughtSeats ?></span>
            </div>
        </div>
        <?php
        echo Utils\SeatsUtils::formatSeats($model);

        if (!$model->isUserLoggedIn()): ?>
            <p>If you would like to <b>reserve</b> or to <b>buy some seats</b>, you must be <a href="./?page=SignIn">logged in</a>!</p>
        <?php else: ?>
            <p>If you would like to <b>reserve</b> or to <b>buy some seats</b>, you should go to your <a href="./?page=PersonalPage">Personal Page</a>.</p>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('img.seat.reserved').prop("src", "images/reserved_seat.png");
        $('img.seat.bought').prop("src", "images/bought_seat.png");
        $('img.seat.free').prop("src", "images/free_seat.png");

        $('span#freeSeats').html($('img.seat.free').length);
        $('span#reservedSeats').html($('img.seat.reserved').length);
        $('span#boughtSeats').html($('img.seat.bought').length);
    });
</script>