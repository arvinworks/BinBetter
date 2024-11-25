$(document).ready(function () {
    let tableId = 'dynamic-service-table';
    let serviceId;

    
    // Set headers dynamically
    let headers = ['Service Type', 'Service Points', 'Description', 'Action'];
    headers.forEach(header => {
        $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
    });

    // Initialize DataTable
    var serviceDataTable = $(`#${tableId}`).DataTable({
        ajax: {
            url: '/service/create',
            dataSrc: 'data'
        },
        columns: [
            {
                data: 'service',
                class: 'px-5'
            },
            {
                data: 'points',
                class: 'px-5'
            },
            {
                data: 'description',
                class: 'px-5',
                render: function (data, type, row) {
                    var plainText = data.replace(/<\/?[^>]+(>|$)/g, "");
                    var words = plainText.split(/\s+/);
                    var truncated = words.slice(0, 7).join(' ');
    
                    if (words.length > 7) {
                        truncated += '... <a href="#" class="show-full-description" data-servicedescription="' + plainText + '">Read more</a>';
                    }

                    return truncated;
                }
            },
            {
                data: 'actions',
                class: 'px-5',
                render: function (data) {
                    return data;
                }
            }
        ],
        autoWidth: false,
        responsive: {
            breakpoints: [
                { name: 'desktop', width: Infinity },
                { name: 'tablet', width: 1024 },
                { name: 'phone', width: 768 }
            ]
        },
        paging: true,
        searching: true,
        ordering: false,
        info: true,
        pageLength: 10,
        dom: '<lf<t>ip>',
        language: {
            search: 'Filter',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                previous: '<i class="bi bi-chevron-left"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>'
            }
        },
        fixedHeader: {
            header: true,
        },
        scrollCollapse: true,
        scrollX: true,
        scrollY: 600,
    });

    $(document).on('click', '#add-btn', function () {
        $('#serviceModal').modal('show');
        $('#service-form').attr('action', '/service');
        $('#service-form').attr('method', 'POST');
        $('#service-form')[0].reset();

        showServiceModal($(this).data('modaltitle'));
    });

    // // Handle editing an existing animal type
    $(document).on('click', '.edit-btn', function () {
        serviceId = $(this).data('id');
        $('#service').val($(this).data('service'))
        $('#points').val($(this).data('points'))
        tinymce.get('description').setContent($(this).data('description'));

        $('#serviceModal').modal('show');
        $('#service-form').attr('action', `/service/${serviceId}`);
        $('#service-form').attr('method', 'POST');
        $('#service-form').find('input[name="_method"]').remove(); 
        $('#service-form').append('<input type="hidden" name="_method" value="PUT">');

        showServiceModal($(this).data('modaltitle'));
    });

    $(document).on('click', '.delete-btn', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) {
                removeService(id)
            }
        })

    });

    $(document).on('click', '.show-full-description', function (e) {
        e.preventDefault();
        var fullDescription = $(this).data('servicedescription');
        $('#descriptionModal .modal-body').text(fullDescription);
        $('#descriptionModal').modal('show');
    });
    

    // // Handle form submission
    $(document).on('click', '#saveService', function () {
        let form = $('#service-form')[0];
        let formData = new FormData(form);

        const descriptionContent = tinymce.get('description').getContent();
        formData.append('description', descriptionContent);

        if (serviceId) {
            formData.append('service_id', serviceId);
        }

        showLoader('.saveService');

        $('#saveService').prop('disabled', true)

        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {

                hideLoader('.saveService');
                $('#saveService').prop('disabled', false)
                toast(data.type, data.message)
                $('#service-form')[0].reset();
                $('#serviceModal').modal('hide');
                serviceDataTable.ajax.reload();

            },
            error: function (response) {
                if (response.status === 422) {

                    hideLoader('.saveService');
                    $('#saveService').prop('disabled', false)

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

    function showServiceModal(modalTitle) {
        $('#serviceModal').modal('show');
        $('#serviceModal .modal-title').text(modalTitle);
    }

    function removeService(id) {

        $.ajax({
            type: 'DELETE',
            url: `/service/${id}`,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (data) {
            toast(data.type, data.message)
            $('#serviceModal').modal('hide');
            serviceDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });

    }

});
