@extends('layouts.back.app')

@section('content')
<div class="app-content-area pt-0">

    <div class="bg-primary pt-12 pb-21"></div>
    <div class="container-fluid mt-n22">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <!-- Page header -->
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div class="mb-2 mb-lg-0">
                        <h3 class="mb-0 text-white">{{ $page }}</h3>
                    </div>
                </div>
            </div>
        </div>

        @if(Auth::user()->role === 'LGU')
        <!-- row for LGU -->
        <div class="row mb-5">
            <div class="col-xl-12 col-12 mb-5">
                <!-- card for Garbage Tips -->
                <div class="card">

                    <!-- card header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">List of Garbage Tips data.</h4>
                        </div>

                        <div>
                            <button class="btn btn-secondary btn-sm rounded-0" id="add-btn" data-modaltitle="Add">
                                Add <i class="bi bi-plus-square fs-4 ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- table -->
                    <div class="card-body p-0">
                        @component('components.datatable', ['tableId' => 'dynamic-garbagetips-table'])
                        @endcomponent
                    </div>

                </div>

                @component('components.modal', [
                'modalId' => 'garbageTipModal',
                'size' => 'modal-lg',
                'title' => 'Add or Edit Menu',
                'confirmText' => 'Save',
                'confirmButtonId' => 'saveGarbageTip'
                ])
                <x-form formId="garbagetip-form" actionUrl="" method="">
                    <x-input type="text" name="title" id="title" label="Title:" value="{{ old('title') }}" placeholder="Enter Title" />
                    <div class="existing-photos mb-2">
                        <label>Existing Photos:</label>
                        <div class="photos-container"></div>
                    </div>
                    <x-input type="file" name="photos[]" id="photos" label="Garbage Tip Photos:" multiple />
                    <x-input type="text" name="video" id="video" label="Video URL:" />
                    <x-textarea name="description" id="description" label="Description" placeholder="Enter description" :value="old('description')" rows="5" />
                </x-form>
                @endcomponent

                @component('components.modal', [
                'modalId' => 'descriptionModal',
                'size' => 'modal-lg',
                'title' => 'Garbage Tip Description',
                'confirmText' => '',
                'confirmButtonId' => ''
                ])
                @endcomponent

            </div>
        </div>

        <!-- List of Tutorials for LGU -->
        <div class="row mb-5">
            <div class="col-xxl-12 col-lg-12 col-12 ">
                <div class="mt-6 mt-lg-0">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">List of Tutorials</h4>
                        </div>

                        <div class="card-body garbage-tip-list">
                            <!-- Tutorials content goes here -->
                        </div>

                    </div>
                </div>
            </div>
        </div>

        @else
        <!-- For non-LGU roles -->
        <div class="row mb-5">
            <div class="col-xxl-12 col-lg-12 col-12 ">
                <div class="mt-6 mt-lg-0">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">List of Tutorials</h4>
                        </div>

                        <div class="card-body garbage-tip-list">
                            <!-- Tutorials content for non-LGU users goes here -->
                        </div>

                    </div>
                </div>
            </div>
        </div>

        @component('components.modal', [
        'modalId' => 'reportContentModal',
        'size' => 'modal-lg',
        'title' => 'Add or Edit Menu',
        'confirmText' => 'Save',
        'confirmButtonId' => 'saveReportContent'
        ])
        <x-form formId="reportcontent-form" actionUrl="" method="">
            <x-input type="text" name="report_type" id="report_type" label="Report Type:" value="{{ old('report_type') }}" placeholder="Enter Report Type ( ex. Violent, Robbery, Sexual etc. )" />
            <x-textarea name="re_description" id="re_description" label="Description" placeholder="Enter description" :value="old('description')" rows="5" />
        </x-form>
        @endcomponent

        @endif

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ route('secure.js', ['filename' => 'garbagetips']) }}"></script>
@endpush