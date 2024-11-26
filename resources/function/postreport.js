$(document).ready(function () {
    let tableGarbageId = "dynamic-postgarbagereport-table";
    let tableRecycledId = "dynamic-postrecycledreport-table";
    let postReportId;

    let headersGarbage = [
        "Address",
        "Photo",
        "Video",
        "Description",
        "Status",
        "Action",
    ];
    headersGarbage.forEach((header) => {
        $(`#${tableGarbageId}-headers`).append(
            `<th class="px-5">${header}</th>`
        );
    });

    let headersRecycled = [
        "Address",
        "Photo",
        "Video",
        "Description",
        "Status",
        "Action",
    ];
    headersRecycled.forEach((header) => {
        $(`#${tableRecycledId}-headers`).append(
            `<th class="px-5">${header}</th>`
        );
    });

    // Initialize DataTable
    var postGarbageReportDataTable = $(`#${tableGarbageId}`).DataTable({
        ajax: {
            url: "/postreport/create",
            dataSrc: "data_garbage",
        },
        columns: [
            {
                data: "address",
                class: "px-5",
                render: function (data, type, row) {
                    return data == null ? "--" : data;
                },
            },
            {
                data: "photo",
            },
            {
                data: "video",
                render: function (data, type, row) {
                    if (data === null) {
                        return "--"; // Display '--' if the video URL is null
                    } else {
                        // Check if the video URL is a YouTube URL
                        if (isYouTubeURL(data)) {
                            // Extract the video ID from the YouTube URL
                            const videoId = extractYouTubeVideoId(data);
                            // Return the YouTube embedded player
                            return (
                                '<iframe class="datatable-video" width="150" height="100" src="https://www.youtube.com/embed/' +
                                videoId +
                                '" frameborder="0" allowfullscreen></iframe>'
                            );
                        } else {
                            return (
                                '<video class="datatable-video" width="150" height="100" controls>' +
                                '<source src="' +
                                data +
                                '" type="application/x-mpegURL">' +
                                "Your browser does not support the video tag." +
                                "</video>"
                            );
                        }
                    }
                },
            },
            {
                data: "description",
                class: "px-5",
                render: function (data, type, row) {
                    var plainText = data.replace(/<\/?[^>]+(>|$)/g, "");
                    var words = plainText.split(/\s+/);
                    var truncated = words.slice(0, 3).join(" ");

                    if (words.length > 3) {
                        truncated +=
                            '... <a href="#" class="show-full-description" data-servicedescription="' +
                            plainText +
                            '">Read more</a>';
                    }

                    return truncated;
                },
            },
            {
                data: "status",
                class: "px-5",
                render: function (data, type, row) {
                    return data == "" ? "--" : data;
                },
            },
            {
                data: "actions",
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

    var postRecycledReportDataTable = $(`#${tableRecycledId}`).DataTable({
        ajax: {
            url: "/postreport/create",
            dataSrc: "data_recycled",
        },
        columns: [
            {
                data: "address",
                class: "px-5",
                render: function (data, type, row) {
                    return data == null ? "--" : data;
                },
            },
            {
                data: "photo",
            },
            {
                data: "video",
                render: function (data, type, row) {
                    if (data === null) {
                        return "--"; // Display '--' if the video URL is null
                    } else {
                        // Check if the video URL is a YouTube URL
                        if (isYouTubeURL(data)) {
                            // Extract the video ID from the YouTube URL
                            const videoId = extractYouTubeVideoId(data);
                            // Return the YouTube embedded player
                            return (
                                '<iframe class="datatable-video" width="150" height="100" src="https://www.youtube.com/embed/' +
                                videoId +
                                '" frameborder="0" allowfullscreen></iframe>'
                            );
                        } else {
                            return (
                                '<video class="datatable-video" width="150" height="100" controls>' +
                                '<source src="' +
                                data +
                                '" type="application/x-mpegURL">' +
                                "Your browser does not support the video tag." +
                                "</video>"
                            );
                        }
                    }
                },
            },
            {
                data: "description",
                class: "px-5",
                render: function (data, type, row) {
                    var plainText = data.replace(/<\/?[^>]+(>|$)/g, "");
                    var words = plainText.split(/\s+/);
                    var truncated = words.slice(0, 3).join(" ");

                    if (words.length > 3) {
                        truncated +=
                            '... <a href="#" class="show-full-description" data-servicedescription="' +
                            plainText +
                            '">Read more</a>';
                    }

                    return truncated;
                },
            },
            {
                data: "status",
                class: "px-5",
                render: function (data, type, row) {
                    return data == "" ? "--" : data;
                },
            },
            {
                data: "actions",
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
        $(".hide_formgroup_rtype").removeClass("d-none");

        $("#postreportModal").modal("show");
        $("#postreport-form").attr("action", "/postreport");
        $("#postreport-form").attr("method", "POST");
        $("#postreport-form")[0].reset();

        showPostReportModal($(this).data("modaltitle"));
    });

    // // Handle editing an existing animal type
    $(document).on("click", ".edit-btn", function () {
        postReportId = $(this).data("id");
        $("#type").val($(this).data("type"));
        $("#address").val($(this).data("address"));
        $("#video_url").val($(this).data("video"));

        $(".hide_formgroup_rtype").addClass("d-none");

        if ($(this).data("type") == "Garbage") {
            $("#garbage_container").removeClass("d-none").show();
            $("#recycled_container").addClass("d-none").hide();
            tinymce.get("description").setContent($(this).data("description"));
        } else {
            $("#recycled_container").removeClass("d-none").show();
            $("#garbage_container").addClass("d-none").hide();
            tinymce
                .get("re_description")
                .setContent($(this).data("description"));
        }

        $("#postreportModal").modal("show");
        $("#postreport-form").attr("action", `/postreport/${postReportId}`);
        $("#postreport-form").attr("method", "POST");
        $("#postreport-form").find('input[name="_method"]').remove();
        $("#postreport-form").append(
            '<input type="hidden" name="_method" value="PUT">'
        );

        showPostReportModal($(this).data("modaltitle"));
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
                removePostReport(id);
            }
        });
    });

    $(document).on("click", ".status-btn", function () {
        let id = $(this).data("id");
        let type = $(this).data("type");

        Swal.fire({
            title: "Are you sure?",
            text: `This post will be ${
                type === "reject" ? "rejected" : "accepted"
            }.`,
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#000",
            cancelButtonColor: "#d33",
            confirmButtonText: `Yes, ${
                type === "reject" ? "reject" : "accept"
            } it!`,
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) {
                acceptPostReport(id, type);
            }
        });
    });

    $(document).on("click", ".show-full-description", function (e) {
        e.preventDefault();
        var fullDescription = $(this).data("servicedescription");
        $("#descriptionModal .modal-body").text(fullDescription);
        $("#descriptionModal").modal("show");
    });

    function toggleContainers() {
        var selectedType = $("#type").val();
        if (selectedType === "Garbage") {
            $("#garbage_container").removeClass("d-none").show();
            $("#recycled_container").addClass("d-none").hide();
        } else if (selectedType === "Recycled") {
            $("#recycled_container").removeClass("d-none").show();
            $("#garbage_container").addClass("d-none").hide();
        } else {
            // Hide both containers if no valid type is selected
            $("#garbage_container").addClass("d-none").hide();
            $("#recycled_container").addClass("d-none").hide();
        }
    }

    toggleContainers();

    $("#type").change(function () {
        toggleContainers();
    });

    $("#postreportModal").on("hide.bs.modal", function () {
        $("#garbage_container").addClass("d-none").hide();
        $("#recycled_container").addClass("d-none").hide();
        $("#type").val("");
    });

    // // Handle form submission
    // $(document).on('click', '#savePostReport', function () {
    //     let form = $('#postreport-form')[0];
    //     let formData = new FormData(form);

    //     let descriptionContent;
    //     let photoReports;

    //     if ($('#type').val() === 'Garbage') {
    //         descriptionContent = tinymce.get('description').getContent();
    //         photoReports = $('#photo')[0].files[0];
    //     } else {
    //         descriptionContent = tinymce.get('re_description').getContent();
    //         photoReports = $('#re_photo')[0].files[0];
    //     }

    //     if (descriptionContent) {
    //         formData.append('description', descriptionContent);
    //     }

    //     if (postReportId) {
    //         formData.append('postreport_id', postReportId);
    //     }

    //     showLoader('.savePostReport');

    //     $('#savePostReport').prop('disabled', true)

    //   // console.log(formData)

    //     $.ajax({
    //         url: $(form).attr('action'),
    //         method: $(form).attr('method'),
    //         data: formData,
    //         contentType: false,
    //         processData: false,
    //         success: function (data) {

    //             hideLoader('.savePostReport');
    //             $('#savePostReport').prop('disabled', false)
    //             toast(data.type, data.message)
    //             $('#postreport-form')[0].reset();
    //             $('#postreportModal').modal('hide');
    //             postGarbageReportDataTable.ajax.reload();
    //             postRecycledReportDataTable.ajax.reload();

    //         },
    //         error: function (response) {
    //             if (response.status === 422) {

    //                 hideLoader('.savePostReport');
    //                 $('#savePostReport').prop('disabled', false)

    //                 var errors = response.responseJSON.errors;
    //                 $.each(errors, function (key, value) {
    //                     // Handle array inputs
    //                     let field = key.replace('.*', ''); // For array fields like photo.*
    //                     $('#' + field).addClass('border-danger is-invalid');
    //                     $('#' + field + '_error').html('<strong>' + value[0] + '</strong>');
    //                 });
    //             } else {
    //                 console.log(response);
    //             }
    //         }
    //     });
    // });

    $(document).on("click", "#savePostReport", function () {
        let form = $("#postreport-form")[0];
        let formData = new FormData(form);

        let descriptionContent;
        let photoReports;

        // Reset previous validation errors
        $("#photo_error").html("");
        $("#re_photo_error").html("");
        $("#photo, #re_photo").removeClass("border-danger is-invalid");

        // Validate the photo files before proceeding with the form submission
        let isValid = true;

        // Check for the 'Garbage' type
        if ($("#type").val() === "Garbage") {
            descriptionContent = tinymce.get("description").getContent();
            photoReports = $("#photo")[0].files;

            // Validate if files exist
            if (photoReports.length === 0) {
                $("#photo_error").html(
                    "<strong>Please select at least one photo.</strong>"
                );
                $("#photo").addClass("border-danger is-invalid");
                isValid = false;
            } else {
                // Validate each selected file
                for (let i = 0; i < photoReports.length; i++) {
                    let file = photoReports[i];
                    let fileType = file.type;
                    let fileSize = file.size;

                    // Check if the file is an image
                    if (!fileType.match("image.*")) {
                        $("#photo_error").html(
                            "<strong>Only image files are allowed (jpeg, png, jpg, webp).</strong>"
                        );
                        $("#photo").addClass("border-danger is-invalid");
                        isValid = false;
                        break; // Stop on the first invalid file
                    }

                    // Check file size (e.g., max 2MB)
                    if (fileSize > 2 * 1024 * 1024) {
                        $("#photo_error").html(
                            "<strong>Each photo must be less than 2MB.</strong>"
                        );
                        $("#photo").addClass("border-danger is-invalid");
                        isValid = false;
                        break;
                    }
                }
            }
        } else {
            // For other types (not 'Garbage')
            descriptionContent = tinymce.get("re_description").getContent();
            photoReports = $("#re_photo")[0].files;

            // Validate if files exist
            if (photoReports.length === 0) {
                $("#re_photo_error").html(
                    "<strong>Please select at least one photo.</strong>"
                );
                $("#re_photo").addClass("border-danger is-invalid");
                isValid = false;
            } else {
                // Validate each selected file
                for (let i = 0; i < photoReports.length; i++) {
                    let file = photoReports[i];
                    let fileType = file.type;
                    let fileSize = file.size;

                    // Check if the file is an image
                    if (!fileType.match("image.*")) {
                        $("#re_photo_error").html(
                            "<strong>Only image files are allowed (jpeg, png, jpg, webp).</strong>"
                        );
                        $("#re_photo").addClass("border-danger is-invalid");
                        isValid = false;
                        break; // Stop on the first invalid file
                    }

                    // Check file size (e.g., max 2MB)
                    if (fileSize > 2 * 1024 * 1024) {
                        $("#re_photo_error").html(
                            "<strong>Each photo must be less than 2MB.</strong>"
                        );
                        $("#re_photo").addClass("border-danger is-invalid");
                        isValid = false;
                        break;
                    }
                }
            }
        }

        // If validation fails, do not proceed with the form submission
        if (!isValid) {
            return; // Prevent form submission
        }

        // If everything is valid, proceed with the form submission
        if (descriptionContent) {
            formData.append("description", descriptionContent);
        }

        if (postReportId) {
            formData.append("postreport_id", postReportId);
        }

        showLoader(".savePostReport");

        $("#savePostReport").prop("disabled", true);

        console.log(formData);

        $.ajax({
            url: $(form).attr("action"),
            method: $(form).attr("method"),
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                hideLoader(".savePostReport");
                $("#savePostReport").prop("disabled", false);
                toast(data.type, data.message);
                $("#postreport-form")[0].reset();
                $("#postreportModal").modal("hide");
                postGarbageReportDataTable.ajax.reload();
                postRecycledReportDataTable.ajax.reload();
            },
            error: function (response) {
                if (response.status === 422) {
                    hideLoader(".savePostReport");
                    $("#savePostReport").prop("disabled", false);

                    var errors = response.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        // Handle array inputs
                        let field = key.replace(".*", ""); // For array fields like photo.*
                        $("#" + field).addClass("border-danger is-invalid");
                        $("#" + field + "_error").html(
                            "<strong>" + value[0] + "</strong>"
                        );
                    });
                } else {
                    console.log(response);
                }
            },
        });
    });

    function showPostReportModal(modalTitle) {
        $("#postreportModal").modal("show");
        $("#postreportModal .modal-title").text(modalTitle);
    }

    function removePostReport(id) {
        $.ajax({
            type: "DELETE",
            url: `/postreport/${id}`,
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
                $("#postreportModal").modal("hide");
                postGarbageReportDataTable.ajax.reload();
                postRecycledReportDataTable.ajax.reload();
            })
            .fail(function (data) {
                console.log(data);
            });
    }

    function acceptPostReport(id, type) {
        $.ajax({
            type: "POST",
            url: `/postreport-accept`,
            data: { reportId: id, statusType: type },
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
                $("#postreportModal").modal("hide");
                postGarbageReportDataTable.ajax.reload();
                postRecycledReportDataTable.ajax.reload();
            })
            .fail(function (data) {
                console.log(data);
            });
    }
});
