$(function () {
    feather.replace()
    $('#year').text(new Date().getFullYear());
});
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').prop('content')
    }
});