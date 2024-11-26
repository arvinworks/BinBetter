@extends('layouts.back.app')

@section('content')
<div class="app-content-area pt-0 ">
    <div class="bg-light pt-12 pb-21 "></div>
    <div class="container-fluid mt-n22 ">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <!-- Page header -->
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div class="mb-2 mb-lg-0">
                        <h3 class="mb-0  text-dark">{{ $page }}</h3>
                    </div>
                    @if(in_array(Auth::user()->role, ['Resident', 'NGO']))
                    <div>
                        <button class="btn btn-secondary btn-sm rounded-0" id="add-btn" data-modaltitle="Add">
                            Add <i class="bi bi-plus-square fs-4 ml-2"></i>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- row  -->
        <div class="row ">
            <div class="col-xl-12 col-12 mb-5">

                <nav>
                    <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-garbage-tab" data-bs-toggle="tab" data-bs-target="#nav-garbage" type="button" role="tab" aria-controls="nav-garbage" aria-selected="true">Garbage</button>
                        <button class="nav-link text-dark" id="nav-recycled-tab" data-bs-toggle="tab" data-bs-target="#nav-recycled" type="button" role="tab" aria-controls="nav-recycled" aria-selected="false">Recycled</button>
                    </div>
                </nav>
                <div class="tab-content p-3 border bg-light p-0" id="nav-tabContent">
                    <div class="tab-pane fade active show" id="nav-garbage" role="tabpanel" aria-labelledby="nav-garbage-tab">


                        <!-- card  -->
                        <div class="card">

                            <!-- card header  -->
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0">List of Post Garbage Report data.</h4>
                                </div>
                            </div>

                            <!-- table  -->
                            <div class="card-body p-0">
                                @component('components.datatable', ['tableId' => 'dynamic-postgarbagereport-table'])
                                @endcomponent
                            </div>

                        </div>

                    </div>
                    <div class="tab-pane fade" id="nav-recycled" role="tabpanel" aria-labelledby="nav-recycled-tab">

                        <!-- card  -->
                        <div class="card">

                            <!-- card header  -->
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0">List of Post Recycled Report data.</h4>
                                </div>
                            </div>

                            <!-- table  -->
                            <div class="card-body p-0">
                                @component('components.datatable', ['tableId' => 'dynamic-postrecycledreport-table'])
                                @endcomponent
                            </div>

                        </div>

                    </div>

                </div>



                @component('components.modal', [
                'modalId' => 'postreportModal',
                'size' => 'modal-lg',
                'title' => 'Add or Edit Menu',
                'confirmText' => 'Save',
                'confirmButtonId' => 'savePostReport'
                ])

                <x-form formId="postreport-form" actionUrl="" method="">

                    <x-select name="type" id="type" label="Select Post Type Options" :options="[
                        'Garbage' => 'Report Garbage', 
                        'Recycled' => 'Sell Recycled Item'
                        ]" :selected="old('type')" placeholder="Select an report type" />


                    <div id="garbage_container" class="d-none">
                        <div>
                            <x-input type="file" name="photo[]" id="photo" label="Photo:" multiple />
                            <span class="invalid-feedback d-block" role="alert" id="photo_error"></span>
                        </div>

                        <x-input type="text" name="video_url" id="video_url" label="Video URL:" />
                        <x-select name="address" id="address" label="Address" :selected="old('address')" placeholder="Select an address" />
                        <x-textarea name="description" id="description" label="Description" placeholder="Enter report description" :value="old('description')" rows="5" />

                    </div>

                    <div id="recycled_container" class="d-none">

                        <x-input type="file" name="photo[]" id="re_photo" label="Photo:" multiple />
                        <span class="invalid-feedback d-block" role="alert" id="re_photo_error"></span>
                        <x-textarea name="description" id="re_description" label="Description" placeholder="Enter report description" :value="old('description')" rows="5" />

                    </div>

                </x-form>

                @endcomponent


                @component('components.modal', [
                'modalId' => 'descriptionModal',
                'size' => 'modal-lg',
                'title' => 'Post Report Description',
                'confirmText' => '',
                'confirmButtonId' => ''
                ])

                @endcomponent


            </div>
        </div>

    </div>
</div>


<script>
    document.getElementById('photo').addEventListener('change', function(event) {
        const allowedExtensions = ['image/jpeg', 'image/png']; // Allowed MIME types (JPG, PNG)
        const files = event.target.files;
        let valid = true;
        let errorMessage = '';

        for (let i = 0; i < files.length; i++) {
            if (!allowedExtensions.includes(files[i].type)) {
                valid = false;
                errorMessage = 'Only PNG and JPG files are allowed.';
                break;
            }
        }

        if (!valid) {
            document.getElementById('photo_error').textContent = errorMessage;
            event.target.value = ''; // Clear the selected file(s)
        } else {
            document.getElementById('photo_error').textContent = ''; // Clear error message if valid
        }
    });

    document.getElementById('re_photo').addEventListener('change', function(event) {
        const allowedExtensions = ['image/jpeg', 'image/png']; // Allowed MIME types (JPG, PNG)
        const files = event.target.files;
        let valid = true;
        let errorMessage = '';

        for (let i = 0; i < files.length; i++) {
            if (!allowedExtensions.includes(files[i].type)) {
                valid = false;
                errorMessage = 'Only PNG and JPG files are allowed.';
                break;
            }
        }

        if (!valid) {
            document.getElementById('re_photo_error').textContent = errorMessage;
            event.target.value = ''; // Clear the selected file(s)
        } else {
            document.getElementById('re_photo_error').textContent = ''; // Clear error message if valid
        }
    });
</script>
@endsection

@push('scripts')
<script src="{{ route('secure.js', ['filename' => 'postreport']) }}"></script>
@endpush