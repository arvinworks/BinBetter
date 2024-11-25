$(document).ready(function () {
    let tableId = 'dynamic-garbageschedules-table';
    let garbageId;


    // Set headers dynamically
    let headers =  ['Street', 'Barangay', 'Time', 'Collection Day'];
    headers.forEach(header => {
        $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
    });

    // Initialize DataTable
    var garbageDataTable = $(`#${tableId}`).DataTable({
        ajax: {
            url: '/garbage/create',
            dataSrc: 'data'
        },
        columns: [
            {
                data: 'street',
                class: 'px-5'
            },
            {
                data: 'barangay',
                class: 'px-5'
            },
            {
                data: 'time',
                class: 'px-5'
            },
            {
                data: 'collection_day',
                class: 'px-5'
            },
            {
                data: 'actions',
                visible: roleAdmin ? false : true,
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
        $('#garbageModal').modal('show');
        $('#garbage-form').attr('action', '/garbage');
        $('#garbage-form').attr('method', 'POST');
        $('#garbage-form')[0].reset();

        showGarbageModal($(this).data('modaltitle'));
    });

    // // Handle editing an existing animal type
    $(document).on('click', '.edit-btn', function () {
        let garbageId = $(this).data('id');
        let street = $(this).data('street');
        let barangay = $(this).data('barangay');
        let timeFrom = $(this).data('timefrom');
        let timeTo = $(this).data('timeto');
        let collectionDay = $(this).data('day');

        $('#street').val(street);
        $('#address').val(barangay);
        $('#timefrom').val(timeFrom);
        $('#timeto').val(timeTo);

        // Check the corresponding checkboxes for the collection days
        let days = collectionDay.split(','); // Split the collectionDay string into an array
        $('input[type="checkbox"][name="day"]').prop('checked', false); // Uncheck all checkboxes first
        days.forEach(function (day) {
            $('input[type="checkbox"][value="' + day.trim() + '"]').prop('checked', true); // Check the relevant checkboxes
        });

        $('#garbageModal').modal('show');
        $('.showNote').removeClass('d-none')
        $('#garbage-form').attr('action', `/garbage/${garbageId}`);
        $('#garbage-form').attr('method', 'POST');
        $('#garbage-form').find('input[name="_method"]').remove();
        $('#garbage-form').append('<input type="hidden" name="_method" value="PUT">');

        showGarbageModal($(this).data('modaltitle'));
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
                removeGarbage(id)
            }
        })

    });

    // // Handle form submission
    $(document).on('click', '#saveGarbage', function () {
        let form = $('#garbage-form')[0];
        let formData = new FormData(form);

        let selectedDays = [];
        $('input[name="day"]:checked').each(function () {
            selectedDays.push($(this).val());
        });
        formData.append('collection_day', selectedDays.join(','));

        showLoader('.saveGarbage');

        $('#saveGarbage').prop('disabled', true)

        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {

                hideLoader('.saveGarbage');
                $('#saveGarbage').prop('disabled', false)
                toast(data.type, data.message)
                $('#garbage-form')[0].reset();
                $('#garbageModal').modal('hide');
                garbageDataTable.ajax.reload();

            },
            error: function (response) {
                if (response.status === 422) {

                    hideLoader('.saveGarbage');
                    $('#saveGarbage').prop('disabled', false)

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

    function showGarbageModal(modalTitle) {
        $('#garbageModal').modal('show');
        $('#garbageModal .modal-title').text(modalTitle);
    }

    function removeGarbage(id) {

        $.ajax({
            type: 'DELETE',
            url: `/garbage/${id}`,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (data) {
            toast(data.type, data.message)
            $('#garbageModal').modal('hide');
            garbageDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });

    }

});
