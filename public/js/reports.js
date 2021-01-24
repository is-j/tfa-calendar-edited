$(function () {
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
            $.ajax({
                type: 'POST',
                url: '/ajax/report/confirm',
                data: {
                    event_id: event_id
                },
                success: function () {
                    $(self).parent().parent().remove();
                }
            });
        } else if ($(self).data('action') == 'deny') {
            $.ajax({
                type: 'POST',
                url: '/ajax/report/deny',
                data: {
                    event_id: event_id
                },
                success: function () {
                    $(self).parent().parent().remove();
                }
            });
        }
    });
});