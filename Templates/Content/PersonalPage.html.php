<?php
if (!$model->isUserLoggedIn()):
    $headers[] = 'refresh:0;url=./?page=401Unauthorized';
    $headers[] = 'HTTP/1.1 401 Unauthorized';
else:
?>
<div class="row">
    <div class="col p-3 text-center">
        <h5>Hi, <?= $model->getLoggedUserEmail() ?></h5>
        Here you can choose, reserve and buy some flight seats. Enjoy!
    </div>
</div>
<div class="row">
    <!-- seats map -->
    <div id="seats-map" class="col text-center mx-auto">

        <div class="row my-3">
            <div class="col text-center">
                <button class="btn btn-sm btn-secondary small" onclick="location.reload();">Update</button><br/>
                <h5>Seats map</h5>
            </div>
        </div>

        <?php
            $boughtSeats = $model->getNumberOfBoughtSeats();
            $selectedSeats = $model->getNumberOfSelectedSeats();
            $reservedSeats = $model->getNumberOfReservedSeats();
            $freeSeats = \Utils::calculateNumberOfFreeSeats($boughtSeats + $reservedSeats);
        ?>
        <div class="row">
            <div class="text-center col-3">
                <img src="images/free_seat.png" class="seat-legend" /><br />
                <b>Free: </b><span id="freeSeats"><?= $freeSeats ?></span>
            </div>
            <div class="text-center col-3">
                <img src="images/reserved_seat.png" class="seat-legend" /><br />
                <b>Reserved: </b><span id="reservedSeats"><?= $reservedSeats ?></span>
            </div>
            <div class="text-center col-3">
                <img src="images/bought_seat.png" class="seat-legend" /><br />
                <b>Bought: </b><span id="boughtSeats"><?= $boughtSeats ?></span>
            </div>
            <div class="text-center col-3">
                <img src="images/selected_seat.png" class="seat-legend" /><br />
                <b>Selected: </b><span id="selectedSeats"><?= $selectedSeats ?></span>
            </div>
        </div>
        <form class="controller" controller="PersonalPage" action="buy">
            <?php
            echo \Utils::formatSeats($model,true);
            ?>

            <button type="submit" class="btn btn-lg btn-primary" id="buySeatsBtn">Buy</button>

            <div class="errorMessage alert alert-danger alert-dismissible alert-overlay my-3" role="alert"
                 style="display:<?= $model->getError()!="" ? 'block' : 'none' ?>">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="$('.errorMessage').hide()">
                    <span aria-hidden="true">&times;</span>
                </button>
                <p class="alert-content"><?= $model->getError(); ?></p>
            </div>
            <div class="successMessage alert alert-success alert-dismissible alert-overlay my-3" role="alert"
                 style="display:<?= $model->getSuccess()!="" ? 'block' : 'none' ?>">
                <button type="button" class="close" aria-label="Close" onclick="$('.successMessage').hide()">
                    <span aria-hidden="true">&times;</span>
                </button>
                <p class="alert-content"><?= $model->getSuccess(); ?></p>
            </div>
        </form>
        <div id="inset-form" hidden></div>

    </div>
</div>

<!-- Project JS -->
<script type="text/javascript" src="MVC/API/js/asyncDispatcher.js"></script>
<script type="text/javascript" src="js/personalPageScripts.js"></script>
<?php endif; ?>