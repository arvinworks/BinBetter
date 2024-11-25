@extends('layouts.back.app')

@section('content')

<div class="app-content-area pt-0">

    @if(Auth::check())
    @if(Auth::user()->role === 'Superadmin')

    <div class="bg-primary pt-12 pb-21 "></div>
    <div class="container-fluid mt-n22 ">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <!-- Page header -->
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div class="mb-2 mb-lg-0">
                        <h3 class="mb-0  text-white">{{ $page }}</h3>
                    </div>

                    @if(route('subscription'))

                    <button type="button" class="btn btn-light">
                        For Approval <span class="badge bg-secondary">{{ $subPendingCount }}</span>
                    </button>

                    @endif
                </div>
            </div>
        </div>

        <!-- row  -->
        <div class="row ">
            <div class="col-xl-12 col-12 mb-5">
                <!-- card  -->
                <div class="card">

                    <!-- card header  -->
                    <div class="card-header">
                        <div>
                            <h4 class="mb-0">List of for approval subscription data.</h4>
                        </div>
                    </div>

                    <!-- table  -->
                    <div class="card-body p-0">
                        @component('components.datatable', ['tableId' => 'dynamic-subscription-table'])
                        @endcomponent
                    </div>

                </div>


            </div>
        </div>

    </div>

    @else
    <div class="container-fluid">
        <div class="py-8">
            <div class="row">
                <div class="offset-xl-2 col-xl-8 col-md-12">
                    <div class="row mb-18">
                        <div class="col-md-12 col-12 mb-16">
                            <h1 class="display-4 fw-bold ls-sm">Find a subscription that's right for you</h1>
                        </div>
                        <div id="subscription-container" class="row">
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table mb-0 text-nowrap table-centered" id="selectedSubscriptionTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Availed Subscription</th>
                                            <th>Daily Reward</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </div>
    @endif
    @endif

</div>
@endsection

@push('scripts')
<script src="{{ route('secure.js', ['filename' => 'subscription']) }}"></script>
@endpush