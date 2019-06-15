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
        $nonFreeSeats = $model->getNonFreeSeats();
        $boughtSeats = sizeOf($nonFreeSeats['bought']);
        $reservedSeats = sizeOf($nonFreeSeats['reserved']);
        $leftSeats = \Utils\SeatsUtils::calculateNumberOfFreeSeats($boughtSeats + $reservedSeats);
        ?>
        <p><b>Left: </b><?= $leftSeats ?> <b>Reserved: </b><?= $reservedSeats ?> <b>Bought: </b><?= $boughtSeats ?></p>

        <form class="controller" controller="PersonalPage" action="buy">
            <?php
            echo Utils\SeatsUtils::formatSeats(
                AirlineBookings\SeatsModel::COLS,
                AirlineBookings\SeatsModel::ROWS,
                $nonFreeSeats,
                true);
            ?>
            <div class="errorMessage alert alert-danger" role="alert" style="display: none"></div>
            <div class="successMessage alert alert-success" role="alert" style="display: none"></div>
        </form>

        <div class="mx-auto">
            <p>
                <b>Selected: </b>
                <span id="selectedSeats">0</span>
            </p>
            <p>
                <button type="submit" class="btn btn-primary">Buy</button>
            </p>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('img.seat').click(function() {
       if ($(this).hasClass('bought'))
           return;

       let targetCheckbox = $(':checkbox#seat'+$(this).attr('id'))[0];
       targetCheckbox.checked = !targetCheckbox.checked;

       $(targetCheckbox).trigger("change");
    });

    function reservationFailed(code) {
        if (code === 403) // Forbidden -> seat has been already bought
        {} // TODO set seat as bought
    }
</script>
<?php endif; ?>