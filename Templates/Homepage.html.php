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
        <div class="row">
            <div class="text-right col-4">
                <b>Left: </b><span id="leftSeats"><?= $leftSeats ?></span><br />
            </div>
            <div class="text-center col-4">
                <b>Reserved: </b><span id="reservedSeats"><?= $reservedSeats ?></span><br />
            </div>
            <div class="text-left col-4">
                <b>Bought: </b><span id="boughtSeats"><?= $boughtSeats ?></span><br />
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
        $('img.seat.selected').prop("src", "images/selected_seat.png");


        $('span#leftSeats').html($('img.seat.free').length);
        let numberOfSelected = $('img.seat.selected').length;
        $('span#reservedSeats').html(numberOfSelected + $('img.seat.reserved').length);
        $('span#boughtSeats').html($('img.seat.bought').length);
    });
</script>