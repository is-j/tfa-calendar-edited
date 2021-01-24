let toast = new bootstrap.Toast(document.getElementById('toast'));
var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    timeZone: 'local',
    initialView: 'dayGridMonth',
    events: '/ajax/get/0',
    selectable: true,
    nowIndicator: true,
    lazyFetching: true,
    loading: function (isLoading) {
        if (isLoading) {
            $('#spinnerArea').html('<div class="spinner-border" role="status"> <span class="visually-hidden">Loading...</span> </div>');
        } else {
            $('#spinnerArea .spinner-border').remove();
        }
    },
    dateClick: function (info) {
        if (accounttype == 'tutor') {
            $('#createSlotForm').find('input[name="start"]').val(DateTime.fromISO(info.dateStr).toFormat("yyyy-MM-dd'T'HH:mm"));
            createSlotModal.show();
        }
    },
    eventClick: function (info) {
        if (accounttype == 'student') {
            if (!info.event.extendedProps.claimed) {
                $('#claimSlotModalLabel').text(`Slot: ${info.event.title}`);
                $('#startClaim input').val(DateTime.fromISO(info.event.startStr).toFormat("yyyy-MM-dd'T'HH:mm"));
                $('#tutornameClaim input').val(info.event.extendedProps.tutorname);
                $('#tutorbioClaim textarea').val(info.event.extendedProps.tutorbio);
                $('#infoClaim textarea').val('');
                $('#subjectClaim input').val(info.event.extendedProps.subject);
                claimSlotModal.show();
            } else {
                $('#unclaimSlotModalLabel').text(`Slot: ${info.event.title}`);
                $('#startUnclaim input').val(DateTime.fromISO(info.event.startStr).toFormat("yyyy-MM-dd'T'HH:mm"));
                $('#tutornameUnclaim input').val(info.event.extendedProps.tutorname);
                $('#meetinglinkUnclaim a').prop('href', info.event.extendedProps.meeting_link);
                $('#tutoremailUnclaim input').val(info.event.extendedProps.tutoremail);
                $('#tutorbioUnclaim textarea').val(info.event.extendedProps.tutorbio);
                $('#infoUnclaim textarea').val(info.event.extendedProps.info);
                $('#subjectUnclaim input').val(info.event.extendedProps.subject);
                unclaimSlotModal.show();
            }
        } else if (accounttype == 'tutor') {
            $('#deleteSlotModalLabel').text(`Slot: ${info.event.title}`);
            $('#startDelete input').val(DateTime.fromISO(info.event.startStr).toFormat("yyyy-MM-dd'T'HH:mm"));
            $('#claimedDelete').data('claimed', info.event.extendedProps.claimed);
            $('#subjectDelete input').val(info.event.extendedProps.subject);
            if (!info.event.extendedProps.studentname) {
                $('#studentnameDelete, #studentemailDelete, #infoDelete, #meetinglinkDelete a').hide();
                $('#repeatDelete').show();
            } else {
                $('#repeatDelete').hide();
                $('#studentnameDelete, #studentemailDelete, #infoDelete, #meetinglinkDelete a').show();
                $('#studentnameDelete input').val(info.event.extendedProps.studentname);
                $('#studentemailDelete input').val(info.event.extendedProps.studentemail);
                $('#infoDelete textarea').val(info.event.extendedProps.info);
                $('#meetinglinkDelete a').prop('href', info.event.extendedProps.meeting_link);
            }
            deleteSlotModal.show();
        }
    }
});
calendar.render();

$(function () {
    checkView();
    $(window).resize(function () {
        checkView();
    });
    $('#calendarSubjects').change(function () {
        calendar.getEventSources()[0].remove();
        calendar.addEventSource(`/ajax/get/${$('#calendarSubjects option:selected').val()}`);
        calendar.refetchEvents();
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
        event.preventDefault();
        if (DateTime.fromFormat($('#createSlotForm').find('input[name="start"]').val(), "yyyy-MM-dd'T'HH:mm") < now.plus({ hours: 6 })) {
            $('#createSlotForm').find('input[name="start"]')[0].setCustomValidity('invalid');
        } else {
            $('#createSlotForm').find('input[name="start"]')[0].setCustomValidity('');
        }
        if (!this.checkValidity()) {
            event.stopPropagation();
        } else {
            createSlot();
        }
        $(this).addClass('was-validated');
    });
    $('#deleteSlotForm').submit(function (event) {
        event.preventDefault();
        if (!this.checkValidity()) {
            event.stopPropagation();
        } else {
            deleteSlot();
        }
        $(this).addClass('was-validated');
    });
    $('#unclaimSlotForm').submit(function (event) {
        event.preventDefault();
        if (!this.checkValidity()) {
            event.stopPropagation();
        } else {
            unclaimSlot();
        }
        $(this).addClass('was-validated');
    });
    $('#claimSlotForm').submit(function (event) {
        event.preventDefault();
        if (!this.checkValidity()) {
            event.stopPropagation();
        } else {
            claimSlot();
        }
        $(this).addClass('was-validated');
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
    let subject = form.find('select[name="subject"] option:selected').val();
    let repeat = form.find('input[name="repeat"]').is(':checked');
    $.ajax({
        type: 'POST',
        url: '/ajax/create',
        data: {
            start: start,
            subject: subject,
            repeat: repeat
        },
        success: function (response) {
            calendar.refetchEvents();
            createSlotModal.hide();
            initReport();
            form.trigger('reset').removeClass('was-validated');
            if (response.error) {
                $('#toast .toast-body').text(response.msg);
                toast.show();
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            }
        },
        dataType: 'json'
    });
}

function deleteSlot() {
    let form = $('#deleteSlotForm');
    let start = form.find('input[name="start"]').val();
    if (form.find('div[data-claimed]').data('claimed')) {
        let studentname = form.find('input[name="studentname"]').val();
        let studentemail = form.find('input[name="studentemail"]').val();
        let subject = form.find('input[name="subject"]').val();
        let info = form.find('textarea[name="info"]').val();
        $.redirect('/cancel', {
            _token: $('meta[name="csrf-token"]').prop('content'),
            start: start,
            studentname: studentname,
            studentemail: studentemail,
            subject: subject,
            info: info
        });
    } else {
        start = DateTime.fromFormat(start, "yyyy-MM-dd'T'HH:mm").toUTC().toFormat('yyyy-MM-dd HH:mm');
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
                deleteSlotModal.hide();
                initReport();
                form.trigger('reset').removeClass('was-validated');
            }
        });
    }
}

function claimSlot() {
    let form = $('#claimSlotForm');
    let start = DateTime.fromFormat(form.find('input[name="start"]').val(), "yyyy-MM-dd'T'HH:mm").toUTC().toFormat('yyyy-MM-dd HH:mm');
    let tutorname = form.find('input[name="tutorname"]').val();
    let info = form.find('textarea[name="info"]').val();
    $.ajax({
        type: 'POST',
        url: '/ajax/claim',
        data: {
            start: start,
            tutorname: tutorname,
            info: info
        },
        success: function (response) {
            calendar.refetchEvents();
            claimSlotModal.hide();
            initReport();
            form.trigger('reset').removeClass('was-validated');
            if (response.error) {
                $('#toast .toast-body').text(response.msg);
                toast.show();
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            }
        },
        dataType: 'json'
    });
}

function unclaimSlot() {
    let form = $('#unclaimSlotForm');
    let start = form.find('input[name="start"]').val();
    let tutorname = form.find('input[name="tutorname"]').val();
    let tutoremail = form.find('input[name="tutoremail"]').val();
    let info = form.find('textarea[name="info"]').val();
    $.redirect('/cancel', {
        _token: $('meta[name="csrf-token"]').prop('content'),
        start: start,
        tutorname: tutorname,
        tutoremail: tutoremail,
        info: info
    });
}