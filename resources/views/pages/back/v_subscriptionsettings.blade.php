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
                            <h4 class="mb-0">List of Subscription data.</h4>
                        </div>

                        <div>
                            <button class="btn btn-secondary btn-sm rounded-0" id="add-btn" data-modaltitle="Add">
                                Add <i class="bi bi-plus-square fs-4 ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- table  -->
                    <div class="card-body p-0">
                        @component('components.datatable', ['tableId' => 'dynamic-subscription-table'])
                        @endcomponent
                    </div>

                </div>

                @component('components.modal', [
                'modalId' => 'subscriptionModal',
                'size' => 'modal-lg',
                'title' => 'Add or Edit Menu',
                'confirmText' => 'Save',
                'confirmButtonId' => 'saveSubscription'
                ])

                <x-form formId="subscription-form" actionUrl="" method="">

                    <x-input type="text" name="subscription_type" id="subscription_type" label="Subscription Type:" value="{{ old('subscription_type') }}" placeholder="Enter subscription type" />
                    <x-textarea name="subscription_description" id="subscription_description" label="Subscription Description" placeholder="Enter subscription description in list" :value="old('subscription_description')" rows="5" />
                    <x-select name="subscription_reward" id="subscription_reward" label="Subscription Reward:" :options="$rewards" :selected="old('subscription_reward')" />
                </x-form>

                @endcomponent

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ route('secure.js', ['filename' => 'subscriptionsettings']) }}"></script>
@endpush