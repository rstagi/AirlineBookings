$(document).ready(function () {
    $('img.seat.reserved').prop("src", "images/reserved_seat.png");
    $('img.seat.bought').prop("src", "images/bought_seat.png");
    $('img.seat.free').prop("src", "images/free_seat.png");

    $('span#freeSeats').html($('img.seat.free').length);
    $('span#reservedSeats').html($('img.seat.reserved').length);
    $('span#boughtSeats').html($('img.seat.bought').length);
});