$(function () {
    var DateTime = luxon.DateTime;
    $('#cancelSlotForm').submit(function (event) {
        event.preventDefault();
        if (!this.checkValidity()) {
            event.stopPropagation();
        } else {
            let form = $(this);
            let start = DateTime.fromFormat(form.find('input[name="start"]').val(), "yyyy-MM-dd'T'HH:mm").toUTC().toFormat('yyyy-MM-dd HH:mm');
            let name = form.find('input[name="name"]').val();
            let email = form.find('input[name="email"]').val();
            let subject = form.find('input[name="subject"]').val();
            let info = form.find('textarea[name="info"]').val();
            let reason = form.find('textarea[name="reason"]').val();
            $.ajax({
                type: 'POST',
                url: '/ajax/cancel',
                data: {
                    start: start,
                    name: name,
                    email: email,
                    subject: subject,
                    info: info,
                    reason: reason
                },
                success: function () {
                    window.location.href = '/dashboard';
                }
            });
        }
        $(this).addClass('was-validated');
    });
});