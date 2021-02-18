var DateTime = luxon.DateTime;
var now = DateTime.local();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').prop('content')
    }
});
let reportModal;
if (layout) {
    reportModal = new bootstrap.Modal(document.getElementById('reportModal'));
}
$(function () {
    feather.replace()
    $('#year').text(new Date().getFullYear());
    if (layout) {
        $('#reportBtn').click(function () {
            $('#reportForm').submit();
        });
        $('#reportForm').submit(function (event) {
            event.preventDefault();
            if (!this.checkValidity()) {
                event.stopPropagation();
            } else {
                report();
            }
            $(this).addClass('was-validated');
        });
        $('#startReport').hide();
        $('#typeReport select').change(function () {
            if ($('#typeReport select option:selected').val() == 1) {
                $('#messageReport').show();
                $('#messageReport textarea').prop('required', true);
                $('#startReport').hide();
                $('#startReport select').prop('required', false);
            } else {
                $('#messageReport').hide();
                $('#messageReport textarea').prop('required', false);
                $('#startReport').show();
                $('#startReport select').prop('required', true);
            }
        });
        initReport();
    }
});

if (layout) {
    function initReport() {
        fetch('/ajax/report', { method: 'GET' }).then(response => response.json()).then(data => {
            if (data.exists) {
                $('#typeReport select').prop('disabled', false);
                $('#startReport select').empty();
                for (item of data.starts) {
                    $('#startReport select').append(`<option value="${item.event_id}">${DateTime.fromSQL(item.start, { zone: 'UTC' }).toLocal().toFormat('ff')}</option>`);
                }
            } else {
                $('#typeReport select').prop('disabled', true);
            }
        });
    }

    function report() {
        let form = $('#reportForm');
        let type = form.find('select[name="type"] option:selected').val();
        if (type == 1) {
            let message = form.find('textarea[name="message"]').val();
            postData('/ajax/report', {
                type: type,
                message: message
            }).then(() => {
                reportModal.hide();
                form.trigger('reset').removeClass('was-validated');
            });
        } else {
            let event_id = form.find('select[name="start"] option:selected').val();
            postData('/ajax/report', {
                type: type,
                event_id: event_id
            }).then(() => {
                reportModal.hide();
                form.trigger('reset').removeClass('was-validated');
            });
        }
    }
}

async function postData(url, data) {
    const response = await fetch(url, {
        method: 'POST',
        cache: 'no-cache',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').prop('content')
        },
        body: JSON.stringify(data)
    });
    return response.json();
}