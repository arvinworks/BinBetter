$(document).ready(function () {
    let tableId = 'dynamic-lgu-table';
    let lguId;

    
    // Set headers dynamically
    let headers = ['Profile', 'Username', 'Email', 'Address', 'Action'];
    headers.forEach(header => {
        $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
    });

    // Initialize DataTable
    var lguDataTable = $(`#${tableId}`).DataTable({
        ajax: {
            url: '/lgu/create',
            dataSrc: 'data'
        },
        columns: [
            {
                data: 'profile',
                render: function (data, type, row) {
                    if (data) {
                        return `
                            <div class="d-flex align-items-center">
                                <img src="${data}" alt="${row.username}" class="avatar avatar-lg rounded-circle">
                            </div>
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
                data: 'username',
                class: 'px-5'
            },
            {
                data: 'email',
                class: 'px-5'
            },
            {
                data: 'address',
                class: 'px-5',
                render: function (data) {
                    return data == null ? '--' : data;
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
        $('#lguModal').modal('show');
        $('.showNote').addClass('d-none')
        $('#lgu-form').attr('action', '/lgu');
        $('#lgu-form').attr('method', 'POST');
        $('#lgu-form')[0].reset();

        showLGUModal($(this).data('modaltitle'));
    });

    // // Handle editing an existing animal type
    $(document).on('click', '.edit-btn', function () {
        lguId = $(this).data('id');
        $('#username').val($(this).data('username'))
        $('#email').val($(this).data('email'))
        $('#address').val($(this).data('address'))

        $('#lguModal').modal('show');
        $('.showNote').removeClass('d-none')
        $('#lgu-form').attr('action', `/lgu/${lguId}`);
        $('#lgu-form').attr('method', 'POST');
        $('#lgu-form').find('input[name="_method"]').remove(); 
        $('#lgu-form').append('<input type="hidden" name="_method" value="PUT">');

        showLGUModal($(this).data('modaltitle'));
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
                removeLGU(id)
            }
        })

    });
    
    
     $(document).on('click', '.disable-btn', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This account will be disabled.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, disable it!',
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) {
                disable(id)
            }
        })

    });
    
    
     $(document).on('click', '.enable-btn', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This account will be enabled.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, enable it!',
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) {
                enable(id)
            }
        })

    });
    

    // // Handle form submission
    $(document).on('click', '#saveLGU', function () {
        let form = $('#lgu-form')[0];
        let formData = new FormData(form);

        if (lguId) {
            formData.append('lgu_id', lguId);
        }

        showLoader('.saveLGU');

        $('#saveLGU').prop('disabled', true)

        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {

                hideLoader('.saveLGU');
                $('#saveLGU').prop('disabled', false)
                toast(data.type, data.message)
                $('#lgu-form')[0].reset();
                $('#lguModal').modal('hide');
                lguDataTable.ajax.reload();

            },
            error: function (response) {
                if (response.status === 422) {

                    hideLoader('.saveLGU');
                    $('#saveLGU').prop('disabled', false)

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

    function showLGUModal(modalTitle) {
        $('#lguModal').modal('show');
        $('#lguModal .modal-title').text(modalTitle);
    }
    
    
      function disable(id) {

        $.ajax({
            type: 'POST',
            url: '/lgu/disable',
            data: { id: id },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (data) {
            toast(data.type, data.message)
            $('#lguModal').modal('hide');
            lguDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });

    }
    
    
      function enable(id) {

        $.ajax({
            type: 'POST',
            url: '/lgu/enable',
            data: { id: id },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (data) {
            toast(data.type, data.message)
            $('#lguModal').modal('hide');
            lguDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });

    }

    function removeLGU(id) {

        $.ajax({
            type: 'DELETE',
            url: `/lgu/${id}`,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (data) {
            toast(data.type, data.message)
            $('#lguModal').modal('hide');
            lguDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });

    }

});
