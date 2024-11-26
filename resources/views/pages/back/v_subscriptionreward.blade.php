@extends('layouts.back.app')

@section('content')
<div class="app-content-area pt-0">
    <div class="container-fluid mt-5">
        <div class="row align-items-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                <h1 class="display-4 fw-bold ls-sm">{{ $page }}</h1>
                <small>Note: <i>You can claim your reward in 24 hours.</i></small>
            </div>
        </div>

        <div class="row mt-5">
            @php
            $dayCounter = 1;
            $today = \Carbon\Carbon::today()->format('Y-m-d');
            @endphp
            @forelse($claimableDates as $claimableDate)
            <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-5">
                <!-- card -->
                <div class="card h-100">
                    <!-- Show overlay if already claimed -->
                    @if($claimableDate['claimed'])
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-warning bg-opacity-75 d-flex align-items-center justify-content-center">
                        <h3 class="text-white fw-bold">Already Claimed</h3>
                    </div>
                    @endif
                    <!-- card body -->
                    <div class="card-body">
                        <h2 class="fw-bold">Day {{ $dayCounter++ }}</h2>
                    </div>
                    <!-- card footer -->
                    <div class="card-footer p-0">
                        <div class="d-flex justify-content-between">
                            <div class="w-50 py-3 px-4">
                                <h6 class="mb-0 text-muted">Date:</h6>
                                <p class="text-dark fs-6 mb-0">
                                    {{ \Carbon\Carbon::parse($claimableDate['date'])->format('F j, Y') }}
                                </p>
                            </div>
                            <div class="border-start w-50 p-0">

                                <button type="button" data-date="{{ $claimableDate['date'] }}" data-expId="{{ $claimableDate['expiration_id'] }}"
                                    class="btn btn-primary w-100 h-100 fw-bold rounded-0 fs-4 claim-btn {{ $claimableDate['claimed'] ? 'd-none' : ''}}"
                                    @if($today !==\Carbon\Carbon::parse($claimableDate['date'])->format('Y-m-d') || $claimableDate['claimed'])
                                    disabled
                                    @endif>
                                    Claim
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="fs-4">No rewards available for claiming.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.claim-btn', function() {
        var button = $(this);
        var date = button.data('date');
        var expirationId = button.data('expid');

        $.ajax({
            url: "{{ route('subscription.claimreward') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                date_claim: date,
                subs_expiry_id: expirationId
            },
            beforeSend: function() {
                button.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    toast('success', response.message)

                    setTimeout(function() {
                        location.reload();
                    }, 3000)

                    button.text('Claimed').prop('disabled', true);
                } else {
                    toast('error', response.message)
                    button.prop('disabled', false);
                }
            },
            error: function(xhr) {
                toast('error', 'An error occurred. Please try again.');
                button.prop('disabled', false);
            }
        });
    });
</script>
@endpush