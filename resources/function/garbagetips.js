$(document).ready(function () {
    let tableId = "dynamic-garbagetips-table";
    let garbagetipId;
    let contentId;

    let headers = ["Title", "Photos", "Video", "Description", "Action"];
    headers.forEach((header) => {
        $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
    });

    // Initialize DataTable
    var garbageTipsDataTable = $(`#${tableId}`).DataTable({
        ajax: {
            url: "/garbagetip/create",
            dataSrc: "data",
        },
        columns: [
            {
                data: "title",
                class: "px-5",
            },
            {
                data: "photos",
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
                                '<iframe class="datatable-video" width="300" height="240" src="https://www.youtube.com/embed/' +
                                videoId +
                                '" frameborder="0" allowfullscreen></iframe>'
                            );
                        } else {
                            return (
                                '<video class="datatable-video" width="300" height="240" controls>' +
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
                    var truncated = words.slice(0, 4).join(" ");

                    if (words.length > 4) {
                        truncated +=
                            '... <a href="#" class="show-full-description" data-tutorialdescription="' +
                            plainText +
                            '">Read more</a>';
                    }

                    return truncated;
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
        $("#garbageTipModal").modal("show");
        $("#garbagetip-form").attr("action", "/garbagetip");
        $("#garbagetip-form").attr("method", "POST");
        $("#garbagetip-form")[0].reset();

        showGarbageTipModal($(this).data("modaltitle"));
    });

    $(document).on("click", ".edit-btn", function () {
        garbagetipId = $(this).data("id");
        var photos = $(this).data("photos");
        var video = $(this).data("video");
        var description = $(this).data("description");
        var title = $(this).data("title");

        $("#title").val(title);
        tinymce.get("description").setContent(description);

        if (photos) {
            var photosArray = photos.split(",");
            var photosHtml = "";

            // Generate HTML for each photo
            photosArray.forEach(function (photo) {
                photosHtml += `<img src="${photo.trim()}" alt="Garbage Tip Photo" class="img-thumbnail mx-1" style="width: 100px; height: auto;">`;
            });

            $("#garbageTipModal").find(".photos-container").html(photosHtml);
        } else {
            $("#garbageTipModal")
                .find(".photos-container")
                .html("<p>No existing photos.</p>");
        }

        if (video) {
            $("#garbagetip-form").find('input[name="video"]').val(video);
        } else {
            $("#garbagetip-form").find('input[name="video"]').val("");
        }

        // Set the form action and method
        $("#garbagetip-form").attr("action", `/garbagetip/${garbagetipId}`);
        $("#garbagetip-form").attr("method", "POST");
        $("#garbagetip-form").find('input[name="_method"]').remove();
        $("#garbagetip-form").append(
            '<input type="hidden" name="_method" value="PUT">'
        );

        $("#garbageTipModal").modal("show");

        showGarbageTipModal($(this).data("modaltitle"));
    });

    $(document).on("click", ".show-full-description", function (e) {
        e.preventDefault();
        var fullDescription = $(this).data("tutorialdescription");
        $("#descriptionModal .modal-body").text(fullDescription);
        $("#descriptionModal").modal("show");
    });

    $(document).on("click", ".delete-btn", function () {
        let id = $(this).data("id"); // Retrieves the comment's ID stored in the data-id attribute.

        console.log("Delete button clicked for comment ID: " + id); // Check the ID retrieved

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
                removeGarbageTip(id); // Proceed with deletion by passing the ID
            }
        });
    });

    function removeGarbageTip(id) {
        $.ajax({
            url: "/garbages/comments/" + id, // Make sure this URL matches the new route defined above
            method: "DELETE",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"), // Include CSRF token
            },
            success: function (response) {
                if (response.success) {
                    Swal.fire(
                        "Deleted!",
                        "Your comment has been deleted.",
                        "success"
                    );
                    $(`#comment-${id}`).remove(); // Remove the comment from DOM
                } else {
                    Swal.fire(
                        "Error!",
                        "There was an issue deleting your comment.",
                        "error"
                    );
                }
            },
            error: function (xhr, status, error) {
                console.error("Delete request failed: ", error);
                Swal.fire(
                    "Error!",
                    "There was an issue deleting your comment.",
                    "error"
                );
            },
        });
    }

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

    $("#garbageTipModal").on("hide.bs.modal", function () {
        $("#garbage_container").addClass("d-none").hide();
        $("#recycled_container").addClass("d-none").hide();
        $("#type").val("");
    });

    // // Handle form submission
    $(document).on("click", "#saveGarbageTip", function () {
        let form = $("#garbagetip-form")[0];
        let formData = new FormData(form);

        // Get the description content from TinyMCE
        let descriptionContent = tinymce.get("description").getContent();
        formData.append("description", descriptionContent);

        // Show loader and disable the save button
        showLoader(".saveGarbageTip");
        $("#saveGarbageTip").prop("disabled", true);

        $.ajax({
            url: $(form).attr("action"),
            method: $(form).attr("method"),
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                hideLoader(".saveGarbageTip");
                $("#saveGarbageTip").prop("disabled", false);
                toast(data.type, data.message);
                $("#garbagetip-form")[0].reset();
                $("#garbageTipModal").modal("hide");
                garbageTipsDataTable.ajax.reload();
            },
            error: function (response) {
                if (response.status === 422) {
                    hideLoader(".saveGarbageTip");
                    $("#saveGarbageTip").prop("disabled", false);
                    let errors = response.responseJSON.errors;
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

    function showGarbageTipModal(modalTitle) {
        $("#garbageTipModal").modal("show");
        $("#garbageTipModal .modal-title").text(modalTitle);
    }

    function removeGarbageTip(id) {
        $.ajax({
            type: "DELETE",
            url: `/garbagetip/${id}`,
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
                $("#garbageTipModal").modal("hide");
                garbageTipsDataTable.ajax.reload();
            })
            .fail(function (data) {
                console.log(data);
            });
    }

    function loadGarbageTips() {
        $.ajax({
            url: "/garbagetip-comment-api",
            method: "GET",
            success: function (response) {
                const tutorials = response.posts_garbagetip;

                $(".garbage-tip-list").empty();

                if (tutorials.length > 0) {
                    tutorials.forEach(function (tutorial) {
                        const tutorialTimeAgo = dayjs(
                            tutorial.created_at
                        ).fromNow();

                        // Filter top-level comments (comments without a parent)
                        const topLevelComments =
                            tutorial.post_garbagetip_comments.filter(
                                (comment) => !comment.parent_id
                            );

                        // Create the structure for each tutorial
                        $(".garbage-tip-list").append(`
                    <div class="tutorial-item" data-tutorialid="${tutorial.id}">
                        <div class="row">
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-danger mb-2 rounded-pill report-content" data-tutorialid="${
                                    tutorial.id
                                }">Report</button>
                            </div>
                            <div class="col-xxl-6">
                                <div class="row">
                                    <div class="col-xxl-3">
                                        <img src="${
                                            tutorial.photos.split(",")[0]
                                        }" style="object-fit:cover;width:100%;height:auto;" alt="Main Image">
                                        <div class="row mt-3">
                                            ${tutorial.photos
                                                .split(",")
                                                .slice(1)
                                                .map(
                                                    (photo) => `
                                                <div class="col-4 mb-2">
                                                    <img src="${photo}" class="img-fluid" style="object-fit:cover;width:100%;height:auto;" alt="Additional Image">
                                                </div>
                                            `
                                                )
                                                .join("")}
                                        </div>
                                    </div>

                                    <div class="col-xxl-9">
                                        ${
                                            tutorial.video
                                                ? `
                                            <iframe class="datatable-video" style="object-fit:cover;width:100%;height:100%;" src="https://www.youtube.com/embed/${extractYouTubeVideoId(
                                                tutorial.video
                                            )}" frameborder="0" allowfullscreen></iframe>
                                        `
                                                : ""
                                        }
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-6">
                                <div class="mt-3">${tutorial.description}</div>
                                <div class="d-flex justify-content-between mt-3">
                                    <div><h4 class="fw-bolder">${
                                        tutorial.title
                                    }</h4></div>
                                    <div><p><b>Date Posted:</b> <i>${tutorialTimeAgo}</i></p></div>
                                </div>
                            </div>

                            <div class="col-xxl-12 mt-3">
                                <div class="mb-5 hstack gap-3 align-items-center">
                                    <div class="fs-5 comment-count">${
                                        tutorial.post_garbagetip_comments.length
                                    } Comments</div>
                                </div>

                                <div class="comment-box">
                                    <div class="d-flex comment">
                                        <div class="flex-grow-1 ms-3">
                                            <div class="form-floating comment-compose mb-2">
                                                <textarea class="form-control w-100 new-comment-${
                                                    tutorial.id
                                                }" placeholder="Leave a comment here" style="height:2rem;"></textarea>
                                                <label for="new-comment">Leave a comment here</label>
                                            </div>
                                            <div class="hstack justify-content-end gap-1">
                                              
                                                <button class="btn btn-sm btn-primary rounded-pill tutorial-comment" data-tutorialid="${
                                                    tutorial.id
                                                }">Comment</button>
                                                  <button class="btn btn-sm btn-secondary rounded-pill cancel-comment" data-tutorialid="${
                                                      tutorial.id
                                                  }">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="comment-list vstack gap-4" id="comment-list-${
                                    tutorial.id
                                }">
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                `);
                        $(document).ready(function () {
                            // When the button is clicked
                            $(".report-content").click(function () {
                                contentId = $(this).attr("data-tutorialid");

                                $("#reportContentModal").modal("show");
                                $("#reportContentModal .modal-title").text(
                                    "Report Content"
                                );
                                $("#reportcontent-form").attr(
                                    "action",
                                    "/report-garbagetip"
                                );
                                $("#reportcontent-form").attr("method", "POST");
                                $("#reportcontent-form")[0].reset();
                            });
                        });
                        $(document).ready(function () {
                            // Unbind any previously bound click events before attaching the new one
                            $(".tutorial-comment")
                                .off("click")
                                .on("click", function () {
                                    const tutorialID =
                                        $(this).attr("data-tutorialid");
                                    const replyToID =
                                        $(this).attr("data-replyid");
                                    const comment = $(
                                        `.new-comment-${tutorialID}`
                                    ).val();
                                    sendComment(tutorialID, replyToID, comment);
                                });
                        });

                        // Event listeners for the Share and React buttons
                        $(document).on("click", ".share-button", function () {
                            const tutorialId = $(this).data("tutorialid");
                            const tutorialLink = `http://127.0.0.1:8000/tutorial/${tutorialId}`; // Replace with the actual link
                            navigator.clipboard
                                .writeText(tutorialLink)
                                .then(() => {
                                    alert("Tutorial link copied to clipboard!");
                                })
                                .catch((err) => {
                                    console.error("Failed to copy link: ", err);
                                });
                        });

                        $(document)
                            .off("click", ".react-button")
                            .on("click", ".react-button", function () {
                                const $button = $(this);
                                const $countSpan = $button.find(".react-count");
                                let currentCount = parseInt(
                                    $countSpan.text(),
                                    10
                                );

                                // Ensure the count is a valid number
                                if (isNaN(currentCount)) {
                                    currentCount = 0; // Reset to 0 if it's not a valid number
                                }

                                // Increment the count by 1
                                currentCount++;

                                // Update the displayed count
                                $countSpan.text(currentCount);

                                console.log("Button clicked"); // Check that it's logged once
                            });

                        // Render comments specific to the current tutorial
                        const commentContainer = $(
                            `#comment-list-${tutorial.id}`
                        );

                        tutorial.post_garbagetip_comments.forEach(function (
                            comment
                        ) {
                            if (!comment.parent_id) {
                                // Only display top-level comments
                                const timeAgo = dayjs(
                                    comment.created_at
                                ).fromNow();
                                let repliesHtml = "";

                                // Check if the comment has replies
                                if (
                                    Array.isArray(comment.replies_garbagetip) &&
                                    comment.replies_garbagetip.length > 0
                                ) {
                                    comment.replies_garbagetip.forEach(
                                        function (reply) {
                                            const replyTimeAgo = dayjs(
                                                reply.created_at
                                            ).fromNow();
                                            repliesHtml += `
                                            <div class="d-flex reply">
                                                <img class="rounded-circle comment-img" src="${
                                                    reply.user.profile == null
                                                        ? "assets/back/images/avatar/noprofile.webp"
                                                        : reply.resident.profile
                                                }" width="128" height="128">
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="mb-1">
                                                        <a href="#" class="fw-bold link-body-emphasis pe-1">${
                                                            reply.user
                                                                ?.username ||
                                                            "Unknown"
                                                        }</a>
                                                        <span class="text-body-secondary text-nowrap">${replyTimeAgo}</span>
                                                    </div>
                                                    <div class="mb-1">${
                                                        reply.comment
                                                    }</div>
                                                    <div class="hstack align-items-center" style="margin-left:-.25rem;">
                                                        <button class="icon-btn me-1 like-btn" data-id="${
                                                            reply.id
                                                        }">
                                                            <svg class="svg-icon material-symbols-outlined material-symbols-thumb_up" width="48" height="48">
                                                                <use xlink:href="#google-thumb_up"></use>
                                                            </svg>
                                                        </button>
                                                        <span class="me-1 small">${
                                                            reply.likes
                                                        }</span>
                                                        <button class="icon-btn me-1 dislike-btn" data-id="${
                                                            reply.id
                                                        }">
                                                            <svg class="svg-icon material-symbols-outlined material-symbols-thumb_down" width="48" height="48">
                                                                <use xlink:href="#google-thumb_down"></use>
                                                            </svg>
                                                        </button>
                                                        <span class="me-3 small">${
                                                            reply.dislikes
                                                        }</span>
                                                        <button class="btn btn-sm btn-secondary rounded-pill small reply-btn" data-id="${
                                                            reply.parent_id
                                                        }">Reply</button>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        }
                                    );
                                }

                                // Append the comment along with its replies to the tutorial's specific comment container
                                commentContainer.append(`
                                    <div class="comment-box">
                                        <div class="d-flex comment">
                                            <img class="rounded-circle comment-img" src="${
                                                comment.user &&
                                                comment.user.profile
                                                    ? comment.user.profile
                                                    : "assets/back/images/avatar/noprofile.webp"
                                            }" width="128" height="128">
                                            <div class="flex-grow-1 ms-3">
                                                <div class="mb-1">
                                                    <a href="#" class="fw-bold link-body-emphasis me-1">${
                                                        comment.user
                                                            ? comment.user
                                                                  .username
                                                            : "Unknown"
                                                    }</a>
                                                    <span class="text-body-secondary text-nowrap">${timeAgo}</span>
                                                </div>
                                                <div class="mb-1">${
                                                    comment.comment
                                                }</div>
                                                <div class="hstack align-items-center mb-0">
                                                    <button class="icon-btn like-btn" data-id="${
                                                        comment.id
                                                    }">
                                                        <svg class="svg-icon" width="24" height="24">
                                                            <use xlink:href="#google-thumb_up"></use>
                                                        </svg>
                                                    </button>
                                                    <span class="me-1 small">${
                                                        comment.likes
                                                    }</span>
                                                    <button class="icon-btn dislike-btn" data-id="${
                                                        comment.id
                                                    }">
                                                        <svg class="svg-icon" width="24" height="24">
                                                            <use xlink:href="#google-thumb_down"></use>
                                                        </svg>
                                                    </button>
                                                    <span class="me-3 small">${
                                                        comment.dislikes
                                                    }</span>
                                                    <button class="btn btn-sm btn-secondary rounded-pill small mx-1 reply-btn" data-id="${
                                                        comment.id
                                                    }">Reply</button>
                                                 <button class="btn btn-sm btn-danger rounded-pill small delete-btn" data-id="${
                                                     comment.id
                                                 }" id="comment-{{ $comment->id }}">Delete</button>

                                                </div>
                                                <div class="collapse" id="collapse-comment${
                                                    comment.id
                                                }">
                                                    <div class="comment-replies vstack gap-3 mt-1 bg-body-tertiary p-3 rounded-3">
                                                        ${repliesHtml}
                                                    </div>
                                                </div>
                                                <div style="margin-left:-.769rem;">
                                                    <button class="btn btn-primary rounded-pill d-inline-flex align-items-center" data-bs-toggle="collapse" data-bs-target="#collapse-comment${
                                                        comment.id
                                                    }" aria-expanded="false" aria-controls="collapse-comment${
                                    comment.id
                                }">
                                                        <i class="chevron-down zmdi zmdi-chevron-down fs-4 me-2"></i>
                                                        <i class="chevron-up zmdi zmdi-chevron-up fs-4 me-2"></i>
                                                        <span>${
                                                            comment.replies_garbagetip
                                                                ? comment
                                                                      .replies_garbagetip
                                                                      .length
                                                                : 0
                                                        } replies</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                            }

                            $(".reply-btn").on("click", function () {
                                const commentId = $(this).data("id");
                                const tutorialId = $(this)
                                    .closest(".tutorial-item")
                                    .data("tutorialid");
                                showReplyForm(commentId, tutorialId);
                            });

                            // Event listener for delete button
                            /*  $(".delete-btn").on("click", function () {
                                const commentId = $(this).data("id");
                                deleteComment(commentId);
                            }); */
                        });
                    });

                    $(".cancel-comment").on("click", function () {
                        const tutorialID = $(this).attr("data-tutorialid");
                        $(this)
                            .closest(".comment-box")
                            .find(`.new-comment-${tutorialID}`)
                            .val("");
                    });

                    $(".like-btn, .dislike-btn").on("click", function () {
                        const commentId = $(this).data("id");
                        const action = $(this).hasClass("like-btn")
                            ? "like"
                            : "dislike";
                        sendLikes(commentId, action);
                    });
                } else {
                    $(".garbage-tip-list").append(`No comment found.`);
                }
            },
        });
    }

    loadGarbageTips();

    function sendComment(tutorialID, replyToID, comment) {
        if (comment) {
            $.ajax({
                url: "/comment-garbagetip",
                method: "POST",
                data: {
                    comment: comment,
                    garbagetip_id: tutorialID,
                    parent_id: replyToID,
                },
                beforeSend: function (xhr) {
                    xhr.setRequestHeader(
                        "X-CSRF-TOKEN",
                        $('meta[name="csrf-token"]').attr("content")
                    );
                },
                success: function (response) {
                    console.log(response); // Log the response
                    $(".new-comment").val(""); // Clear comment box
                    loadGarbageTips(); // Reload comments
                },
            });
        }
    }

    function sendLikes(commentId, action) {
        $.ajax({
            url: `/comment-garbagetip/${action}/${commentId}`,
            method: "POST",
            beforeSend: function (xhr) {
                xhr.setRequestHeader(
                    "X-CSRF-TOKEN",
                    $('meta[name="csrf-token"]').attr("content")
                );
            },
            success: function (response) {
                // Reload comments
                loadGarbageTips();
            },
            error: function (xhr) {
                console.log("Error:", xhr);
            },
        });
    }

    function showReplyForm(commentId, tutorialId) {
        const commentBox = $(`.new-comment-${tutorialId}`);
        $("html, body").animate(
            {
                scrollTop: commentBox.offset().top - 100,
            },
            500
        );

        commentBox.focus();

        $(`.tutorial-comment[data-tutorialid="${tutorialId}"]`).attr(
            "data-replyid",
            commentId
        );
    }

    $(document).on("click", "#saveReportContent", function () {
        let form = $("#reportcontent-form")[0];
        let formData = new FormData(form);

        let descriptionContent = tinymce.get("re_description").getContent();
        formData.append("report_message", descriptionContent);

        if (typeof contentId !== "undefined" && contentId) {
            formData.append("content_id", contentId);
        }

        // Show loader and disable the save button
        showLoader(".saveReportContent");
        $("#saveReportContent").prop("disabled", true);

        $.ajax({
            url: $(form).attr("action"),
            method: $(form).attr("method"),
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                hideLoader(".saveReportContent");
                $("#saveReportContent").prop("disabled", false);

                // Reset form and hide modal
                $("#reportcontent-form")[0].reset();
                $("#reportContentModal").modal("hide");

                Swal.fire({
                    title: data.message,
                    text: "Our administrator will review this content. Thank you.",
                    icon: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Okay",
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Note",
                            text: "We will remove this content if it is proven that there is something wrong with it.",
                            icon: "info",
                        });
                    }
                });
            },
            error: function (response) {
                hideLoader(".saveReportContent");
                $("#saveReportContent").prop("disabled", false);

                if (response.status === 422) {
                    let errors = response.responseJSON.errors;
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
});
