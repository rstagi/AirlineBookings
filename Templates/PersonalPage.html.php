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
                <button class="btn btn-sm btn-secondary small" onclick="location.reload()">Update</button><br/>
                <h5>Seats map</h5>
            </div>
        </div>

        <?php
            $boughtSeats = $model->getNumberOfBoughtSeats();
            $selectedSeats = $model->getNumberOfSelectedSeats();
            $reservedSeats = $model->getNumberOfReservedSeats();
            $freeSeats = \Utils\SeatsUtils::calculateNumberOfFreeSeats($boughtSeats + $reservedSeats);
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
            echo Utils\SeatsUtils::formatSeats($model,true);
            ?>

            <button type="submit" class="btn btn-lg btn-primary" id="buySeatsBtn">Buy</button>

            <div class="errorMessage alert alert-danger my-3" role="alert" style="display: none">
                <?= $model->getError(); ?>
            </div>
            <div class="successMessage alert alert-success my-3" role="alert" style="display: none">
                <?= $model->getSuccess(); ?>
            </div>
        </form>
        <div id="inset-form" hidden></div>

    </div>
</div>

<script type="text/javascript">
    function updateSeats() {
        $(':checkbox.seat').removeClass('triggerAction');
        $(':checkbox.seat').prop('checked', false);
        $(':checkbox.seat').attr('list', 'true');
        $(':checkbox.seat').attr('action', 'reserve');
        $(':checkbox.seat').attr('onFailure', 'reserveFailed');
        $(':checkbox.seat').attr('onSuccess', 'reserveSucceed');

        $(':checkbox.seat.selected').prop('checked', true);
        $(':checkbox.seat.selected').attr('action', 'free');
        $(':checkbox.seat.selected').attr('onFailure', '');
        $(':checkbox.seat.selected').attr('onSuccess', 'freeSucceed');
        $(':checkbox.seat').addClass('triggerAction');

        $('img.seat').addClass('cursor-pointer').addClass('hover-light-up');
        $('img.seat.free').prop("src", "images/free_seat.png");
        $('img.seat.selected').prop("src", "images/selected_seat.png");
        $('img.seat.reserved').prop("src", "images/reserved_seat.png");
        $('img.seat.bought').prop("src", "images/bought_seat.png");
        $('img.seat.bought').removeClass('cursor-pointer').removeClass('hover-light-up');


        let numberOfSelected = $(':checkbox.seat.selected').length;
        $('span#selectedSeats').html(numberOfSelected);
        $('span#freeSeats').html($(':checkbox.seat.free').length);
        $('span#reservedSeats').html($(':checkbox.seat.reserved').length + numberOfSelected);
        $('span#boughtSeats').html($('img.seat.bought').length);
        if (numberOfSelected < 1) $('#buySeatsBtn').prop('disabled', true)
        else $('#buySeatsBtn').prop('disabled', false);
    }

    $(document).ready(function () {
        updateSeats();

        $('img.seat').each(function () {
            let id = $(this).attr("id");

            let title = id;
            if ($(this).hasClass('bought')) title += ' - not available';

            $(this).attr("data-toggle", "tooltip").attr("title", title);
        }).tooltip();

        <?php
        if(!empty($model->getError())) echo "$('.errorMessage').show();";
        else if(!empty($model->getSuccess())) echo "$('.successMessage').show();";
        ?>
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
            showErrorMessage("We are sorry, but apparently the seat <b>"+id.substr(4)+"</b> has already been sold to another user.");
        }
    }

    function reserveSucceed(id) {
        $('#'+id).removeClass('free');
        $('#'+id).removeClass('reserved');
        $('#'+id).addClass('selected');
        $('#'+id.substr(4)).removeClass('free');
        $('#'+id.substr(4)).removeClass('reserved');
        $('#'+id.substr(4)).addClass('selected');
        updateSeats();

        showSuccessMessage("Seat <b>"+id.substr(4)+"</b> has been successfully <b>reserved</b>.")
    }

    function freeSucceed(id) {
        $('#'+id).removeClass('selected');
        $('#'+id).addClass('free');
        $('#'+id.substr(4)).removeClass('selected');
        $('#'+id.substr(4)).addClass('free');
        updateSeats();

        showSuccessMessage("Reservation for <b>"+id.substr(4)+"</b> has been successfully <b>canceled</b>.");
    }

    function showErrorMessage(msg) {
        $('.errorMessage').html(msg).show();
        $('.successMessage').hide();
    }

    function showSuccessMessage(msg) {
        $('.successMessage').html(msg).show();
        $('.errorMessage').hide();
    }

</script>
<?php endif; ?>