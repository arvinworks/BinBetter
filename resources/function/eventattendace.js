$(document).ready(function () {
    let tableId = 'dynamic-eventattendance-table';
    let eventId;


    // Set headers dynamically
    let headers = ['Name', 'Event', 'Generated QR', 'Time In', 'Status', 'Action'];
    headers.forEach(header => {
        $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
    });

    // Initialize DataTable
    var eventAttendanceDataTable = $(`#${tableId}`).DataTable({
        ajax: {
            url: '/event-attendance-api',
            dataSrc: 'data'
        },
        columns: [
            {
                data: 'user',
                class: 'px-5'
            },

            {
                data: 'event',
                class: 'px-5'
            },

            {
                data: 'generateqr',
                class: 'px-5',
                render: function (data, type, row) {
                    if (data == null) {
                        return '--'; 
                    } else {
                        var canvasId = 'qrcodeCanvas_' + row.joinEventId;
                        setTimeout(function () {
                            QRCode.toCanvas(document.getElementById(canvasId), data, function (error) {
                                if (error) console.error(error);
                            });
                        }, 0);
            
                        return '<canvas id="' + canvasId + '" width="80" height="80"></canvas>'; 
                    }
                }
            },
            
            {
                data: 'timein',
                class: 'px-5',
                render: function (data, type, row) {
                    return data == null ? '--' : data
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


    $(document).on('click', '.generate-btn', function () {
        joinEventId = $(this).data('id');
        userId = $(this).data('userid');
        eventId = $(this).data('eventid');
        

        showLoader('.generate-btn');
        $('#generate-btn').prop('disabled', true)

        $.ajax({
            url: '/event-generate-qr',
            method: 'POST',
            data: {
                joineventId : joinEventId,
                userId : userId,
                eventId : eventId
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            },
            success: function (data) {
                
                hideLoader('.generate-btn');
                toast(data.type, data.message)
                eventAttendanceDataTable.ajax.reload();

            },
            error: function (response) {
                if (response.status === 422) {

                    hideLoader('.generate-btn');
                    $('#generate-btn').prop('disabled', false)

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

});