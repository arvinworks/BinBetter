$(document).ready(function () {
    let tableId = "dynamic-subscription-table";
    let subscriptionId;

    // Set headers dynamically
    let headers = [
        "Subscription Type",
        "Subscription Description",
        "Subscription Price",
        "Subscription Reward",
        "Reward Amount",
        "Action",
    ];
    headers.forEach((header) => {
        $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
    });

    // Initialize DataTable
    var subscriptionDataTable = $(`#${tableId}`).DataTable({
        ajax: {
            url: "/subscriptionsettings/create",
            dataSrc: "data",
        },
        columns: [
            {
                data: "type",
                class: "px-5",
            },
            {
                data: "description",
                class: "px-5",
            },
            {
                data: "subscription_price",
                class: "px-5",
            },
            {
                data: "reward",
                class: "px-5",
            },
            {
                data: "reward_amount",
                class: "px-5",
            },
            {
                data: "actions",
                class: "px-5",
                render: function (data) {
                    return data;
                },
            },
        ],
        autoWidth: false,
        responsive: {
            breakpoints: [
                { name: "desktop", width: Infinity },
                { name: "tablet", width: 1024 },
                { name: "phone", width: 768 },
            ],
        },
        paging: true,
        searching: true,
        ordering: false,
        info: true,
        pageLength: 10,
        dom: "<lf<t>ip>",
        language: {
            search: "Filter",
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                previous: '<i class="bi bi-chevron-left"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
            },
        },
        fixedHeader: {
            header: true,
        },
        scrollCollapse: true,
        scrollX: true,
        scrollY: 600,
    });

    $(document).on("click", "#add-btn", function () {
        $("#subscriptionModal").modal("show");
        $("#subscription-form").attr("action", "/subscriptionsettings");
        $("#subscription-form").attr("method", "POST");
        $("#subscription-form")[0].reset();

        showSubscriptionModal($(this).data("modaltitle"));
    });

    // // Handle editing an existing animal type
    $(document).on("click", ".edit-btn", function () {
        subscriptionId = $(this).data("id");
        $("#subscription_type").val($(this).data("type"));
        tinymce
            .get("subscription_description")
            .setContent($(this).data("description"));
        $("#subscription_reward").val($(this).data("reward"));
        $("#subscription_price").val($(this).data("price"));

        $("#subscriptionModal").modal("show");
        $("#subscription-form").attr(
            "action",
            `/subscriptionsettings/${subscriptionId}`
        );
        $("#subscription-form").attr("method", "POST");
        $("#subscription-form").find('input[name="_method"]').remove();
        $("#subscription-form").append(
            '<input type="hidden" name="_method" value="PUT">'
        );

        showSubscriptionModal($(this).data("modaltitle"));
    });

    $(document).on("click", ".delete-btn", function () {
        let id = $(this).data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#000",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) {
                removeSubscription(id);
            }
        });
    });

    // // Handle form submission
    $(document).on("click", "#saveSubscription", function () {
        let form = $("#subscription-form")[0];
        let formData = new FormData(form);

        const descriptionContent = tinymce
            .get("subscription_description")
            .getContent();
        formData.append("subscription_description", descriptionContent);

        if (subscriptionId) {
            formData.append("subscription_id", subscriptionId);
        }

        showLoader(".saveSubscription");

        $("#saveSubscription").prop("disabled", true);

        $.ajax({
            url: $(form).attr("action"),
            method: $(form).attr("method"),
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                hideLoader(".saveSubscription");
                $("#saveSubscription").prop("disabled", false);
                toast(data.type, data.message);
                $("#subscription-form")[0].reset();
                $("#subscriptionModal").modal("hide");
                subscriptionDataTable.ajax.reload();
            },
            error: function (response) {
                if (response.status === 422) {
                    hideLoader(".saveSubscription");
                    $("#saveSubscription").prop("disabled", false);

                    var errors = response.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $("#" + key).addClass("border-danger is-invalid");
                        $("#" + key + "_error").html(
                            "<strong>" + value[0] + "</strong>"
                        );
                    });
                } else {
                    console.log(response);
                }
            },
        });
    });

    function showSubscriptionModal(modalTitle) {
        $("#subscriptionModal").modal("show");
        $("#subscriptionModal .modal-title").text(modalTitle);
    }

    function removeSubscription(id) {
        $.ajax({
            type: "DELETE",
            url: `/subscriptionsettings/${id}`,
            dataType: "json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader(
                    "X-CSRF-TOKEN",
                    $('meta[name="csrf-token"]').attr("content")
                );
            },
        })
            .done(function (data) {
                toast(data.type, data.message);
                $("#subscriptionModal").modal("hide");
                subscriptionDataTable.ajax.reload();
            })
            .fail(function (data) {
                console.log(data);
            });
    }
});
