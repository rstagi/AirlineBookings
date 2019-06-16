function updateSeats() {
    // init checkboxes with right parameters
    $(':checkbox.seat').removeClass('triggerAction');   // removed class which to avoid any trigger to the controller's action
    $(':checkbox.seat').prop('checked', false);         // unchecked
    $(':checkbox.seat').attr('action', 'reserve');      // action is "reserve" by default
    $(':checkbox.seat').attr('onFailure', 'reserveFailed');     // failure callback
    $(':checkbox.seat').attr('onSuccess', 'reserveSucceed');    // success callback

    // init selected checkboxes
    $(':checkbox.seat.selected').prop('checked', true); // checked
    $(':checkbox.seat.selected').attr('action', 'free');    //action becomes "free"
    $(':checkbox.seat.selected').attr('onFailure', '');
    $(':checkbox.seat.selected').attr('onSuccess', 'freeSucceed');
    $(':checkbox.seat').addClass('triggerAction');

    // images init
    $('img.seat').addClass('cursor-pointer').addClass('hover-light-up');
    $('img.seat.free').prop("src", "images/free_seat.png");
    $('img.seat.selected').prop("src", "images/selected_seat.png");
    $('img.seat.reserved').prop("src", "images/reserved_seat.png");
    $('img.seat.bought').prop("src", "images/bought_seat.png");
    $('img.seat.bought').removeClass('cursor-pointer').removeClass('hover-light-up');

    // seats counters init
    let numberOfSelected = $(':checkbox.seat.selected').length;
    $('span#selectedSeats').html(numberOfSelected);
    $('span#freeSeats').html($(':checkbox.seat.free').length);
    $('span#reservedSeats').html($(':checkbox.seat.reserved').length + numberOfSelected);
    $('span#boughtSeats').html($('img.seat.bought').length);
    if (numberOfSelected < 1) $('#buySeatsBtn').prop('disabled', true)
    else $('#buySeatsBtn').prop('disabled', false); // buy button
}

$(document).ready(function () {
    updateSeats();

    // add the on mouse hover tooltip
    $('img.seat').each(function () {
        let id = $(this).attr("id");

        let title = id;
        if ($(this).hasClass('bought')) title += ' - not available';

        $(this).attr("data-toggle", "tooltip").attr("title", title);
    }).tooltip();

    // alerts behaviour
    function removeAlertBox(){
        $(".alert-dismissible").hide();
    }
    $(".close").click(function(e){
        e.stopPropagation();
        removeAlertBox();
    });
    $(document).click(function(e){
        removeAlertBox();
    });
});

$('img.seat').click(function() {
    if ($(this).hasClass('bought'))
        return;

    // on the click of the image, update checked value of the checkbox and trigger a "change" (it will be catched in asyncDispatcher.js)
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
    $('.successMessage').hide();
    $('.errorMessage .alert-content').html(msg);
    $('.errorMessage').show();
}

function showSuccessMessage(msg) {
    $('.errorMessage').hide();
    $('.successMessage .alert-content').html(msg);
    $('.successMessage').show();
}