@extends('layouts.back.app')

@section('content')
<div class="app-content-area pt-0">

    @if(in_array(Auth::user()->role, ['NGO','Superadmin']))
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

                    <!-- card header  -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">List of NGO Events data.</h4>
                        </div>
                        @if(Auth::user()->role === 'NGO')
                        <div>
                            <button class="btn btn-secondary btn-sm rounded-0" id="add-btn" data-modaltitle="Add">
                                Add <i class="bi bi-plus-square fs-4 ml-2"></i>
                            </button>
                        </div>
                        @endif
                    </div>

                    <!-- table  -->
                    <div class="card-body p-0">
                        @component('components.datatable', ['tableId' => 'dynamic-event-table'])
                        @endcomponent
                    </div>

                </div>

                @component('components.modal', [
                'modalId' => 'eventModal',
                'size' => 'modal-lg',
                'title' => 'Add or Edit Menu',
                'confirmText' => 'Save',
                'confirmButtonId' => 'saveEvent'
                ])

                <x-form formId="event-form" actionUrl="" method="">

                    <div class="row">

                        <div class="col-6">
                            <x-input type="text" name="title" id="title" label="Title:" value="{{ old('title') }}" placeholder="Enter event title" />
                            <x-input type="date" name="start_date" id="start_date" label="Start Date:" value="{{ old('start_date') }}" placeholder="Enter event start date" />

                        </div>

                        <div class="col-6">
                            <x-input type="text" name="location" id="location" label="Location:" value="{{ old('location') }}" placeholder="Enter event location" />
                            <x-input type="date" name="end_date" id="end_date" label="End Date:" value="{{ old('end_date') }}" placeholder="Enter event end date" />

                        </div>

                        <div class="col-12">

                            <x-input type="time" name="time" id="time" label="Time:" value="{{ old('time') }}" placeholder="Enter event time" />


                            <x-input type="number" name="capacity" id="capacity" label="Capacity:" value="{{ old('capacity') }}" placeholder="Enter event capacity" />

                            <x-textarea name="description" id="description" label="Description" placeholder="Enter event description" :value="old('description')" rows="5" />

                            <x-select name="status" id="status" label="Event Status" :options="[
                                'ongoing' => 'Ongoing',
                                'completed' => 'Compeleted'
                                ]" :selected="old('status')" />

                        </div>

                    </div>

                </x-form>

                @endcomponent




            </div>
        </div>

    </div>
    @elseif(in_array(Auth::user()->role, ['NGO']))
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


    @else
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

    @endif

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