
$(document).ready(function () {


    fetchSubscriptionSettings();

    function fetchSubscriptionSettings() {
        $.ajax({
            url: '/subscription-api', // Your API endpoint
            type: 'GET',
            success: function (response) {
                if (response.data.length > 0) {
                    $('#subscription-container').empty();

                    // Prepare a map of user subscription IDs for easy lookup
                    const userSubscriptions = response.datausersubs || []; // Ensure user subscriptions are defined
                    console.log(response.data)
                    response.data.forEach(function (subscription) {
                        let statusHtml = '';
                        let buttonText = 'Select';
                        let buttonSelectType = '';
                        let buttonDisabled = '';
                        let buttonClass = '';
                        let subscriptionStatus;
                        let isUserSubscribed = false;

                        // Check if the user has a subscription
                        userSubscriptions.forEach(function (userSubscription) {
                            if (userSubscription.subscription_setting.id === subscription.id) {
                                subscriptionStatus = userSubscription.status;
                                isUserSubscribed = true;
                            }
                        });


                        // If the user has their own subscription, use its status
                        if (isUserSubscribed) {
                            subscriptionStatus = subscription.subscriptions[0].status;

                        } else {
                            subscriptionStatus = subscription.subscriptions.status;

                        }



                        switch (subscriptionStatus) {
                            case 'Pending':
                                statusHtml = `<div class="subscription-overlay"><h3>Pending...</h3></div>`;
                                buttonText = 'Selected';
                                break;
                            case 'Approved':
                                statusHtml = `<div class="subscription-overlay"><h3>Approved <i class="bi bi-check-lg"></i></h3></div>`;
                                buttonText = 'Selected';
                                buttonDisabled = 'disabled';
                                break;
                            case 'Cancelled':
                                buttonText = 'Re-Select';
                                buttonSelectType = 'reselect';
                                break;
                            default:
                                statusHtml = ''; // Default no overlay
                                break;
                        }

                        const rewardAmount = subscription.rewards.length > 0 ? subscription.rewards[0].reward_amount : 0;


                        // Append the subscription card to the container
                        $('#subscription-container').append(`
                            <div class="col-xl-6 col-lg-6 col-md-12 col-12 mb-3">
                                <div class="card position-relative">
                                    ${statusHtml}
                                    <div class="card-body p-6 mb-4">
                                        <h2 class="mb-3">${subscription.subscription_type}</h2>
                                        <div style="margin-left:-18px;">
                                            ${subscription.subscription_desc}
                                        </div>
                                        <div class="d-flex align-items-end mt-6 mb-3">
                                            <h1 class="me-1 mb-0">${rewardAmount}</h1>
                                            <p class="mb-0">Reward Points</p>
                                        </div>
                                        <button class="btn btn-outline-primary mt-3 text-uppercase select-subscription ${buttonClass}" 
                                           data-subscription-settings-id="${subscription.id}" data-type="${buttonSelectType}" ${buttonDisabled}>
                                           ${buttonText}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                } else {
                    $('#no-data-message').show();
                }
            },
            error: function (xhr) {
                // Handle any errors
                console.error(xhr.responseJSON.message);
                $('#no-data-message').show();
            }
        });
    }

    function fetchUserSubscriptions() {
        $.ajax({
            url: '/subscription-api', // Ensure this URL is correct
            type: 'GET',
            success: function (response) {
                $('#selectedSubscriptionTable tbody').empty(); // Clear previous data

                if (response.datausersubs.length > 0) {
                    let hasValidSubscription = false;

                    response.datausersubs.forEach(function (subscription) {

                        let actionHtml = '';
                        let subscriptionStatus;

                        if (subscription.subscription_setting) { // Ensure subscription setting is present
                            subscriptionStatus = subscription.status; // Directly use subscription status
                            hasValidSubscription = true;

                            switch (subscriptionStatus) {
                                case 'Pending':
                                    $('.select-subscription').prop('disabled', true);
                                    break;
                                case 'Approved':
                                    $('.select-subscription').prop('disabled', true);
                                    actionHtml = `<p><i class="bi bi-check-lg fs-3 position-relative" style="top:7px;"></i></p>`;
                                    break;
                                case 'Cancelled':
                                    $('.select-subscription').prop('disabled', false);
                                    break;
                                case 'Expired':
                                    $('.select-subscription').prop('disabled', false);
                                    break;
                            }


                            if (subscriptionStatus !== 'Approved') {
                                actionHtml = `<button type="button" class="btn btn-outline-danger btn-sm cancel-subscription" data-subscribe-id="${subscription.id}"><span class="mt-1">Cancel</span> <i class="bi bi-x-lg"></i></button>`;
                            }

                            if (subscriptionStatus === 'Expired') {
                                actionHtml = `<p><i class="bi bi-x-lg fs-3 position-relative" style="top:7px;"></i></p>`;
                            }

                            if (subscriptionStatus === 'Pending' || subscriptionStatus === 'Approved' || subscriptionStatus === 'Expired') {
                                $('#selectedSubscriptionTable tbody').append(`
                                <tr>
                                    <td>${subscription.subscription_setting.subscription_type}</td>
                                    <td>${subscription.subscription_setting.subscription_reward}</td>
                                    <td>${subscriptionStatus}</td>
                                    <td>${actionHtml}</td>
                                </tr>
                            `);
                            } else {
                                $('#selectedSubscriptionTable tbody').append(`
                                    <tr>
                                        <td colspan="4">No data found</td>
                                    </tr>
                                `);
                            }

                        }
                    });

                    if (!hasValidSubscription) {
                        $('#selectedSubscriptionTable tbody').append(`
                            <tr>
                                <td colspan="4">No data found</td>
                            </tr>
                        `);
                    }
                } else {
                    $('#selectedSubscriptionTable tbody').append(`
                        <tr>
                            <td colspan="4">No data found</td>
                        </tr>
                    `);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseJSON.message);
                $('#no-data-message').show();
            }
        });
    }

    fetchUserSubscriptions()




    $(document).on('click', '.select-subscription', function () {

        let subscriptionSettingsId = $(this).data('subscription-settings-id');
        let subscriptionType = $(this).data('type');

        $.ajax({
            url: '/subscription-select',
            type: 'POST',
            data: {
                subscription_settings_id: subscriptionSettingsId,
                subscription_type: subscriptionType
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            },
            success: function (data) {
                fetchSubscriptionSettings();
                fetchUserSubscriptions()
                toast(data.type, data.message)
            },
            error: function (response) {

                if (response.status === 400) {
                    toast('error', response.responseJSON.message);
                } else {
                    console.log('Error:', response);
                }

            }
        });
    });

    $(document).on('click', '.cancel-subscription', function () {

        let subscriptionId = $(this).data('subscribe-id');

        Swal.fire({
            title: "Are you sure?",
            text: "You can still reselect this subscription if you cancel it.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, cancel it!",
            cancelButtonText: "Close"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/subscription-cancel',
                    type: 'POST',
                    data: {
                        subscription_id: subscriptionId
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    },
                    success: function (data) {
                        fetchSubscriptionSettings();
                        fetchUserSubscriptions()
                        toast(data.type, data.message)
                    },
                    error: function (response) {

                        if (response.status === 400) {
                            toast('error', response.responseJSON.message);
                        } else {
                            console.log('Error:', response);
                        }

                    }
                });
            }
        });


    });

    let tableId = 'dynamic-subscription-table';

    // Set headers dynamically
    let headers = ['Profile', 'Role', 'Subscription Type', 'Subscription Reward', 'Multiplier Promotion', 'Status', 'Action'];
    headers.forEach(header => {
        $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
    });

    // Initialize DataTable
    var subscriptionsDataTable = $(`#${tableId}`).DataTable({
        ajax: {
            url: '/subscription-api',
            dataSrc: 'datasubscription'
        },
        columns: [
            {
                data: 'profile',
                render: function (data, type, row) {
                    if (data != 'N/A') {
                        return `
                        <div class="d-flex align-items-center">
                            <img src="${data}" alt="${row.username}" class="avatar avatar-lg rounded-circle">
                            <div class="ms-2">
                                <h5 class="mb-0"><a href="#!" class="text-inherit">${row.username}</a></h5>
                            </div>
                        </div>
                            `;
                    } else {
                        return `

                         <div class="d-flex align-items-center">
                            <img src="assets/back/images/avatar/noprofile.webp" class="avatar avatar-lg rounded-circle">
                            <div class="ms-2">
                                <h5 class="mb-0"><a href="#!" class="text-inherit">${row.username}</a></h5>
                            </div>
                        </div>

                        `;
                    }
                }
            },
            {
                data: 'role',
                class: 'px-5'
            },
            {
                data: 'subscription',
                class: 'px-5'
            },
            {
                data: 'reward_amount',
                class: 'px-5',
                render: function (data) {
                    return data + ' points';
                }
            },
            {
                data: 'reward',
                class: 'px-5'
            },
            // {
            //     data: 'promotion',
            //     class: 'px-5',
            //     render: function (data) {
            //         return 'x' + data;
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


    $(document).on('click', '.approve-btn', function () {

        let id = $(this).data('id');

        Swal.fire({
            title: "Approve this subscription?",
            text: "This user will receive daily rewards.",
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, approve it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/subscription-approve',
                    type: 'POST',
                    data: {
                        subscription_id: id
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    },
                    success: function (data) {
                        subscriptionsDataTable.ajax.reload();
                        toast(data.type, data.message)
                    },
                    error: function (response) {

                        if (response.status === 400) {
                            toast('error', response.responseJSON.message);
                        } else {
                            console.log('Error:', response);
                        }

                    }
                });
            }
        });


    });

});

