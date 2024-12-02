@extends('layouts.back.app')

@section('content')
<div class="app-content-area">

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <!-- Page header -->
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div class="mb-2 mb-lg-0">
                        <h3 class="mb-0">
                            {{ $page }}
                        </h3>
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
                            <h4 class="mb-0">
                                List of payment data.
                            </h4>
                        </div>
                        <div>

                            <button class="btn btn-primary-soft btn-sm rounded-0" id="add-btn" data-modaltitle="Submit Payment Proof">
                                Pay <i class="bi bi-plus-square fs-4 ml-2"></i>
                            </button>

                        </div>
                    </div>
                    <!-- table  -->
                    <div class="card-body">
                        @component('components.datatable', ['tableId' => 'dynamic-payment-table'])
                        @endcomponent
                    </div>

                </div>
            </div>
        </div>

        @component('components.modal', [
        'modalId' => 'paymentModal',
        'title' => 'Gcash Payment',
        'confirmCloseHide' => 'No',
        'confirmText' => 'Save',
        'confirmButtonId' => 'savePayment'
        ])
        <x-form formId="payment-form" actionUrl="" method="">



            <input type="hidden" id="gcash_id" name="gcash_id" value="{{ $gcash->id ?? 0 }}" />

            <div class="d-flex flex-column mb-2 mt-2  text-center" id="gcashDetails">
                <div id="gcash_number_display" class="mb-1 fw-bold">{{ $gcash->gcash_number ?? 0 }}</div>
                <div id="gcash_qr_display"><img src="{{ asset($gcash->gcash_qr ?? '' ) }}" width="200"></div>
            </div>


            <x-input type="number" name="amount" id="amount" label="Amount" value="{{ old('amount') }}" placeholder="Enter payment amount" />

            <x-input type="file" name="proof_payment" id="proof_payment" label="Upload Proof payment:" />



        </x-form>
        @endcomponent



        @component('components.modal', [
        'modalId' => 'paymentImageModal',
        'title' => 'Proof Payment',
        'confirmCloseHide' => 'No',
        'confirmText' => '',
        'confirmButtonId' => ''
        ])
        <div id="showImage"> </div>
        @endcomponent



    </div>
</div>
@endsection


@push('scripts')
<script src="{{ route('secure.js', ['filename' => 'payments']) }}"></script>

<script>
    const isAdmin = @json($isAdmin);
</script>

@endpush