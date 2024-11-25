$(document).ready(function () {
    // Add an event listener for the "Enter" key press on input fields
    $('#identifier, #password').on('keyup', function (e) {
        e.preventDefault(); 
        if (e.key === 'Enter' || e.keyCode === 13) {
            $('#loading-container').removeClass('d-none');
            loginAccount();
        }
    });

    $('#loginAccount').on('click', function () {
        $('#loading-container').removeClass('d-none');
        loginAccount();
    });

    function loginAccount() {

        var formData = { identifier: $('#identifier').val(), password: $('#password').val() };

        $.post({
            url: '/login/ajax',
            data: formData,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (data) {
            $(this).find('#loginAccount').attr('disabled', 'disabled');
          

            $('#loading-container').removeClass('d-none');

            setTimeout(function() {
                $('#loading-container').addClass('d-none');
                window.location.href = '/home';
            }, 3000);


        }).fail(function (data) {

            setTimeout(function () {
                $('#loading-container').addClass('d-none');
            }, 2000);

            if (data.status === 422) {
                var errors = data.responseJSON.errors;
                $.each(errors, function (key, value) {
                    $('#' + key).addClass('border-danger is-invalid');
                    $('#' + key + '_error').html('<strong>' + value[0] + '</strong>');
                });
            } else if (data.status === 403) {
                toast('error','Your account is disabled. Please contact support.')
                
            } else {
                console.log(data);
            }
        });
    }

});
