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
    $('.errorMessage .alert-content').html(msg);
    $('.errorMessage').show();
    $('.successMessage').hide();
}

function showSuccessMessage(msg) {
    $('.successMessage .alert-content').html(msg);
    $('.successMessage').show();
    $('.errorMessage').hide();
}