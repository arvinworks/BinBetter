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
                        <h3 class="mb-0  text-white">{{ $page }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- row  -->
        <div class="row ">
            <div class="col-xl-12 col-12 mb-5">
                <!-- card  -->
                <div class="card">

                    <!-- table  -->
                    <div class="card-body p-0">

                        <!-- char-area -->
                        <section class="message-area">

                            <div class="row">
                                <div class="col-12">
                                    <div class="chat-area">
                                        <!-- chatlist -->
                                        <div class="chatlist">
                                            <div class="modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="chat-header">
                                                        <div class="msg-search p-3">
                                                            <input type="text" class="form-control w-100" id="inlineFormInputGroup" placeholder="Search" aria-label="search">
                                                            <!-- <a class="add" href="#"><img class="img-fluid" src="https://mehedihtml.com/chatbox/assets/img/add.svg" alt="add"></a> -->
                                                        </div>

                                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link active" id="Open-tab" data-bs-toggle="tab" data-bs-target="#Open" type="button" role="tab" aria-controls="Open" aria-selected="true">Online</button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="Closed-tab" data-bs-toggle="tab" data-bs-target="#Closed" type="button" role="tab" aria-controls="Closed" aria-selected="false">Offline</button>
                                                            </li>
                                                        </ul>
                                                    </div>

                                                    <div class="modal-body p-5">
                                                        <!-- chat-list -->
                                                        <div class="chat-lists">
                                                            <div class="tab-content" id="myTabContent">
                                                                <div class="tab-pane fade show active" id="Open" role="tabpanel" aria-labelledby="Open-tab">
                                                                    <!-- chat-list -->
                                                                    <div class="chat-list"></div>
                                                                    <!-- chat-list -->
                                                                </div>
                                                                <div class="tab-pane fade" id="Closed" role="tabpanel" aria-labelledby="Closed-tab">
                                                                    <!-- chat-list -->
                                                                    <div class="chat-list"></div>
                                                                    <!-- chat-list -->
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <!-- chat-list -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- chatlist -->

                                        <!-- chatbox -->
                                        <div class="chatbox">
                                            <div class="modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="msg-head">
                                                        <div class="row">
                                                            <div class="col-8">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="chat-icon"><img class="img-fluid" src="https://mehedihtml.com/chatbox/assets/img/arroleftt.svg" alt="image title"></span>
                                                                    <div class="flex-shrink-0">
                                                                        <img class="img-fluid" src="" alt="user img" width="35">
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h3 id="msg-head-name"></h3>
                                                                        <p id="msg-head-role"></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <ul class="moreoption">
                                                                    <li class="navbar nav-item dropdown">
                                                                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a class="dropdown-item" href="#">Action</a></li>
                                                                            <li><a class="dropdown-item" href="#">Another action</a></li>
                                                                            <li>
                                                                                <hr class="dropdown-divider">
                                                                            </li>
                                                                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                                                                        </ul>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="modal-body">
                                                        <div class="msg-body" id="msg-body-list">
                                                            <ul></ul>
                                                        </div>
                                                    </div>


                                                    <div class="send-box">
                                                        <form action="" method="post">
                                                            <input type="text" class="form-control" aria-label="message…" id="msg-text" placeholder="Write message…">

                                                            <button type="submit"><i class="fa fa-paper-plane" aria-hidden="true" onClick="scrollToEnd('.my_class')"></i> Send</button>
                                                        </form>

                                                        <div class="send-btns d-none">
                                                            <div class="attach">
                                                                <div class="button-wrapper">
                                                                    <span class="label">
                                                                        <img class="img-fluid" src="https://mehedihtml.com/chatbox/assets/img/upload.svg" alt="image title"> attached file
                                                                    </span><input type="file" name="upload" id="upload" class="upload-box" placeholder="Upload File" aria-label="Upload File">
                                                                </div>

                                                                <select class="form-control" id="exampleFormControlSelect1">
                                                                    <option>Select template</option>
                                                                    <option>Template 1</option>
                                                                    <option>Template 2</option>
                                                                </select>

                                                                <div class="add-apoint">
                                                                    <a href="#" data-toggle="modal" data-target="#exampleModal4"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewbox="0 0 16 16" fill="none">
                                                                            <path d="M8 16C3.58862 16 0 12.4114 0 8C0 3.58862 3.58862 0 8 0C12.4114 0 16 3.58862 16 8C16 12.4114 12.4114 16 8 16ZM8 1C4.14001 1 1 4.14001 1 8C1 11.86 4.14001 15 8 15C11.86 15 15 11.86 15 8C15 4.14001 11.86 1 8 1Z" fill="#7D7D7D" />
                                                                            <path d="M11.5 8.5H4.5C4.224 8.5 4 8.276 4 8C4 7.724 4.224 7.5 4.5 7.5H11.5C11.776 7.5 12 7.724 12 8C12 8.276 11.776 8.5 11.5 8.5Z" fill="#7D7D7D" />
                                                                            <path d="M8 12C7.724 12 7.5 11.776 7.5 11.5V4.5C7.5 4.224 7.724 4 8 4C8.276 4 8.5 4.224 8.5 4.5V11.5C8.5 11.776 8.276 12 8 12Z" fill="#7D7D7D" />
                                                                        </svg> Appoinment</a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- chatbox -->


                                </div>
                            </div>

                        </section>
                        <!-- char-area -->

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ route('secure.js', ['filename' => 'message']) }}"></script>
<script>
    const currentUserId = '{{ auth()->user()->id }}'; 
</script>
@endpush