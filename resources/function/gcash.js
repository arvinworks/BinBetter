$(document).ready(function () {
    let tableId = 'dynamic-gcash-table';
    let gcashId;

    // Set headers dynamically
    let headers = ['Gcash Number', 'Gcash QR', 'Gcash Status', 'Set Active Gcash', 'Action'];
    headers.forEach(header => {
        $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
    });

    // Initialize DataTable
    var gcashDataTable = $(`#${tableId}`).DataTable({
        ajax: {
            url: '/gcash/create',
            dataSrc: 'data'
        },
        columns: [
            {
                data: 'gcashnumber',
                class: 'px-5'
            },
            {
                data: 'gcashqr',
                render: function (data, type, row) {
                    if (data) {
                        return `
                        <picture>
                            <source srcset="${data}" type="image/webp">
                            <img src="${data}" style="width:100px;">
                        </picture>

                            `;
                    } else {
                        return `
                        <picture>
                            <source srcset="assets/back/images/brand/logo/noimage.jpg" type="image/webp">
                            <img src="assets/back/images/brand/logo/noimage.jpg" style="width:100px;">
                        </picture>
                        `;
                    }
                }
            },
            {
                data: 'status',
                class: 'px-5',
                render: function (data, type, row) {
                    return data == null ? '--' : data
                }
            },
            {
                data: "id",
                render: function (data, type, row) {
                    const radioButton = `<input type="radio" name="status" value="${data}" class="switchStatus mx-10" ${row.status === 'Active' ? 'checked' : ''} />`;
                    return radioButton;
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
        $('#gcashModal').modal('show');
        $('.showNote').addClass('d-none')
        $('#gcash-form').attr('action', '/gcash');
        $('#gcash-form').attr('method', 'POST');
        $('#gcash-form')[0].reset();

        showGcashModal($(this).data('modaltitle'));
    });

    $(document).on('click', '.edit-btn', function () {
        gcashId = $(this).data('id');
        $('#gcash_number').val($(this).data('gcashnumber'))

        $('#gcashModal').modal('show');
        $('.showNote').removeClass('d-none')
        $('#gcash-form').attr('action', `/gcash/${gcashId}`);
        $('#gcash-form').attr('method', 'POST');
        $('#gcash-form').find('input[name="_method"]').remove();
        $('#gcash-form').append('<input type="hidden" name="_method" value="PUT">');

        showGcashModal($(this).data('modaltitle'));
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
                removeGcash(id)
            }
        })

    });

    $(document).on('change', '.switchStatus', function () {

        var selectedValue = $(this).val();
        var isChecked = $(this).is(':checked'); // Check if the switch is checked

        $.ajax({
            type: 'POST',
            url: '/gcash-switch',
            dataType: 'json',
            data: { id: selectedValue, status: isChecked ? 'Active' : 'Inactive' },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            },
            success: function (response) {
                toast(response.type, response.message);
                gcashDataTable.ajax.reload();
            },
            error: function (data) {
                if (data.status === 400) {
                    $('.switchStatus').prop('checked', false); // Revert if error
                    var errorMessage = data.responseJSON.message;
                    toast('error', errorMessage);
                    gcashDataTable.ajax.reload();
                } else {
                    console.log(data);
                }
            }
        });
    });

    $('#saveGcash').on('click', function (e) {
        e.preventDefault();

        showLoader('.saveGcash');

        let form = $('#gcash-form')[0];
        let url = $(form).attr('action');
        let method = $(form).attr('method');

        let formData = new FormData(form);

        $('#saveGcash').prop('disabled', true)

        $.ajax({
            url: url,
            method: method,
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {

                hideLoader('.saveGcash');
                $('#gcash-form')[0].reset();
                $('#saveGcash').prop('disabled', false)
                toast(response.type, response.message);
                $('#gcashModal').modal('hide');
                gcashDataTable.ajax.reload();

            },
            error: function (response) {
                if (response.status === 422) {

                    hideLoader('.saveGcash');
                    $('#saveGcash').prop('disabled', false)

                    var errors = response.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $('#' + key).addClass('border-danger is-invalid');
                        $('#' + key + '_error').html('<strong>' + value[0] + '</strong>');
                    });
                } else if (response.status === 400) {  // Handling 400 Bad Request
                    alert(response.responseJSON.message);
                } else {
                    console.log(response);
                }
            }
        });
    });

    function showGcashModal(modalTitle) {
        $('#gcashModal').modal('show');
        $('#gcashModal .modal-title').text(modalTitle);
    }

    function removeGcash(id) {
        $.ajax({
            type: 'DELETE',
            url: `/gcash/${id}`,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (response) {
            toast(response.type, response.message)
            $('#gcashModal').modal('hide');
            gcashDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });
    }


});
