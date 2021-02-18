$(() => {
    $('input[name="search"]').on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $('.table tbody tr').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    $('[data-eventid]').click(function () {
        let self = this;
        let event_id = $(self).data('eventid');
        if ($(self).data('action') == 'confirm') {
            postData('/ajax/report/confirm', {
                event_id: event_id
            }).then(() => {
                $(self).parent().parent().remove();
            });
        } else if ($(self).data('action') == 'deny') {
            postData('/ajax/report/deny', {
                event_id: event_id
            }).then(() => {
                $(self).parent().parent().remove();
            });
        }
    });
});