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
                            <h4 class="mb-0">List of Service data.</h4>
                        </div>

                        <div>
                            <button class="btn btn-secondary btn-sm rounded-0" id="add-btn" data-modaltitle="Add">
                                Add <i class="bi bi-plus-square fs-4 ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- table  -->
                    <div class="card-body p-0">
                        @component('components.datatable', ['tableId' => 'dynamic-service-table'])
                        @endcomponent
                    </div>

                </div>

                @component('components.modal', [
                'modalId' => 'serviceModal',
                'size' => 'modal-lg',
                'title' => 'Add or Edit Menu',
                'confirmText' => 'Save',
                'confirmButtonId' => 'saveService'
                ])

                <x-form formId="service-form" actionUrl="" method="">

                    <x-input type="text" name="service" id="service" label="Service Type:" value="{{ old('service') }}" placeholder="Enter service type" />
                    <x-input type="number" name="points" id="points" label="Service Points:" value="{{ old('points') }}" placeholder="Enter service points" />
                    <x-textarea name="description" id="description" label="Description" placeholder="Enter service description" :value="old('description')" rows="5" />

                </x-form>

                @endcomponent



                @component('components.modal', [
                'modalId' => 'descriptionModal',
                'size' => 'modal-lg',
                'title' => 'Service Description',
                'confirmText' => '',
                'confirmButtonId' => ''
                ])

                @endcomponent


            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ route('secure.js', ['filename' => 'service']) }}"></script>
@endpush