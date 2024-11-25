@extends('layouts.back.app')

@section('content')
<div class="app-content-area pt-0 ">


    @if(in_array(Auth::user()->role, ['LGU','Superadmin']))

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
                            <h4 class="mb-0">List of Garbage Collection Schedule data.</h4>
                        </div>
                        @if(Auth::user()->role === 'LGU')
                        <div>
                            <button class="btn btn-primary-soft btn-sm rounded-0" id="add-btn" data-modaltitle="Add">
                                Add <i class="bi bi-plus-square fs-4 ml-2"></i>
                            </button>
                        </div>
                        @endif
                    </div>

                    <!-- table  -->
                    <div class="card-body p-0">
                        @component('components.datatable', ['tableId' => 'dynamic-garbageschedules-table'])
                        @endcomponent
                    </div>

                </div>

            </div>
        </div>

        @component('components.modal', [
        'modalId' => 'garbageModal',
        'size' => 'modal-lg',
        'title' => 'Add or Edit Menu',
        'confirmText' => 'Save',
        'confirmButtonId' => 'saveGarbage'
        ])

        <x-form formId="garbage-form" actionUrl="" method="">

            <div class="row">

                <div class="col-6">
                    <x-input type="text" name="street" id="street" label="Street:" value="{{ old('street') }}" placeholder="Enter street or purok" />

                    <x-select name="address" id="address" label="Address" :selected="old('address')" placeholder="Select an address" />

                    <x-input type="time" name="timefrom" id="timefrom" label="Time From:" value="{{ old('timefrom') }}" placeholder="Enter time from" />

                    <x-input type="time" name="timeto" id="timeto" label="Time To:" value="{{ old('timeto') }}" placeholder="Enter time to" />
                </div>

                <div class="col-6 p-2">

                    <h3><strong>Choose a Day</strong></h3>

                    <x-input type="checkbox" name="day" id="monday" label="Monday" value="Monday" />
                    <x-input type="checkbox" name="day" id="tuesday" label="Tuesday" value="Tuesday" />
                    <x-input type="checkbox" name="day" id="wednesday" label="Wednesday" value="Wednesday" />
                    <x-input type="checkbox" name="day" id="thursday" label="Thursday" value="Thursday" />
                    <x-input type="checkbox" name="day" id="friday" label="Friday" value="Friday" />
                </div>

            </div>

        </x-form>

        @endcomponent

    </div>

    @else

    <div class="container-fluid mt-5">
        <div class="row align-items-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-12">

                <h1 class="display-4 fw-bold ls-sm">Garbage Schedule</h1>

                @forelse($schedules as $schedule)
                <div class="card mb-5 mt-5">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center">
                            <div class="d-flex col-lg-4 col-12 mb-4 mb-lg-0">
                                <!-- icon -->
                                <div>
                                    <i class="bi bi-map h2 text-muted"></i>
                                </div>
                                <div class="ms-3">
                                    <!-- heading -->
                                    <a href="#!">
                                        <h5 class="mb-1">{{ $schedule->street }}, {{ $schedule->barangay }}</h5>
                                    </a>
                                    <p class="mb-0">
                                        Uploaded by LGU on {{ \Carbon\Carbon::parse($schedule->created_at)->format('d M, Y h:i a') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 d-flex flex-column">
                                <span>{{ $schedule->time }}</span>
                                <small><b>Schedule</b></small>
                            </div>
                            <div class="col-4 d-flex flex-column">
                                <span>{{ $schedule->collection_day }}</span>
                                <small><b>Collection Day</b></small>
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


</div>
@endsection

@push('scripts')
<script>
    let roleAdmin = "{{ Auth::user()->role === 'Superadmin' ? 'true' : 'false' }}" === 'true';
</script>
<script src="{{ route('secure.js', ['filename' => 'garbageschedules']) }}"></script>
@endpush