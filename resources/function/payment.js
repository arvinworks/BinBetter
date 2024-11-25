$(document).ready(function () {
    let tableId = 'dynamic-payment-table';
    let paymentId;

    let headers = ['Name', 'Amount', 'Proof of Payment', 'Status', 'Action'];
    
    $(`#${tableId}-headers`).empty();
    
    headers.forEach(header => {
        $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
    });

    // Initialize DataTable
    var paymentDataTable = $(`#${tableId}`).DataTable({
        ajax: {
            url: '/payment/create',
            dataSrc: 'data'
        },
        columns: [
             {
                data: 'username',
                class: 'px-5',
               // visible: isAdmin 
            },
            {
                data: 'amount',
                class: 'px-5'
            },
            {
                data: 'paymentproof',
                render: function (data, type, row) {
                    if (data) {
                        return `
                        <picture>
                            <source srcset="${data}" type="image/webp">
                            <img src="${data}" style="width:100px;" class="clickable-image">
                        </picture>
                        `;
                    } else {
                        return `
                        <picture>
                            <source srcset="assets/back/images/brand/logo/noimage.jpg" type="image/webp">
                            <img src="assets/back/images/brand/logo/noimage.jpg" style="width:100px;" class="clickable-image">
                        </picture>
                        `;
                    }
                }
            },
            {
                data: 'status',
                class: 'px-5'
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
    
    
    
   $(document).on('click', '.clickable-image', function () {
    const imageUrl = $(this).attr('src'); // Get the image source
    
    // Dynamically set the image in the modal
    $('#showImage').html(`<img src="${imageUrl}" class="w-100">`);

    // Show the modal using Bootstrap's modal functionality
    $('#paymentImageModal').modal('show');
})


    $(document).on('click', '#add-btn', function () {
        $('#paymentModal').modal('show');
        $('.showNote').addClass('d-none')
        $('#payment-form').attr('action', '/payment');
        $('#payment-form').attr('method', 'POST');
        $('#payment-form')[0].reset();

        showPaymentModal($(this).data('modaltitle'));
    });

    $(document).on('click', '.edit-btn', function () {
        paymentId = $(this).data('id');
      
        $('#gcash_id').val($(this).data('gcash'))
        $('#amount').val($(this).data('amount'))
        $('#status').val($(this).data('status'))

        $('#paymentModal').modal('show');
        $('.showNote').removeClass('d-none')
        $('#payment-form').attr('action', `/payment/${paymentId}`);
        $('#payment-form').attr('method', 'POST');
        $('#payment-form').find('input[name="_method"]').remove();
        $('#payment-form').append('<input type="hidden" name="_method" value="PUT">');

        showPaymentModal($(this).data('modaltitle'));
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
                removePayment(id)
            }
        })

    });
    
    
    
     $(document).on('click', '.received-btn', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Received Payment?',
            text: "Please check your gcash account before proceed.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, receive it!',
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) {
                receivePayment(id)
            }
        })

    });
    
    
     $(document).on('click', '.reject-btn', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Reject Payment?',
            text: "Continue to reject if no payment yet or mismatch.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reject it!',
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) {
                rejectPayment(id)
            }
        })

    });

 
    $('#savePayment').on('click', function (e) {
        e.preventDefault();

        showLoader('.savePayment');

        let form = $('#payment-form')[0];
        let url = $(form).attr('action');
        let method = $(form).attr('method');

        let formData = new FormData(form);

        $('#savePayment').prop('disabled', true)

        $.ajax({
            url: url,
            method: method,
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {

                hideLoader('.savePayment');
                $('#payment-form')[0].reset();
                $('#savePayment').prop('disabled', false)
                toast(response.type, response.message);
                $('#paymentModal').modal('hide');
                paymentDataTable.ajax.reload();

            },
            error: function (response) {
                if (response.status === 422) {

                    hideLoader('.savePayment');
                    $('#savePayment').prop('disabled', false)

                    var errors = response.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $('#' + key).addClass('border-danger is-invalid');
                        $('#' + key + '_error').html('<strong>' + value[0] + '</strong>');
                    });
                } else if (response.status === 400) { 
                    alert(response.responseJSON.message);
                } else {
                    console.log(response);
                }
            }
        });
    });

    function showPaymentModal(modalTitle) {
        $('#paymentModal').modal('show');
        $('#paymentModal .modal-title').text(modalTitle);
    }
    
    
    
      function receivePayment(id) {
        $.ajax({
            type: 'POST',
            url: '/payment/receive',
            data: { id: id },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (response) {
            toast(response.type, response.message)
            $('#paymentModal').modal('hide');
            paymentDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });
    }
    
    
    
      function rejectPayment(id) {
        $.ajax({
            type: 'POST',
            url: '/payment/reject',
            data: { id: id },
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (response) {
            toast(response.type, response.message)
            $('#paymentModal').modal('hide');
            paymentDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });
    }
    

    function removePayment(id) {
        $.ajax({
            type: 'DELETE',
            url: `/payment/${id}`,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (response) {
            toast(response.type, response.message)
            $('#paymentModal').modal('hide');
            paymentDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });
    }


});
