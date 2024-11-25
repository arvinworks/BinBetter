
function loadAccountData() {
    // Parse the JSON data into a JavaScript object
    const accountSetting = JSON.parse(accountSettingJson);


    if (accountSetting) {

        const avatarLogo = accountSetting.profile;
    
        $('#auth_user_username').text(accountSetting.username);
        $('#account_address').val(accountSetting.address);
        $('#age').val(accountSetting.age);
        $('#gender').val(accountSetting.gender);

        if (avatarLogo) {
            $('#account-profile-img').attr('src', avatarLogo);
            $('#auth_user_profile').attr('src', avatarLogo);
        } else {
            $('#account-profile-img').attr('src', 'assets/back/images/avatar/noprofile.webp');
        }

    }
}

$(document).ready(function () {

    $('#profile').on('submit', function (e) {
        e.preventDefault();

        showLoader('.profile');

        // Create FormData object
        var formData = new FormData(this);

        $.ajax({
            url: '/generalsettings-profile',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                // Handle success
                if (response.success) {

                    hideLoader('.profile');

                    $('.form-control').val('');

                    toast('success', response.success);

                    // Update companySettingJson with new data from the response
                    accountSettingJson = JSON.stringify(response.accountSetting);
                    loadAccountData();
                }
            },
            error: function (response) {
                if (response.status === 422) {

                    hideLoader('.profile');

                    var errors = response.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $('#' + key).addClass('border-danger is-invalid');
                        $('#' + key + '_error').html('<strong>' + value[0] + '</strong>');
                    });
                } else {
                    console.log(response);
                }
            }
        });
    });

    $('#account').on('submit', function (e) {
        e.preventDefault();

        showLoader('.account');

        // Create FormData object
        var formData = new FormData(this);

        $.ajax({
            url: '/generalsettings-account',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                // Handle success
                if (response.success) {

                    hideLoader('.account');

                    //$('.form-control').val('');

                    toast('success', response.success);

                    // Update companySettingJson with new data from the response
                    accountSettingJson = JSON.stringify(response.accountSetting);
                    loadAccountData();
                    
                      setTimeout(function () {
            location.reload();
        }, 3000); 
        
                }
            },
            error: function (response) {
                if (response.status === 422) {

                    hideLoader('.account');

                    var errors = response.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $('#' + key).addClass('border-danger is-invalid');
                        $('#' + key + '_error').html('<strong>' + value[0] + '</strong>');
                    });
                } else {
                    console.log(response);
                }
            }
        });
    });

    $('#account_password').on('submit', function (e) {
        e.preventDefault();

        showLoader('.password');

        var formData = new FormData(this);

        $.ajax({
            url: '/generalsettings-password',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                // Handle success
                if (response.success) {

                    hideLoader('.password');

                    $('.form-control').val('');

                    toast('success', response.success);

                    // Update companySettingJson with new data from the response
                    accountSettingJson = JSON.stringify(response.accountSetting);
                    
                    loadAccountData();
                }
            },
            error: function (response) {
                if (response.status === 422) {

                    hideLoader('.password');

                    var errors = response.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $('#' + key).addClass('border-danger is-invalid');
                        $('#' + key + '_error').html('<strong>' + value[0] + '</strong>');
                    });
                } else {
                    console.log(response);
                }
            }
        });
    });

    loadAccountData();

});