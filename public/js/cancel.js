$(() => {
    $('#cancelSlotForm').submit((event) => {
        event.preventDefault();
        const form = $('#cancelSlotForm');
        if (!form[0].checkValidity()) {
            event.stopPropagation();
        } else {
            let start = DateTime.fromFormat(form.find('input[name="start"]').val(), "yyyy-MM-dd'T'HH:mm").toUTC().toFormat('yyyy-MM-dd HH:mm');
            let name = form.find('input[name="name"]').val();
            let email = form.find('input[name="email"]').val();
            let subject = form.find('input[name="subject"]').val();
            let info = form.find('textarea[name="info"]').val();
            let reason = form.find('textarea[name="reason"]').val();
            postData('/ajax/cancel', {
                start: start,
                name: name,
                email: email,
                subject: subject,
                info: info,
                reason: reason
            }).then(() => {
                window.location.href = '/dashboard';
            })
        }
        form.addClass('was-validated');
    });
});