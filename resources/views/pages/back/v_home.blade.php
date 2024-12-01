@extends('layouts.back.app')

@section('content')
<div class="app-content-area pt-0 ">
    <div class="bg-primary pt-12 pb-21 "></div>
    <div class="container-fluid mt-n22 ">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <!-- Page header -->
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div class="mb-2 mb-lg-0">
                        <h3 class="mb-0  text-white">
                            @if(Auth::user()->role === 'Superadmin')
                            Hello! Superadmin
                            @elseif(Auth::user()->role === 'LGU')
                            Hello! LGU's
                            @elseif(Auth::user()->role === 'NGO')
                            Hello! NGO's
                            @elseif(Auth::user()->role === 'Resident')
                            Hello! {{ Auth::user()->username }}
                            @endif
                        </h3>
                    </div>
                    <div>




                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            @if(in_array(Auth::user()->role, ['Resident', 'LGU','Superadmin', 'NGO']))


            @if(Auth::user()->role === 'Superadmin')
            <div class="col-xl-3 col-lg-6 col-md-12 col-12 mb-5">
                <!-- card -->
                <div class="card h-100 card-lift">
                    <!-- card body -->
                    <div class="card-body">
                        <!-- heading -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="mb-0">Total Resident</h4>
                            </div>
                            <div class="icon-shape icon-md bg-primary-soft text-primary rounded-2">
                                <i data-feather="users" height="20" width="20"></i>
                            </div>
                        </div>
                        <!-- project number -->
                        <div class="lh-1">
                            <h1 class=" mb-1 fw-bold">{{ $residentCount }}</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-12 col-12 mb-5">
                <!-- card -->
                <div class="card h-100 card-lift">
                    <!-- card body -->
                    <div class="card-body">
                        <!-- heading -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="mb-0">Total NGO</h4>
                            </div>
                            <div class="icon-shape icon-md bg-primary-soft text-primary rounded-2">
                                <i data-feather="users" height="20" width="20"></i>
                            </div>
                        </div>
                        <!-- project number -->
                        <div class="lh-1">
                            <h1 class="  mb-1 fw-bold">{{ $ngoCount }}</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-12 col-12 mb-5">
                <!-- card -->
                <div class="card h-100 card-lift">
                    <!-- card body -->
                    <div class="card-body">
                        <!-- heading -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="mb-0">Total LGU</h4>
                            </div>
                            <div class="icon-shape icon-md bg-primary-soft text-primary rounded-2">
                                <i data-feather="users" height="20" width="20"></i>
                            </div>
                        </div>
                        <!-- project number -->
                        <div class="lh-1">
                            <h1 class="  mb-1 fw-bold">{{ $lguCount }}</h1>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-xl-3 col-lg-6 col-md-12 col-12 mb-5">
                <!-- card -->
                <div class="card h-100 card-lift">
                    <!-- card body -->
                    <div class="card-body">
                        <!-- heading -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="mb-0">For Approval Subscription</h4>
                            </div>
                            <div class="icon-shape icon-md bg-primary-soft text-primary rounded-2">
                                <i data-feather="briefcase" height="20" width="20"></i>
                            </div>
                        </div>
                        <!-- project number -->
                        <div class="lh-1">
                            <h1 class="  mb-1 fw-bold">{{ $subPending }}</h1>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                    <!-- Card -->
                    <div>
                        <!-- Card body -->
                        <div class="row ">
                            <div class="col-xxl-6 col-lg-4">

                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h4 class="mb-0">Recycled Items</h4>
                                    </div>

                                    <div class="card-body post-recycled-list">

                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-6 col-lg-8 col-12 ">
                                <div class="mt-6 mt-lg-0">

                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h4 class="mb-0">Garbage Report</h4>
                                        </div>

                                        <div class="card-body post-garbage-list">

                                        </div>

                                    </div>


                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {

        function loadPostGarbage() {
            $.ajax({
                url: '/post-report',
                method: 'GET',
                success: function(response) {
                    const posts = response.posts_garbage;

                    $('.post-garbage-list').empty();

                    if (posts.length > 0) {
                        posts.forEach(function(post) {

                            // console.log(post)
                            const postTimeAgo = dayjs(post.created_at).fromNow();

                            // Filter top-level comments (comments without a parent)
                            const topLevelComments = post.postcomments.filter(comment => !comment.parent_id);


                            const photos = post.photo ? post.photo.split(',') : [];
                            const photoElements = photos.map(photo => `
                                <div class="col-6 mb-2"> <!-- Each image will take half the width of the row -->
                                    <img src="${photo.trim() !== '' ? photo : 'assets/back/images/brand/logo/noimage.jpg'}" style="object-fit:cover;width:100%;max-width:100px;max-height:100px;height:auto;">
                                </div>
                            `).join('');


                            let videoElement = '';
                            if (post.video_url && post.video_url.trim() !== '') {
                                if (isYouTubeURL(post.video_url)) {
                                    const videoId = extractYouTubeVideoId(post.video_url);
                                    videoElement = `<iframe class="datatable-video" height="200" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen style="width:100%;"></iframe>`;
                                } else {
                                    videoElement = `
                                        <video class="datatable-video" width="150" height="100" controls  style="width:100%;">
                                            <source src="${post.video_url}" type="video/mp4"> <!-- Adjust type as necessary -->
                                            Your browser does not support the video tag.
                                        </video>
                                    `;
                                }
                            }

                            // Create the structure for each post
                            $('.post-garbage-list').append(`
                                <div class="post-item" data-postid="${post.id}">

                                <div class="row">
                                                    
                                    <div class="col-xxl-6">
                                        <div class="row">
                                            ${photoElements}
                                        </div>
                                    </div>
                                    <div class="col-xxl-6">
                                        ${videoElement}
                                       
                                    </div>
                                    <div class="col-xxl-12 mt-3">
                                        

                                     ${post.description}
                                        <div class="d-flex justify-content-between">
                                            <div><p><b>Address:</b> <i>${post.address}</i></p></div>
                                           <div>
                                                <p><b>Status:</b> 
                                                    <span>${post.status === 'Accepted' ? 'Area Cleaned' 
                                                        : post.status === 'Pending' ? 'Area not yet Cleaned' 
                                                        : post.status === 'Rejected' ? 'Report Rejected' : ''}</span>
                                                </p>
                                            </div>
                                            <div><p><b>Posted Date:</b> <i>${postTimeAgo}</i></p></div>
                                            
                                        </div>
                                        @if(Auth::user()->role === 'LGU')
 <div>
        
                <p>
                    ${post.status === 'Pending' ? `
      
                        <button class="btn btn-sm btn-success approve-button" data-postid="${post.id}">Approve</button> 
                        <button class="btn btn-sm btn-danger rejected-button" data-postid="${post.id}">Reject</button>
                    ` : ''}
                </p>
            </div>
            @endif

                                        <div class="mb-5 hstack gap-3 align-items-center">
                                            <div class="fs-5 comment-count">${topLevelComments.length} Comments</div> <!-- Use topLevelComments -->
                                        </div>
                                        

                                        <div class="comment-box">
                                            <div class="d-flex comment">
                                                <div class="flex-grow-1">
                                                    <div class="form-floating comment-compose mb-2">
                                                        <textarea class="form-control w-100 new-comment-${post.id}" placeholder="Leave a comment here" style="height:2rem;"></textarea>
                                                        <label for="new-comment">Leave a comment here</label>
                                                    </div>
                                                    <div class="hstack justify-content-end gap-1">
                                                        <button class="btn btn-sm btn-primary rounded-pill post-comment" data-postid="${post.id}">Comment</button> 
                                                          <button class="btn btn-sm btn-secondary rounded-pill cancel-comment" data-postid="${post.id}">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="comment-list vstack gap-4" id="comment-list-${post.id}"></div> <!-- Unique comment list for each post -->
                                    </div>

                                    </div>
                                    <hr>
                                </div>
                            `);

                            // Render comments specific to the current post
                            const commentContainer = $(`#comment-list-${post.id}`);

                            post.postcomments.forEach(function(comment) {

                                if (!comment.parent_id) { // Only display top-level comments
                                    const timeAgo = dayjs(comment.created_at).fromNow();
                                    let repliesHtml = '';

                                    // Check if the comment has replies
                                    if (Array.isArray(comment.replies) && comment.replies.length > 0) {
                                        comment.replies.forEach(function(reply) {
                                            const replyTimeAgo = dayjs(reply.created_at).fromNow();
                                            repliesHtml += `
                                    <div class="d-flex reply">
                                        <img class="rounded-circle comment-img" 
                                        src="${reply.resident && reply.resident.profile ? reply.resident.profile : 'assets/back/images/avatar/noprofile.webp'}" width="128" height="128">
                                        <div class="flex-grow-1 ms-3">
                                            <div class="mb-1">
                                                <a href="#" class="fw-bold link-body-emphasis pe-1">
                                                ${reply.resident ? reply.resident.username : 'Unknown'}</a>
                                                <span class="text-body-secondary text-nowrap">${replyTimeAgo}</span>
                                            </div>
                                            <div class="mb-1">${reply.comment}</div>
                                            <div class="hstack align-items-center" style="margin-left:-.25rem;">
                                                <button class="icon-btn me-1 like-btn" data-id="${reply.id}">
                                                    <svg class="svg-icon material-symbols-outlined material-symbols-thumb_up" width="48" height="48">
                                                        <use xlink:href="#google-thumb_up"></use>
                                                    </svg>
                                                </button>
                                                <span class="me-1 small">${reply.likes}</span>
                                                <button class="icon-btn me-1 dislike-btn" data-id="${reply.id}">
                                                    <svg class="svg-icon material-symbols-outlined material-symbols-thumb_down" width="48" height="48">
                                                        <use xlink:href="#google-thumb_down"></use>
                                                    </svg>
                                                </button>
                                                 <span class="me-3 small">${reply.dislikes}</span>
                                                <button class="btn btn-sm btn-secondary rounded-pill small reply-btn" data-id="${reply.parent_id}">Reply</button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                        });
                                    }



                                    // Append the comment along with its replies to the post's specific comment container
                                    commentContainer.append(`
                                    <div class="comment-box">
                                        <div class="d-flex comment">
                                            <img class="rounded-circle comment-img" src="${comment.resident && comment.resident.profile ? comment.resident.profile : 'assets/back/images/avatar/noprofile.webp'}" width="128" height="128">
                                            <div class="flex-grow-1 ms-3">
                                                <div class="mb-1">
                                                    <a href="#" class="fw-bold link-body-emphasis me-1">${comment.resident ? comment.resident.username : 'Unknown'}</a>
                                                    <span class="text-body-secondary text-nowrap">${timeAgo}</span>
                                                </div>
                                                <div class="mb-1">${comment.comment}</div>
                                                <div class="hstack align-items-center mb-0">
                                                    <button class="icon-btn like-btn" data-id="${comment.id}">
                                                        <svg class="svg-icon" width="24" height="24">
                                                            <use xlink:href="#google-thumb_up"></use>
                                                        </svg>
                                                    </button>
                                                    <span class="me-1 small">${comment.likes}</span>
                                                    <button class="icon-btn dislike-btn" data-id="${comment.id}">
                                                        <svg class="svg-icon" width="24" height="24">
                                                            <use xlink:href="#google-thumb_down"></use>
                                                        </svg>
                                                    </button>
                                                     <span class="me-3 small">${comment.dislikes}</span>
                                                    <button class="btn btn-sm btn-secondary rounded-pill small mx-1 reply-btn" data-id="${comment.id}">Reply</button>
                                                    <button class="btn btn-sm btn-danger rounded-pill small delete-btn" data-id="${comment.id}">Delete</button>
                                                </div>
                                                <div class="collapse" id="collapse-comment${comment.id}">
                                                    <div class="comment-replies vstack gap-3 mt-1 bg-body-tertiary p-3 rounded-3">
                                                        ${repliesHtml}
                                                    </div>
                                                </div>
                                                <div style="margin-left:-.769rem;">
                                                    <button class="btn btn-primary rounded-pill d-inline-flex align-items-center" data-bs-toggle="collapse" data-bs-target="#collapse-comment${comment.id}" aria-expanded="false" aria-controls="collapse-comment${comment.id}">
                                                        <i class="chevron-down zmdi zmdi-chevron-down fs-4 me-2"></i>
                                                        <i class="chevron-up zmdi zmdi-chevron-up fs-4 me-2"></i>
                                                        <span>${comment.replies ? comment.replies.length : 0} replies</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                                }


                                $('.reply-btn').on('click', function() {
                                    const commentId = $(this).data('id');
                                    const postId = $(this).closest('.post-item').data('postid');
                                    showReplyForm(commentId, postId);
                                });

                                // Event listener for delete button
                                $('.delete-btn').on('click', function() {
                                    const commentId = $(this).data('id');
                                    deleteComment(commentId);
                                });
                            });

                        });

                        $('.post-comment').on('click', function() {
                            const postID = $(this).attr('data-postid');
                            const replyToID = $(this).attr('data-replyid');
                            const comment = $(`.new-comment-${postID}`).val();
                            sendComment(postID, replyToID, comment)
                        });

                        $('.cancel-comment').on('click', function() {
                            const postID = $(this).attr('data-postid');
                            $(this).closest('.comment-box').find(`.new-comment-${postID}`).val('');
                        });

                        $('.like-btn, .dislike-btn').on('click', function() {
                            const commentId = $(this).data('id');
                            const action = $(this).hasClass('like-btn') ? 'like' : 'dislike';
                            sendLikes(commentId, action);

                        });

                    } else {
                        $('.post-garbage-list').append(`No data or accepted garbage post yet.`);
                    }



                }
            });
        }

        loadPostGarbage();


        function loadPostRecycled() {
            $.ajax({
                url: '/post-recycled',
                method: 'GET',
                success: function(response) {
                    const posts = response.posts_recycled;

                    $('.post-recycled-list').empty();

                    if (posts.length > 0) {
                        posts.forEach(function(post) {
                            const postTimeAgo = dayjs(post.created_at).fromNow();

                            // Filter top-level comments (comments without a parent)
                            const topLevelComments = post.postcomments.filter(comment => !comment.parent_id);

                            const photos = post.photo ? post.photo.split(',') : [];
                            const photoElements = photos.map(photo => `
                                <div class="col-6 mb-2"> <!-- Each image will take half the width of the row -->
                                    <img src="${photo.trim() !== '' ? photo : 'assets/back/images/brand/logo/noimage.jpg'}" style="object-fit:cover;width:100%;max-width:100px;max-height:100px;height:auto;">
                                </div>
                            `).join('');

                            // Create the structure for each post
                            $('.post-recycled-list').append(`
                                 <div class="post-recycled-item" data-postid="${post.id}">

                                <div class="row">
                                    <div class="d-flex justify-content-end"> <button class="btn btn-primary rounded-0 mt-3 mb-3 privateMessage" data-username="${post.resident ? post.resident.username : 'Unknown'}"><i class="bi bi-chat-left-dots"></i></button></div>   
                                    <div class="col-xxl-6">
                                        <div class="row">
                                        ${photoElements}
                                        </div>
                                    </div>
                                    <div class="col-xxl-6">
                                        ${post.description}
                                        <p><b>Posted Date:</b> <i>${postTimeAgo}</i></p>
                                       </div>
                                    </div>



                                     <div class="col-xxl-12 mt-3">
                        

                                        <div class="mb-5 hstack gap-3 align-items-center">
                                            <div class="fs-5 comment-count">${topLevelComments.length} Comments</div> <!-- Use topLevelComments -->
                                        </div>
                                        

                                        <div class="comment-box">
                                            <div class="d-flex comment">
                                                <div class="flex-grow-1">
                                                    <div class="form-floating comment-compose mb-2">
                                                        <textarea class="form-control w-100 new-recycled-comment-${post.id}" placeholder="Leave a comment here" style="height:2rem;"></textarea>
                                                        <label for="new-comment">Leave a comment here</label>
                                                    </div>
                                                    <div class="hstack justify-content-end gap-1">
                                                        <button class="btn btn-sm btn-primary rounded-pill post-recycled-comment" data-postid="${post.id}">Comment</button> 
                                                     <button class="btn btn-sm btn-secondary rounded-pill cancel-recycled-comment" data-postid="${post.id}">Cancel</button>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="comment-list vstack gap-4" id="comment-recycled-list-${post.id}"></div> <!-- Unique comment list for each post -->
                                    </div>
                            
                                    <hr>
                                </div>

                                </div>


                                
                            `);


                            // Render comments specific to the current post
                            const commentContainer = $(`#comment-recycled-list-${post.id}`);

                            post.postcomments.forEach(function(comment) {

                                if (!comment.parent_id) { // Only display top-level comments
                                    const timeAgo = dayjs(comment.created_at).fromNow();
                                    let repliesHtml = '';

                                    // Check if the comment has replies
                                    if (Array.isArray(comment.replies) && comment.replies.length > 0) {
                                        comment.replies.forEach(function(reply) {
                                            const replyTimeAgo = dayjs(reply.created_at).fromNow();
                                            repliesHtml += `
                                            <div class="d-flex reply">
                                                <img class="rounded-circle comment-img" 
                                                src="${reply.resident && reply.resident.profile ? reply.resident.profile : 'assets/back/images/avatar/noprofile.webp'}" width="128" height="128">
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="mb-1">
                                                        <a href="#" class="fw-bold link-body-emphasis pe-1">
                                                        ${reply.resident ? reply.resident.username : 'Unknown'}</a>
                                                        <span class="text-body-secondary text-nowrap">${replyTimeAgo}</span>
                                                    </div>
                                                    <div class="mb-1">${reply.comment}</div>
                                                    <div class="hstack align-items-center" style="margin-left:-.25rem;">
                                                        <button class="icon-btn me-1 like-recycled-btn" data-id="${reply.id}">
                                                            <svg class="svg-icon material-symbols-outlined material-symbols-thumb_up" width="48" height="48">
                                                                <use xlink:href="#google-thumb_up"></use>
                                                            </svg>
                                                        </button>
                                                        <span class="me-1 small">${reply.likes}</span>
                                                        <button class="icon-btn me-1 dislike-recycled-btn" data-id="${reply.id}">
                                                            <svg class="svg-icon material-symbols-outlined material-symbols-thumb_down" width="48" height="48">
                                                                <use xlink:href="#google-thumb_down"></use>
                                                            </svg>
                                                        </button>
                                                        <span class="me-3 small">${reply.dislikes}</span>
                                                        <button class="btn btn-sm btn-secondary rounded-pill small reply-recycled-btn" data-id="${reply.parent_id}">Reply</button>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        });
                                    }



                                    // Append the comment along with its replies to the post's specific comment container
                                    commentContainer.append(`
                                    <div class="comment-box">
                                        <div class="d-flex comment">
                                            <img class="rounded-circle comment-img" src="${comment.resident && comment.resident.profile ? comment.resident.profile : 'assets/back/images/avatar/noprofile.webp'}" width="128" height="128">
                                            <div class="flex-grow-1 ms-3">
                                                <div class="mb-1">
                                                    <a href="#" class="fw-bold link-body-emphasis me-1">${comment.resident ? comment.resident.username : 'Unknown'}</a>
                                                    <span class="text-body-secondary text-nowrap">${timeAgo}</span>
                                                </div>
                                                <div class="mb-1">${comment.comment}</div>
                                                <div class="hstack align-items-center mb-0">
                                                    <button class="icon-btn like-btn" data-id="${comment.id}">
                                                        <svg class="svg-icon" width="24" height="24">
                                                            <use xlink:href="#google-thumb_up"></use>
                                                        </svg>
                                                    </button>
                                                    <span class="me-1 small">${comment.likes}</span>
                                                    <button class="icon-btn dislike-btn" data-id="${comment.id}">
                                                        <svg class="svg-icon" width="24" height="24">
                                                            <use xlink:href="#google-thumb_down"></use>
                                                        </svg>
                                                    </button>
                                                    <span class="me-3 small">${comment.dislikes}</span>
                                                    <button class="btn btn-sm btn-secondary rounded-pill small mx-1 reply-recycled-btn" data-id="${comment.id}">Reply</button>
                                                    <button class="btn btn-sm btn-danger rounded-pill small delete-recycled-btn" data-id="${comment.id}">Delete</button>
                                                </div>
                                                <div class="collapse" id="collapse-comment${comment.id}">
                                                    <div class="comment-replies vstack gap-3 mt-1 bg-body-tertiary p-3 rounded-3">
                                                        ${repliesHtml}
                                                    </div>
                                                </div>
                                                <div style="margin-left:-.769rem;">
                                                    <button class="btn btn-primary rounded-pill d-inline-flex align-items-center" data-bs-toggle="collapse" data-bs-target="#collapse-comment${comment.id}" aria-expanded="false" aria-controls="collapse-comment${comment.id}">
                                                        <i class="chevron-down zmdi zmdi-chevron-down fs-4 me-2"></i>
                                                        <i class="chevron-up zmdi zmdi-chevron-up fs-4 me-2"></i>
                                                        <span>${comment.replies ? comment.replies.length : 0} replies</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                                }


                                $('.reply-recycled-btn').on('click', function() {
                                    const commentId = $(this).data('id');
                                    const postId = $(this).closest('.post-recycled-item').data('postid');
                                    showRecycledReplyForm(commentId, postId);
                                });

                                // Event listener for delete button
                                $('.delete-recycled-btn').on('click', function() {
                                    const commentId = $(this).data('id');
                                    deleteRecycledComment(commentId);
                                });
                            });

                            $(document).on('click', '.privateMessage', function() {
                                const username = $(this).data('username');
                                if (username) {
                                    window.location.href = `/message?username=${encodeURIComponent(username)}`;
                                } else {
                                    console.error('Username not found');
                                }
                            });

                        });


                        $('.post-recycled-comment').on('click', function() {
                            const postID = $(this).attr('data-postid');
                            const replyToID = $(this).attr('data-replyid');
                            const comment = $(`.new-recycled-comment-${postID}`).val();
                            sendRecycledComment(postID, replyToID, comment)
                        });

                        $('.cancel-recycled-comment').on('click', function() {
                            const postID = $(this).attr('data-postid');
                            $(this).closest('.comment-box').find(`.new-recycled-comment-${postID}`).val('');
                        });

                        $('.like-recycled-btn, .dislike-recycled-btn').on('click', function() {
                            const commentId = $(this).data('id');
                            const action = $(this).hasClass('like-btn') ? 'like' : 'dislike';
                            sendRecycledLikes(commentId, action);

                        });

                    } else {
                        $('.post-recycled-list').append(`No data or accepted recycled post yet.`);
                    }

                }
            });
        }

        loadPostRecycled();

        function sendComment(postID, replyToID, comment) {

            if (comment) {
                $.ajax({
                    url: '/comment',
                    method: 'POST',
                    data: {
                        comment: comment,
                        post_report_id: postID,
                        parent_id: replyToID
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    },
                    success: function(response) {
                        // Clear the comment box
                        $('.new-comment').val('');
                        // Reload comments
                        loadPostGarbage();
                    }
                });
            }
        }

        function sendRecycledComment(postID, replyToID, comment) {

            if (comment) {
                $.ajax({
                    url: '/comment',
                    method: 'POST',
                    data: {
                        comment: comment,
                        post_report_id: postID,
                        parent_id: replyToID
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    },
                    success: function(response) {
                        // Clear the comment box
                        $('.new-recycled-comment').val('');
                        // Reload comments
                        loadPostRecycled();
                    }
                });
            }
        }


        function sendLikes(commentId, action) {

            $.ajax({
                url: `/comment/${action}/${commentId}`,
                method: 'POST',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                },
                success: function(response) {
                    // Reload comments
                    loadPostGarbage();
                },
                error: function(xhr) {
                    console.log('Error:', xhr);
                }
            });
        }

        function sendRecycledLikes(commentId, action) {

            $.ajax({
                url: `/comment/${action}/${commentId}`,
                method: 'POST',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                },
                success: function(response) {
                    // Reload comments
                    loadPostRecycled();
                },
                error: function(xhr) {
                    console.log('Error:', xhr);
                }
            });
        }

        function showReplyForm(commentId, postId) {
            const commentBox = $(`.new-comment-${postId}`);

            $('html, body').animate({
                scrollTop: commentBox.offset().top - 100
            }, 500);

            commentBox.focus();

            $(`.post-comment[data-postid="${postId}"]`).attr('data-replyid', commentId);
        }

        function showRecycledReplyForm(commentId, postId) {
            const commentBox = $(`.new-recycled-comment-${postId}`);

            $('html, body').animate({
                scrollTop: commentBox.offset().top - 100
            }, 500);

            commentBox.focus();

            $(`.post-comment[data-postid="${postId}"]`).attr('data-replyid', commentId);
        }

        function deleteComment(commentId) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        type: 'POST',
                        url: '/post-destroy',
                        data: {
                            commentid: commentId
                        },
                        dataType: 'json',
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                        }
                    }).done(function(data) {
                        loadPostGarbage();
                    }).fail(function(data) {
                        console.log(data)
                    });
                }
            });
        }

        function deleteRecycledComment(commentId) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        type: 'POST',
                        url: '/post-destroy',
                        data: {
                            commentid: commentId
                        },
                        dataType: 'json',
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                        }
                    }).done(function(data) {
                        loadPostGarbage();
                    }).fail(function(data) {
                        console.log(data)
                    });
                }
            });
        }

    });

    $(document).on('click', '.approve-button', function() {
        const postId = $(this).data('postid');

        $.ajax({
            url: '/post/approve',
            type: 'POST',
            data: {
                post_id: postId,
                _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function(response) {
                alert(response.message); // Show success message
                // Optionally, update the UI to reflect the new status
                $(`.post-item[data-postid="${postId}"] .status-span`).text('Accepted');
                location.reload();
            },
            error: function(xhr) {
                alert(xhr.responseJSON.message || 'An error occurred');
            }
        });
    });

    $(document).on('click', '.rejected-button', function() {
        const postId = $(this).data('postid');

        $.ajax({
            url: '/post/rejected',
            type: 'POST',
            data: {
                post_id: postId,
                _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function(response) {
                alert(response.message); // Show success message
                // Optionally, update the UI to reflect the new status
                $(`.post-item[data-postid="${postId}"] .status-span`).text('Rejected');
                location.reload();
            },
            error: function(xhr) {
                alert(xhr.responseJSON.message || 'An error occurred');
            }
        });
    });
</script>
@endpush