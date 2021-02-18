let subjectID = '0';
let toast = new bootstrap.Toast(document.getElementById('toast'));
var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    timeZone: 'local',
    initialView: 'dayGridMonth',
    events: (info, successCallback) => {
        fetch(`/ajax/get/${subjectID}?start=${encodeURIComponent(info.startStr)}&end=${encodeURIComponent(info.endStr)}`, {
            method: 'GET'
        }).then(response => response.json()).then(data => { successCallback(data) });
    },
    selectable: true,
    nowIndicator: true,
    lazyFetching: true,
    loading: (isLoading) => {
        if (isLoading) {
            $('#spinnerArea').html('<div class="spinner-border" role="status"> <span class="visually-hidden">Loading...</span> </div>');
        } else {
            $('#spinnerArea .spinner-border').remove();
        }
    },
    dateClick: (info) => {
        if (accounttype == 'tutor') {
            $('#createSlotForm').find('input[name="start"]').val(DateTime.fromISO(info.dateStr).toFormat("yyyy-MM-dd'T'HH:mm"));
            createSlotModal.show();
        }
    },
    eventClick: (info) => {
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

$(() => {
    checkView();
    $(window).resize(() => {
        checkView();
    });
    $('#calendarSubjects').change(() => {
        subjectID = $('#calendarSubjects option:selected').val();
        calendar.refetchEvents();
    });
    $('#createSlotBtn').click(() => {
        $('#createSlotForm').submit();
    });
    $('#deleteSlotBtn').click(() => {
        $('#deleteSlotForm').submit();
    });
    $('#unclaimSlotBtn').click(() => {
        $('#unclaimSlotForm').submit();
    });
    $('#claimSlotBtn').click(() => {
        $('#claimSlotForm').submit();
    });
    $('#createSlotForm').submit((event) => {
        event.preventDefault();
        const form = $('#createSlotForm');
        if (DateTime.fromFormat(form.find('input[name="start"]').val(), "yyyy-MM-dd'T'HH:mm") < now.plus({ hours: 6 })) {
            form.find('input[name="start"]')[0].setCustomValidity('invalid');
        } else {
            form.find('input[name="start"]')[0].setCustomValidity('');
        }
        if (!form[0].checkValidity()) {
            event.stopPropagation();
        } else {
            createSlot();
        }
        form.addClass('was-validated');
    });
    $('#deleteSlotForm').submit((event) => {
        event.preventDefault();
        const form = $('#deleteSlotForm');
        if (!form[0].checkValidity()) {
            event.stopPropagation();
        } else {
            deleteSlot();
        }
        form.addClass('was-validated');
    });
    $('#unclaimSlotForm').submit((event) => {
        event.preventDefault();
        const form = $('#unclaimSlotForm');
        if (!form[0].checkValidity()) {
            event.stopPropagation();
        } else {
            unclaimSlot();
        }
        form.addClass('was-validated');
    });
    $('#claimSlotForm').submit((event) => {
        event.preventDefault();
        const form = $('#claimSlotForm');
        if (!form[0].checkValidity()) {
            event.stopPropagation();
        } else {
            claimSlot();
        }
        form.addClass('was-validated');
    });
});

checkView = () => {
    if (!$('.fc-timeGridDay-button').is(':visible')) {
        $('.fc-timeGridDay-button').trigger('click');
    }
}

createSlot = () => {
    let form = $('#createSlotForm');
    let start = DateTime.fromFormat(form.find('input[name="start"]').val(), "yyyy-MM-dd'T'HH:mm").toUTC().toFormat('yyyy-MM-dd HH:mm');
    let subject = form.find('select[name="subject"] option:selected').val();
    let repeat = form.find('input[name="repeat"]').is(':checked');
    postData('/ajax/create', {
        start: start,
        subject: subject,
        repeat: repeat
    }).then(response => {
        calendar.refetchEvents();
        createSlotModal.hide();
        initReport();
        form.trigger('reset').removeClass('was-validated');
        if (response.error) {
            $('#toast .toast-body').text(response.msg);
            toast.show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        }
    })
}

deleteSlot = () => {
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
        postData('/ajax/cancel', {
            start: start,
            repeat: repeat
        }).then(() => {
            calendar.refetchEvents();
            deleteSlotModal.hide();
            initReport();
            form.trigger('reset').removeClass('was-validated');
        });
    }
}

claimSlot = () => {
    let form = $('#claimSlotForm');
    let start = DateTime.fromFormat(form.find('input[name="start"]').val(), "yyyy-MM-dd'T'HH:mm").toUTC().toFormat('yyyy-MM-dd HH:mm');
    let tutorname = form.find('input[name="tutorname"]').val();
    let info = form.find('textarea[name="info"]').val();
    postData('/ajax/claim', {
        start: start,
        tutorname: tutorname,
        info: info
    }).then(response => {
        calendar.refetchEvents();
        claimSlotModal.hide();
        initReport();
        form.trigger('reset').removeClass('was-validated');
        if (response.error) {
            $('#toast .toast-body').text(response.msg);
            toast.show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        }
    });
}

unclaimSlot = () => {
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