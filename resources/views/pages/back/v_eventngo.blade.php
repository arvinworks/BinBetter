@extends('layouts.back.app')

@section('content')
<div class="app-content-area pt-0">

      <div class="container-fluid mt-5">

        <div class="row align-items-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-12">

                <h1 class="display-4 fw-bold ls-sm">BinBetter Events</h1>
                @forelse($events as $event)
                <div class="card mb-5 mt-5">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center">
                            <div class="d-flex col-lg-4 col-12 mb-4 mb-lg-0">
                                <!-- icon -->
                                <div>
                                    <i class="bi bi-calendar h2 text-muted"></i>
                                </div>
                                <div class="ms-3">
                                    <!-- heading -->
                                    <a href="#!">
                                        <h5 class="mb-1">{{ $event->title }} {{ \Carbon\Carbon::parse($event->time)->format('h:i A')  }}</h5>
                                    </a>
                                    <p class="mb-0">
                                        Uploaded by NGO on {{ \Carbon\Carbon::parse($event->created_at)->format('d M, Y h:i a') }}
                                    </p>



                                    <div style="overflow:hidden; resize:none; max-width:100%; width:300px; height:200px;" class="mt-2">
                                        <div id="my-map-display" style="height:100%; width:100%; max-width:100%;">
                                            <iframe
                                                style="height:100%; width:100%; border:0;"
                                                frameborder="0"
                                                src="https://www.google.com/maps/embed/v1/place?q={{ urlencode($event->location) }},+Philippines&key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8"
                                                allowfullscreen>
                                            </iframe>
                                        </div>
                                        <style>
                                            #my-map-display img {
                                                max-width: none !important;
                                                background: none !important;
                                                font-size: inherit;
                                                font-weight: inherit;
                                            }
                                        </style>
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-8 p-0" style="font-size:12px;">
                                <div class="row">
                                    <div class="col-2 d-flex flex-column">
                                        <span>{{ $event->location }}</span>
                                        <small><b>Event Location</b></small>
                                    </div>
                                    <div class="col-2 d-flex flex-column">
                                        <span>{{ $event->capacity }}</span>
                                        <small><b>Event Capacity</b></small>
                                    </div>
                                    <div class="col-2 d-flex flex-column">
                                        <span>{{ ucfirst($event->status) }}</span>
                                        <small><b>Event Status</b></small>
                                    </div>
                                    <div class="col-2  d-flex flex-column">
                                        <span>
                                            <small><b>From:</b></small>
                                            {{ substr(\Carbon\Carbon::parse($event->start_date)->format('F'), 0, 4) }}.
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('j') }}
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('Y') }}
                                        </span>
                                        <span>
                                            <small><b>To:</b></small>
                                            {{ substr(\Carbon\Carbon::parse($event->end_date)->format('F'), 0, 4) }}.
                                            {{ \Carbon\Carbon::parse($event->end_date)->format('j') }}
                                            {{ \Carbon\Carbon::parse($event->end_date)->format('Y') }}
                                        </span>

                                    </div>
                                    <div class="col-2">
                                        <!-- Check if user has joined the event -->
                                        @if($event->joinEvents->isNotEmpty())
                                        @foreach($event->joinEvents as $joinevent)
                                        @if($joinevent->status === 'Approved')
                                        <span class="badge bg-success">You joined an event.</span>
                                        @else
                                        <span class="badge bg-danger">{{ $joinevent->status }}</span>
                                        @endif
                                        @endforeach
                                        @endif
                                    </div>
                                    <div class="col-2">
                                        <!-- Check if user has joined the event and disable the button if joined -->
                                        @if($event->joinEvents->isNotEmpty())
                                        @foreach($event->joinEvents as $joinevent)
                                        @if($joinevent->status === 'Approved')
                                        <!-- Disable the button if user has already joined with status 'Approved' -->
                                        <button class="btn btn-outline-primary text-primary btn-sm" disabled style="cursor:not-allowed">Joined</button>
                                        @else
                                        <!-- Allow to join if the status is not 'Approved' -->
                                        <button class="btn btn-outline-primary text-primary btn-sm join-event" data-eventid="{{ $event->id }}">Join</button>
                                        @endif
                                        @endforeach
                                        @else
                                        <!-- Allow to join if the user hasn't joined the event yet -->
                                        <button class="btn btn-outline-primary text-primary btn-sm join-event" data-eventid="{{ $event->id }}">Join</button>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-12 text-justify">
                                        {!! ucfirst($event->description) !!}
                                    </div>
                                </div>
                            </div>




                        </div>
                    </div>
                </div>
                @empty
                <h3>No data found.</h3>
                @endforelse


            </div>
        </div>
    </div>


   

    @component('components.modal', [
    'modalId' => 'descriptionModal',
    'size' => 'modal-lg',
    'title' => 'Event Description',
    'confirmText' => '',
    'confirmButtonId' => ''
    ])

    @endcomponent

</div>
@endsection

@push('scripts')
<script>
    let roleAdmin = "{{ Auth::user()->role === 'Superadmin' ? 'true' : 'false' }}" === 'true';
</script>
<script src="{{ route('secure.js', ['filename' => 'event']) }}"></script>
@endpush