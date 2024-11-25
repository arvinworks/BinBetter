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

                    <!-- card header  -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">List of Resident data.</h4>
                        </div>

                        <div>
                            <button class="btn btn-secondary btn-sm rounded-0" id="add-btn" data-modaltitle="Add">
                                Add <i class="bi bi-plus-square fs-4 ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- table  -->
                    <div class="card-body p-0">
                        @component('components.datatable', ['tableId' => 'dynamic-resident-table'])
                        @endcomponent
                    </div>

                </div>


                @component('components.modal', [
                'modalId' => 'residentModal',
                'size' => 'modal-lg',
                'title' => 'Add or Edit Menu',
                'confirmText' => 'Save',
                'confirmButtonId' => 'saveResident'
                ])

                <x-form formId="resident-form" actionUrl="" method="">

                    <div class="row">

                        <div class="col-6">
                            <x-input type="file" name="profile" id="profile" label="Profile:" />
                            <x-input type="text" name="username" id="username" label="Username:" value="{{ old('username') }}" placeholder="Enter username" />
                            <x-input type="number" name="age" id="age" label="Age:" />
                        </div>

                        <div class="col-6">
                            <x-input type="email" name="email" id="email" label="Email:" value="{{ old('email') }}" placeholder="Enter email" />
                            <x-input type="password" name="password" id="password" label="Password:" placeholder="Enter password" />
                            
                                  <x-select name="gender" id="gender" label="Gender" :options="[
                        'Male' => 'Male', 
                        'Female' => 'Female'
                        ]" :selected="old('gender')" />
                        
                        </div>
                        

                        <div class="col-12">
                            <x-select name="address" id="address" label="Address" :selected="old('address')" placeholder="Select an address" />
                        </div>

                    </div>

                </x-form>

                @endcomponent

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ route('secure.js', ['filename' => 'resident']) }}"></script>
@endpush