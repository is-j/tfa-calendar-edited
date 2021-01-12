$(function () {
    $('#cancelSlotForm').submit(function (event) {
        event.preventDefault();
        if (!this.checkValidity()) {
            event.stopPropagation();
        } else {
            let form = $(this);
            let start = DateTime.fromFormat(form.find('input[name="start"]').val(), "yyyy-MM-dd'T'HH:mm").toUTC().toFormat('yyyy-MM-dd HH:mm');
            let studentname = form.find('input[name="studentname"]').val();
            let studentemail = form.find('input[name="studentemail"]').val();
            let subject = form.find('input[name="subject"]').val();
            let info = form.find('textarea[name="info"]').val();
            let reason = form.find('textarea[name="reason"]').val();
            $.ajax({
                type: 'POST',
                url: '/ajax/cancel',
                data: {
                    start: start,
                    studentname: studentname,
                    studentemail: studentemail,
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