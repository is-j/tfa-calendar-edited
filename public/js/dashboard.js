$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').prop('content')
    }
});
var DateTime = luxon.DateTime
var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    timeZone: 'local',
    initialView: 'dayGridMonth',
    events: '/ajax/get',
    selectable: true,
    nowIndicator: true,
    lazyFetching: true,
    loading: function (isLoading) {
        if (isLoading) {
            $('#spinnerarea').append('<div class="spinner-border text-primary" role="status"> <span class="sr-only">Loading...</span> </div>');
        } else {
            $('#spinnerarea').html('');
        }
    },
    dateClick: function (info) {
        if (accounttype == 'tutor') {
            $('#createSlotForm').find('input[name="start"]').val(DateTime.fromISO(info.dateStr).toFormat("yyyy-MM-dd'T'HH:mm"));
            $('#createSlotModal').modal();
        }
    },
    eventClick: function (info) {
        if (accounttype == 'student') {
            if (info.event.extendedProps.student == 'n/a') {
                $('#claimSlotModalLabel').text(`Slot: ${info.event.title}`);
                $('#startClaim input').val(DateTime.fromISO(info.event.startStr).toFormat("yyyy-MM-dd'T'HH:mm"));
                $('#tutorClaim input').val(info.event.extendedProps.tutor);
                $('#tutorbioClaim textarea').val(info.event.extendedProps.tutorbio);
                $('#infoClaim textarea').val('');
                if ($('#calendarSubjects').val() == 'General') {
                    getSubjects(info.event.extendedProps.tutor);
                } else {
                    $('#subjectClaim').html(`<div class="input-group-prepend"> <span class="input-group-text">subject</span> </div><input type="text" class="form-control" value="${$('#calendarSubjects option:selected').val()}" disabled>`);
                }
                $('#claimSlotModal').modal();
            } else {
                storage.ref().child(`${info.event.extendedProps.tutor}.png`).getDownloadURL().then(function (url) {
                    $('#tutorUnclaim .input-group-append span').html(`<img src='${url}' class='shadow rounded-circle border border-secondary mx-1 my-1' width='42' height='42'>`);
                }).catch(function () {
                    storage.ref().child(`default.png`).getDownloadURL().then(function (url) {
                        $('#tutorUnclaim .input-group-append span').html(`<img src='${url}' class='shadow rounded-circle border border-secondary mx-1 my-1' width='42' height='42'>`);
                    })
                });
                $('#unclaimSlotModalLabel').text(`Slot: ${info.event.title}`);
                $('#startUnclaim input').val(DateTime.fromISO(info.event.startStr).toFormat("yyyy-MM-dd'T'HH:mm"));
                $('#tutorUnclaim input').val(info.event.extendedProps.tutor);
                $('#zoomUnclaim').html(`<a class="btn btn-info btn-block" href="${info.event.extendedProps.zoom}" target="_blank"><i class="fas fa-video"></i>&nbsp;zoom link</a>`);
                $('#contactUnclaim div span').text('contact tutor');
                $('#contactUnclaim input').val(info.event.extendedProps.tutoremail);
                $('#tutorbioUnclaim textarea').val(info.event.extendedProps.tutorbio);
                $('#infoUnclaim textarea').val(info.event.extendedProps.info);
                $('#subjectUnclaim').html(`<div class="input-group-prepend"> <span class="input-group-text">subject</span> </div><input type="text" class="form-control" value="${info.event.extendedProps.subject}" disabled>`);
                $('#unclaimSlotModal').modal();
            }
        } else if (accounttype == 'tutor') {
            $('#deleteSlotModalLabel').text(`Slot: ${info.event.title}`);
            $('#startDelete input').val(DateTime.fromISO(info.event.startStr).toFormat("yyyy-MM-dd'T'HH:mm"));
            if (!info.event.extendedProps.studentname) {
                $('#studentnameDelete, #studentemailDelete, #subjectDelete, #infoDelete').hide();
                $('#repeatDelete').show();
            } else {
                $('#repeatDelete').hide();
                $('#studentnameDelete, #studentemailDelete, #subjectDelete, #infoDelete').show();
                $('#studentnameDelete input').val(info.event.extendedProps.studentname);
                $('#studentemailDelete input').val(info.event.extendedProps.studentemail);
                $('#infoDelete textarea').val(info.event.extendedProps.info);
                $('#subjectDelete').html(`<div class="input-group-prepend"> <span class="input-group-text">subject</span> </div><input type="text" class="form-control" value="${info.event.extendedProps.subject}" disabled>`);
            }
            $('#deleteSlotModal').modal();
        }
    }
});
calendar.render();

$(function () {
    checkView();
    $(window).resize(function () {
        checkView();
    });
    $('#createSlotBtn').click(function () {
        $('#createSlotForm').submit();
    });
    $('#deleteSlotBtn').click(function () {
        $('#deleteSlotForm').submit();
    });
    $('#unclaimSlotBtn').click(function () {
        $('#unclaimSlotForm').submit();
    });
    $('#claimSlotBtn').click(function () {
        $('#claimSlotForm').submit();
    });
    $('#createSlotForm').submit(function (event) {
        createSlot();
    });
    $('#deleteSlotForm').submit(function (event) {
        deleteSlot();
    });
    $('#unclaimSlotForm').submit(function (event) {
        unclaimSlot();
    });
    $('#claimSlotForm').submit(function (event) {
        claimSlot();
    });
});

function checkView() {
    if (!$('.fc-timeGridDay-button').is(':visible')) {
        $('.fc-timeGridDay-button').trigger('click');
    }
}

function createSlot() {
    let form = $('#createSlotForm');
    let start = DateTime.fromFormat(form.find('input[name="start"]').val(), "yyyy-MM-dd'T'HH:mm").toUTC().toFormat('yyyy-MM-dd HH:mm');
    let repeat = form.find('input[name="repeat"]').is(':checked');
    $.ajax({
        type: 'POST',
        url: '/ajax/create',
        data: {
            start: start,
            repeat: repeat
        },
        success: function () {
            calendar.refetchEvents();
            $('#createSlotModal').modal('hide');
            form.trigger('reset').removeClass('was-validated');
        }
    });
}

function deleteSlot() {
    let form = $('#deleteSlotForm');
    let start = DateTime.fromFormat(form.find('input[name="start"]').val(), "yyyy-MM-dd'T'HH:mm").toUTC().toFormat('yyyy-MM-dd HH:mm');
    let repeat = form.find('input[name="repeat"]').is(':checked');
    $.ajax({
        type: 'POST',
        url: '/ajax/cancel',
        data: {
            start: start,
            repeat: repeat
        },
        success: function () {
            calendar.refetchEvents();
            $('#deleteSlotModal').modal('hide');
            form.trigger('reset').removeClass('was-validated');
        }
    });
}