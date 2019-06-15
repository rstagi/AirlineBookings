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
            $selectedSeats = $model->getNumberOfSelectedSeats();
            $reservedSeats = $model->getNumberOfReservedSeats() - $selectedSeats;
            $leftSeats = \Utils\SeatsUtils::calculateNumberOfFreeSeats($boughtSeats + $reservedSeats + $selectedSeats);
        ?>
        <div class="row">
            <div class="text-right col-4">
                <b>Left: </b><span id="freeSeats"><?= $leftSeats ?></span><br />
            </div>
            <div class="text-center col-4">
                <b>Reserved: </b><span id="reservedSeats"><?= $reservedSeats ?></span><br />
            </div>
            <div class="text-left col-4">
                <b>Bought: </b><span id="boughtSeats"><?= $boughtSeats ?></span><br />
            </div>
        </div>
        <form class="controller" controller="PersonalPage" action="buy" onFailure="buyFailure()">
            <?php
            echo Utils\SeatsUtils::formatSeats($model,true);
            ?>
            <div class="errorMessage alert alert-danger" role="alert" style="display: none"></div>
            <div class="successMessage alert alert-success" role="alert" style="display: none"></div>
            <div class="row">
                <div class="col text-center">
                    <p>
                        <b>Selected: </b>
                        <span id="selectedSeats"><?= $selectedSeats ?></span><br />
                        <button type="submit" class="btn btn-primary" id="buySeatsBtn">Buy</button>
                    </p>
                    <span class="btn btn-secondary cursor-pointer" onclick="location.reload()">Update</span>
                </div>
            </div>
        </form>

    </div>
</div>

<script type="text/javascript">
    function updateSeats() {
        $(':checkbox.seat').removeClass('triggerAction');
        $(':checkbox.seat').attr('checked', 'false');
        $(':checkbox.seat').attr('list', 'true');
        $(':checkbox.seat').attr('action', 'reserve');
        $(':checkbox.seat').attr('groupName', 'seats');
        $(':checkbox.seat').attr('onFailure', 'reserveFailed');
        $(':checkbox.seat').attr('onSuccess', 'reserveSucceed');

        $(':checkbox.seat.selected').prop('checked', 'true');
        $(':checkbox.seat.selected').attr('action', 'free');
        $(':checkbox.seat.selected').attr('onFailure', '');
        $(':checkbox.seat.selected').attr('onSuccess', 'freeSucceed');
        $(':checkbox.seat').addClass('triggerAction');

        $('img.seat').addClass('cursor-pointer');
        $('img.seat.free').prop("src", "images/free_seat.png");
        $('img.seat.selected').prop("src", "images/selected_seat.png");
        $('img.seat.reserved').prop("src", "images/reserved_seat.png");
        $('img.seat.bought').prop("src", "images/bought_seat.png");
        $('img.seat.bought').removeClass('cursor-pointer');


        $('span#leftSeats').html($(':checkbox.seat.free').length);
        $('span#reservedSeats').html($(':checkbox.seat.reserved').length);
        $('span#boughtSeats').html($('img.seat.bought').length);
        let numberOfSelected = $(':checkbox.seat.selected').length;
        $('span#selectedSeats').html(numberOfSelected);
        if (numberOfSelected < 1) $('#buySeatsBtn').prop('disabled', true)
        else $('#buySeatsBtn').prop('disabled', false);
    }

    $(document).ready(function () {
        updateSeats();
    });

    $('img.seat').click(function() {
       if ($(this).hasClass('bought'))
           return;

       let targetCheckbox = $(':checkbox#seat'+$(this).attr('id'))[0];
       targetCheckbox.checked = !targetCheckbox.checked;

       $(targetCheckbox).trigger("change");
    });

    function reserveFailed(id, code) {
        if (code === 403) // Forbidden -> seat has been already bought
        {
            $('#'+id).removeClass('free');
            $('#'+id).removeClass('selected');
            $('#'+id).addClass('bought');
            $('#'+id.substr(4)).removeClass('free');
            $('#'+id.substr(4)).removeClass('selected');
            $('#'+id.substr(4)).addClass('bought');
            updateSeats();
        }
    }

    function buyFailed() {
        $(':checkbox.seat.selected').prop('checked', 'false').trigger("change");
    }

    function reserveSucceed(id) {
        $('#'+id).removeClass('free');
        $('#'+id).removeClass('reserved');
        $('#'+id).addClass('selected');
        $('#'+id.substr(4)).removeClass('free');
        $('#'+id.substr(4)).removeClass('reserved');
        $('#'+id.substr(4)).addClass('selected');
        updateSeats();
    }

    function freeSucceed(id) {
        $('#'+id).removeClass('selected');
        $('#'+id).addClass('free');
        $('#'+id.substr(4)).removeClass('selected');
        $('#'+id.substr(4)).addClass('free');
        updateSeats();
    }

</script>
<?php endif; ?>