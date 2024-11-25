$(document).ready(function() {
    $('#forgotAccount').on('click', function () {
        $('#loading-container').removeClass('d-none');
        forgotAccount();
    });

    $('#email').on('keyup', function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            $('#loading-container').removeClass('d-none');
            forgotAccount();
        }
    });
    

    function forgotAccount() {

        var formData = $('#forgotPasswordForm').serialize();

        $.post({
            url: $('#forgotPasswordForm').attr('action'),
            data: formData,
            dataType: 'json',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function(data) {
            $('#loading-container').addClass('d-none');
            toast('success', data.message);
            $('#email').val('')
        }).fail(function(data) {

            setTimeout(function () {
                $('#loading-container').addClass('d-none');
            }, 2000);

            if (data.status === 422) {
                var errors = data.responseJSON.errors;
                $.each(errors, function (key, value) {
                    $('#' + key).addClass('border-danger is-invalid');
                    $('#' + key + '_error').html('<strong>' + value[0] + '</strong>');
                });
            } else {
                console.log(data);
            }
        });
    }
});
