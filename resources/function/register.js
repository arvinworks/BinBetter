$(document).ready(function () {

    $('#username, #email, #password, #password-confirm').on('keyup', function (e) {
        e.preventDefault();
        if (e.key === 'Enter' || e.keyCode === 13) {
            $('#loading-container').removeClass('d-none');
            registerAccount();
        }
    });

    $.ajax({
        url: '/get-barangays',
        method: 'GET',
        data: { municipality: 'CEBU CITY' },
        success: function (response) {
            var barangayDropdown = $('#barangay');
            barangayDropdown.empty().append('<option value="">Select Barangay</option>');
            $.each(response.barangays, function (index, barangay) {
                barangayDropdown.append('<option value="' + barangay + '">' + barangay + '</option>');
            });
        },
        error: function (response) {
            console.log('Error:', response);
        }
    });


    $('#registerAccount').on('click', function () {
        $('#loading-container').removeClass('d-none');
        registerAccount();
    });

    function registerAccount() {

        var formData = $('#registerForm').serialize();

        $.post({
            url: $('#registerForm').attr('action'),
            data: formData,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (data) {
            $(this).find('#registerAccount').attr('disabled', 'disabled');

            $('#loading-container').removeClass('d-none');

            setTimeout(function () {
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
            } else {
                console.log(data);
            }
        });
    }


});
