$(() => {
    $('#subjectError').hide();
    fetch('/ajax/subject/get', { method: 'GET' }).then(response => response.json()).then(data => {
        for (item of data[0]) {
            $('#mainContent').prepend(`<li class="list-group-item">${item.name}<span class="float-end toggle-true" data-subject="${item.item}" data-toggle="true"><i data-feather="minus"></i></span></li>`);
        }
        for (item of data[1]) {
            $('#searchContent').append(`<li class="list-group-item">${item.name}<span class="float-end toggle-false" data-subject="${item.item}" data-toggle="false"><i data-feather="plus"></i></span></li>`);
        }
        feather.replace();
    });
    $('#subjects').on('click', '[data-subject]', function () {
        let self = this;
        let subject = $(self).data('subject');
        if ($(self).data('toggle')) {
            postData('/ajax/subject/minus', {
                subject: subject
            }).then(response => {
                if (response.success) {
                    $(self).parent().detach().appendTo('#searchContent');
                    $(self).data('toggle', false).html('<i data-feather="plus"></i>').toggleClass('toggle-true toggle-false');
                    feather.replace();
                } else {
                    $('#subjectError').show().delay(1250).fadeOut(500);
                }
            });
        } else {
            postData('/ajax/subject/plus', {
                subject: subject
            }).then(() => {
                $(self).parent().detach().insertBefore('#subjectError');
                $(self).data('toggle', true).html('<i data-feather="minus"></i>').toggleClass('toggle-true toggle-false');
                feather.replace();
            });
        }
    });
    $('#searchInput div input').on("keyup", () => {
        var value = $('#searchInput div input').val().toLowerCase();
        $('#searchContent li').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    $('#informationForm').submit((event) => {
        event.preventDefault();
        const form = $('#informationForm');
        if (!form[0].checkValidity()) {
            event.stopPropagation();
        } else {
            $('fieldset').prop('disabled', true);
            form.find('button').prop('disabled', true);
            form.find('button').html('Updating&nbsp;&nbsp;<div class="spinner-border spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>');
            let meeting_link = $('input[name="meeting_link"]').val();
            let bio = $('textarea[name="bio"]').val();
            postData('/ajax/information/update', {
                meeting_link: meeting_link,
                bio: bio
            }).then(() => setTimeout(() => {
                $('fieldset').prop('disabled', false);
                $('#informationForm').find('button').prop('disabled', false).html('Update information');
                $('#informationForm').find('button');
            }, 1000));
        }
        form.addClass('was-validated');
    });
});
