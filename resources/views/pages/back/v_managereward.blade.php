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
                        @component('components.datatable', ['tableId' => 'dynamic-managereward-table'])
                        @endcomponent
                    </div>

                </div>


                @component('components.modal', [
                'modalId' => 'rewardModal',
                'size' => 'modal-lg',
                'title' => 'Add or Edit Menu',
                'confirmText' => 'Save',
                'confirmButtonId' => 'saveReward'
                ])

                <x-form formId="reward-form" actionUrl="" method="">

                    <div class="row">

                        <div class="col-6">
                            <x-input type="text" name="reward_type" id="reward_type" label="Reward Type:" value="{{ old('reward_type') }}" placeholder="Enter reward type" />
                        </div>

                        <div class="col-6">
                            <x-input type="number" name="reward_amount" id="reward_amount" label="Reward Amount:" value="{{ old('reward_amount') }}" placeholder="Enter reward amount" />
                        </div>


                        <div class="col-12">
                            <x-input type="number" name="reward_expiration_value" id="reward_expiration_value" label="Reward Expiration Value:" value="{{ old('reward_expiration_value') }}" />
                            <x-select name="reward_expiration_type" id="reward_expiration_type" label="Reward Expiration Type:" :options="[
                            'days' => 'Days', 
                            'month' => 'Month',
                            ]" :selected="old('reward_expiration_type')" />

                            <x-select name="status" id="status" label="Status" :options="[
                            'Active' => 'Active', 
                            'Inactive' => 'Inactive',
                            ]" :selected="old('status')" />
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
<script src="{{ route('secure.js', ['filename' => 'managereward']) }}"></script>
@endpush