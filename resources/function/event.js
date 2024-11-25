$(document).ready(function () {
    let tableId = 'dynamic-event-table';
    let eventId;


    // Set headers dynamically

    let headers = 
    roleAdmin ? ['Title', 'Description', 'Start Date', 'End Date', 'Location', 'Time', 'Capacity', 'Status']
    : ['Title', 'Description', 'Start Date', 'End Date', 'Location', 'Time', 'Capacity', 'Status', 'Action'];
    headers.forEach(header => {
        $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
    });

    // Initialize DataTable
    var eventDataTable = $(`#${tableId}`).DataTable({
        ajax: {
            url: '/event/create',
            dataSrc: 'data'
        },
        columns: [
            {
                data: 'title',
                class: 'px-5'
            },
            {
                data: 'description',
                class: 'px-5',
                render: function (data, type, row) {
                    var plainText = data.replace(/<\/?[^>]+(>|$)/g, "");
                    var words = plainText.split(/\s+/);
                    var truncated = words.slice(0, 5).join(' ');
    
                    if (words.length > 5) {
                        truncated += '... <a href="#" class="show-full-description" data-servicedescription="' + plainText + '">Read more</a>';
                    }

                    return truncated;
                }
            },
            {
                data: 'start_date',
                class: 'px-5',
                render: function (data, type, row) {
                    var date = new Date(data);
                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }
            },
            {
                data: 'end_date',
                class: 'px-5',
                render: function (data, type, row) {
                    var date = new Date(data);
                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }
            },
            {
                data: 'location',
                class: 'px-5'
            },
            {
                data: 'time',
                class: 'px-5'
            },
            {
                data: 'capacity',
                class: 'px-5'
            },
            {
                data: 'status',
                class: 'px-5',
                render: function (data, type, row) {
                    return data.charAt(0).toUpperCase() + data.slice(1);
                }
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
        $('#eventModal').modal('show');
        $('#event-form').attr('action', '/event');
        $('#event-form').attr('method', 'POST');
        $('#event-form')[0].reset();
        
        $('.hide_formgroup_status').addClass('d-none');
        $('#status').addClass('d-none');

        showEventModal($(this).data('modaltitle'));
    });

    // // Handle editing an existing animal type
    $(document).on('click', '.edit-btn', function () {
        eventId = $(this).data('id');
        $('#title').val($(this).data('title'))
        $('#start_date').val($(this).data('startdate'))
        $('#end_date').val($(this).data('enddate'))
        $('#location').val($(this).data('location'))
        $('#time').val($(this).data('time'))
        $('#capacity').val($(this).data('capacity'))
        $('#status').val($(this).data('status'))
        tinymce.get('description').setContent($(this).data('description'));
        
        $('.hide_formgroup_status').removeClass('d-none');
        $('#status').removeClass('d-none');

        $('#eventModal').modal('show');
        $('#event-form').attr('action', `/event/${eventId}`);
        $('#event-form').attr('method', 'POST');
        $('#event-form').find('input[name="_method"]').remove();
        $('#event-form').append('<input type="hidden" name="_method" value="PUT">');

        showEventModal($(this).data('modaltitle'));
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
                removeEvent(id)
            }
        })

    });

    $(document).on('click', '.show-full-description', function (e) {
        e.preventDefault();
        var fullDescription = $(this).data('servicedescription');
        $('#descriptionModal .modal-body').text(fullDescription);
        $('#descriptionModal').modal('show');
    });

    $('.join-event').on('click', function() {
        var eventId = $(this).data('eventid');

        $(this).prop('disabled', true);

        $.ajax({
            url: '/join-event',
            method: 'POST',
            data: {
                event_id: eventId,
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            },
            success: function(data) {
                toast(data.type, data.message)
                setTimeout(function() {
                    location.reload();
                }, 3000);
            },
            error: function(xhr) {
                alert('Something went wrong! Please try again.');
                $('.join-event').prop('disabled', false);
            }
        });
    });

    // // Handle form submission
    $(document).on('click', '#saveEvent', function () {
        let form = $('#event-form')[0];
        let formData = new FormData(form);

        if (eventId) {
            formData.append('event_id', eventId);
        }

        const descriptionContent = tinymce.get('description').getContent();
        formData.append('description', descriptionContent);

        showLoader('.saveEvent');

        $('#saveEvent').prop('disabled', true)

        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {

                hideLoader('.saveEvent');
                $('#saveEvent').prop('disabled', false)
                toast(data.type, data.message)
                $('#event-form')[0].reset();
                $('#eventModal').modal('hide');
                eventDataTable.ajax.reload();

            },
            error: function (response) {
                if (response.status === 422) {

                    hideLoader('.saveEvent');
                    $('#saveEvent').prop('disabled', false)

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

    function showEventModal(modalTitle) {
        $('#eventModal').modal('show');
        $('#eventModal .modal-title').text(modalTitle);
    }

    function removeEvent(id) {

        $.ajax({
            type: 'DELETE',
            url: `/event/${id}`,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            }
        }).done(function (data) {
            toast(data.type, data.message)
            $('#eventModal').modal('hide');
            eventDataTable.ajax.reload();
        }).fail(function (data) {
            console.log(data)
        });

    }

});
