$(document).ready(function () {
    let tableId = 'dynamic-ngo-table';
    let ngoId;

    
    // Set headers dynamically
    let headers = ['Profile', 'Username', 'Email', 'Address', 'Action'];
    headers.forEach(header => {
        $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
    });

    // Initialize DataTable
    var ngoDataTable = $(`#${tableId}`).DataTable({
        ajax: {
            url: '/ngo/create',
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
        $('#ngoModal').modal('show');
        $('.showNote').addClass('d-none')
        $('#ngo-form').attr('action', '/ngo');
        $('#ngo-form').attr('method', 'POST');
        $('#ngo-form')[0].reset();

        showNGOModal($(this).data('modaltitle'));
    });

    // // Handle editing an existing animal type
    $(document).on('click', '.edit-btn', function () {
        ngoId = $(this).data('id');
        $('#username').val($(this).data('username'))
        $('#email').val($(this).data('email'))
        $('#address').val($(this).data('address'))

        $('#ngoModal').modal('show');
        $('.showNote').removeClass('d-none')
        $('#ngo-form').attr('action', `/ngo/${ngoId}`);
        $('#ngo-form').attr('method', 'POST');
        $('#ngo-form').find('input[name="_method"]').remove(); 
        $('#ngo-form').append('<input type="hidden" name="_method" value="PUT">');

        showNGOModal($(this).data('modaltitle'));
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
                removeNGO(id)
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
    $(document).on('click', '#saveNGO', function () {
        let form = $('#ngo-form')[0];
        let formData = new FormData(form);

        // if (ngoId) {
        //     formData.append('ngo_id', ngoId);
        // }

        showLoader('.saveNGO');

        $('#saveNGO').prop('disabled', true)

        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {

                hideLoader('.saveNGO');
                $('#saveNGO').prop('disabled', false)
                toast(data.type, data.message)
                $('#ngo-form')[0].reset();
                $('#ngoModal').modal('hide');
                ngoDataTable.ajax.reload();

            },
            error: function (response) {
                if (response.status === 422) {

                    hideLoader('.saveNGO');
                    $('#saveNGO').prop('disabled', false)

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

    function showNGOModal(modalTitle) {
        $('#ngoModal').modal('show');
        $('#ngoModal .modal-title').text(modalTitle);
    }
    
    
      function disable(id) {

        $.ajax({
            type: 'POST',
            url: '/ngo/disable',
            data: { id: id },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (data) {
            toast(data.type, data.message)
            $('#ngoModal').modal('hide');
              ngoDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });

    }
    
    
      function enable(id) {

        $.ajax({
            type: 'POST',
            url: '/ngo/enable',
            data: { id: id },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (data) {
            toast(data.type, data.message)
           $('#ngoModal').modal('hide');
             ngoDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });

    }
    

    function removeNGO(id) {

        $.ajax({
            type: 'DELETE',
            url: `/ngo/${id}`,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (data) {
            toast(data.type, data.message)
            $('#ngoModal').modal('hide');
            ngoDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });

    }

});
