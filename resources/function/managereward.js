$(document).ready(function () {
    let tableId = 'dynamic-managereward-table';
    let rewardId;

    
    // Set headers dynamically
    let headers = ['Reward Type', 'Reward Amount', 'Reward Expiration Value', 'Reward Expiration Type', 'Status', 'Action'];
    headers.forEach(header => {
        $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
    });

    // Initialize DataTable
    var manageRewardDataTable = $(`#${tableId}`).DataTable({
        ajax: {
            url: '/managereward/create',
            dataSrc: 'data'
        },
        columns: [
            {
                data: 'rewardtype',
                class: 'px-5'
            },
            {
                data: 'rewardamount',
                class: 'px-5'
            },
            {
                data: 'rewardexpirationvalue',
                class: 'px-5'
            },
            {
                data: 'rewardexpirationtype',
                class: 'px-5'
            },
            // {
            //     data: 'rewardexpiration',
            //     class: 'px-5',
            //     render: function (data) {
            //         const today = moment(); // Current date
            //         const expirationDate = moment(data); // Expiration date
            
            //         const daysToExpire = expirationDate.diff(today, 'days'); // Calculate the number of days
            
            //         return daysToExpire + ' days'; // Just return the number of days
            //     }
            // },
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

    $(document).on('click', '#add-btn', function () {
        $('#rewardModal').modal('show');
        $('#reward-form').attr('action', '/managereward');
        $('#reward-form').attr('method', 'POST');
        $('#reward-form')[0].reset();

        showRewardModal($(this).data('modaltitle'));
    });

    // // Handle editing an existing animal type
    $(document).on('click', '.edit-btn', function () {
        rewardId = $(this).data('id');
        $('#reward_type').val($(this).data('rewardtype'))
        $('#reward_amount').val($(this).data('rewardamount'))
        $('#reward_expiration_value').val($(this).data('rewardexpirationvalue'))
        $('#reward_expiration_type').val($(this).data('rewardexpirationtype'))
        $('#status').val($(this).data('status'))

        $('#rewardModal').modal('show');
        $('.showNote').removeClass('d-none')
        $('#reward-form').attr('action', `/managereward/${rewardId}`);
        $('#reward-form').attr('method', 'POST');
        $('#reward-form').find('input[name="_method"]').remove(); 
        $('#reward-form').append('<input type="hidden" name="_method" value="PUT">');

        showRewardModal($(this).data('modaltitle'));
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
                removeReward(id)
            }
        })

    });

    // // Handle form submission
    $(document).on('click', '#saveReward', function () {
        let form = $('#reward-form')[0];
        let formData = new FormData(form);

        showLoader('.saveReward');

        $('#saveReward').prop('disabled', true)

        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {

                hideLoader('.saveReward');
                $('#saveReward').prop('disabled', false)
                toast(data.type, data.message)
                $('#reward-form')[0].reset();
                $('#rewardModal').modal('hide');
                manageRewardDataTable.ajax.reload();

            },
            error: function (response) {
                if (response.status === 422) {

                    hideLoader('.saveReward');
                    $('#saveReward').prop('disabled', false)

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

    function showRewardModal(modalTitle) {
        $('#rewardModal').modal('show');
        $('#rewardModal .modal-title').text(modalTitle);
    }

    function removeReward(id) {

        $.ajax({
            type: 'DELETE',
            url: `/managereward/${id}`,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (data) {
            toast(data.type, data.message)
            $('#rewardModal').modal('hide');
            manageRewardDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });

    }

});
