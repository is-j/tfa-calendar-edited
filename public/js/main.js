$(function () {
    $('#year').text(new Date().getFullYear());
    $('form.needs-validation').submit(function (event) {
        if (!this.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        } else if ($(this).hasClass('ajax')) {
            event.preventDefault();
        }
        $(this).addClass('was-validated');
    }); 
});