function toast(type, message) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom-left',
        showConfirmButton: false,
        timer: 3000,
        animation: false,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })

    Toast.fire({
        icon: "" + type + "",
        title: "" + message + "",
    })
}

function showLoader(loaderClass) {
    $(loaderClass + '_button_text').addClass('d-none');
    $(loaderClass + '_load_data').removeClass('d-none');
}

function hideLoader(loaderClass) {
    setTimeout(function () {
        $(loaderClass + '_button_text').removeClass('d-none');
        $(loaderClass + '_load_data').addClass('d-none');

        clearValidation();

    }, 2000);
}

function clearValidation() {
    $('.form-control').removeClass('is-invalid border-danger');
    $('.invalid-feedback').text('');
}

function clearInput() {
    $('.form-control').val('');
    tinymce.get('description').setContent('');
}

$.ajax({
    url: '/get-barangays',
    method: 'GET',
    data: { municipality: 'CEBU CITY' },
    success: function (response) {
        var barangayDropdown = $('#address, #account_address');
        barangayDropdown.empty().append('<option value="">Select Barangay</option>');
        $.each(response.barangays, function (index, barangay) {
            barangayDropdown.append('<option value="' + barangay + '">' + barangay + '</option>');
        });
    },
    error: function (response) {
        console.log('Error:', response);
    }
});


// Function to check if a URL is a YouTube URL
function isYouTubeURL(url) {
    return url.indexOf('youtube.com') !== -1 || url.indexOf('youtu.be') !== -1;
}

// Function to extract YouTube Video ID from URL
function extractYouTubeVideoId(url) {
    const regex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
    const match = url.match(regex);
    return match && match[1];
}