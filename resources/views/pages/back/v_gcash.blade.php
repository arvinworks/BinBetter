@extends('layouts.back.app')

@section('content')
<div class="app-content-area">
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <!-- Page header -->
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div class="mb-2 mb-lg-0">
                        <h3 class="mb-0">{{ $page }}</h3>
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
                            <h4 class="mb-0">List of gcash data.</h4>
                        </div>
                        <div>
                            <button class="btn btn-primary-soft btn-sm rounded-0" id="add-btn" data-modaltitle="Add">
                                Add <i class="bi bi-plus-square fs-4 ml-2"></i>
                            </button>
                        </div>                      
                    </div>
                    <!-- table  -->
                    <div class="card-body">
                        @component('components.datatable', ['tableId' => 'dynamic-gcash-table'])
                        @endcomponent
                    </div>

                </div>
            </div>
        </div>


        @component('components.modal', [
        'modalId' => 'gcashModal',
        'title' => 'Add or Edit',
        'confirmCloseHide' => 'No',
        'confirmText' => 'Save',
        'confirmButtonId' => 'saveGcash'
        ])
        <x-form formId="gcash-form" actionUrl="" method="">

            <x-input type="number" name="gcash_number" id="gcash_number" label="Gcash Number" value="{{ old('gcash_number') }}" placeholder="Enter gcash number" />

            <x-input type="file" name="gcash_qr" id="gcash_qr" label="Gcash QR:" />

        </x-form>
        @endcomponent


        
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ route('secure.js', ['filename' => 'gcash']) }}"></script>
@endpush