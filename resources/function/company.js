
function loadCompanyData() {
    // Parse the JSON data into a JavaScript object
    const companySetting = JSON.parse(companySettingJson);

    $('#company-logo-img').attr('src', 'assets/back/images/brand/logo/noimage.jpg').css('width', '100px');
     
    if (companySetting) {

        const companyLogo = companySetting.company_logo;

        if (companyLogo) {
            $('#company-logo-img').attr('src', companyLogo);
        } 

        $('#company_email').val( companySetting.company_email || '');
        $('#company_phone').val( companySetting.company_phone || '');
        $('#company_address').val( companySetting.company_address || '');
    }
}

$(document).ready(function () {

    $('#companyForm').on('submit', function (e) {
        e.preventDefault();

        showLoader('.company');

        // Create FormData object
        var formData = new FormData(this);

        $.ajax({
            url: '/generalsettings-company',
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
                    
                    hideLoader('.company');

                    toast('success', response.success);

                    // Update companySettingJson with new data from the response
                    companySettingJson = JSON.stringify(response.companySetting);
                    loadCompanyData();
                }
            },
            error: function (response) {
                if (response.status === 422) {

                    hideLoader('.company');
                    
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

    loadCompanyData();
});