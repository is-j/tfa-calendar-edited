$(function () {
    $('#subjectError').hide();
    $('[data-subject]').click(function () {
        let self = this;
        let subject = $(self).data('subject');
        if ($(self).data('toggle')) {
            $.ajax({
                type: 'POST',
                url: '/ajax/subject/minus',
                data: {
                    subject: subject
                },
                success: function (response) {
                    if (response) {
                        $(self).parent().detach().appendTo('#searchContent');
                        $(self).data('toggle', false).html('<i data-feather="plus"></i>').toggleClass('toggle-true toggle-false');
                        feather.replace();
                    } else {
                        $('#subjectError').show().delay(1250).fadeOut(500);
                    }
                }
            });
        } else {
            $.ajax({
                type: 'POST',
                url: '/ajax/subject/plus',
                data: {
                    subject: subject
                },
                success: function () {
                    $(self).parent().detach().insertBefore('#subjectError');
                    $(self).data('toggle', true).html('<i data-feather="minus"></i>').toggleClass('toggle-true toggle-false');
                    feather.replace();
                }
            });
        }
    });
    $('#searchInput div input').on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $('#searchContent li').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    $('#informationForm').submit(function (event) {
        event.preventDefault();
        if (!this.checkValidity()) {
            event.stopPropagation();
        } else {
            $('fieldset').prop('disabled', true);
            $(this).find('button').prop('disabled', true);
            $(this).find('button').html('Updating&nbsp;&nbsp;<div class="spinner-border spinner-border-sm align-middle" role="status"> <span class="sr-only">Loading...</span> </div>');
            let meeting_link = $('input[name="meeting_link"]').val();
            let bio = $('textarea[name="bio"]').val();
            $.ajax({
                type: 'POST',
                url: '/ajax/information/update',
                data: {
                    meeting_link: meeting_link,
                    bio: bio
                },
                success: setTimeout(function () {
                    $('fieldset').prop('disabled', false);
                    $('#informationForm').find('button').prop('disabled', false).html('Update information');
                    $('#informationForm').find('button');
                }, 1000)
            });
        }
        $(this).addClass('was-validated');
    });
});
