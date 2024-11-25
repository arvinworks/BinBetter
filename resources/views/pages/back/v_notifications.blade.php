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
                            <h4 class="mb-0">List of messages data.</h4>
                        </div>
                    </div>

                    <!-- table  -->
                    <div class="card-body p-0">
                        @component('components.datatable', ['tableId' => 'dynamic-notification-table'])
                        @endcomponent
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let tableId = 'dynamic-notification-table';
    
        let headers = ['Sender', 'Message'];
        headers.forEach(header => {
            $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
        });

        // Initialize DataTable
        var notificationDataTable = $(`#${tableId}`).DataTable({
            ajax: {
                url: '/view-notification-api',
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'sender',
                    class: 'px-5'
                },
                {
                    data: 'message',
                    class: 'px-5'
                },
            
            ],
            autoWidth: false,
            responsive: {
                breakpoints: [{
                        name: 'desktop',
                        width: Infinity
                    },
                    {
                        name: 'tablet',
                        width: 1024
                    },
                    {
                        name: 'phone',
                        width: 768
                    }
                ]
            },
            paging: true,
            searching: true,
            ordering: false,
            info: true,
            pageLength: 10,
            dom: '<lf<t>ip>',
            language: {
                search: 'Filter',
                paginate: {
                    first: '<i class="bi bi-chevron-double-left"></i>',
                    previous: '<i class="bi bi-chevron-left"></i>',
                    next: '<i class="bi bi-chevron-right"></i>',
                    last: '<i class="bi bi-chevron-double-right"></i>'
                }
            },
            fixedHeader: {
                header: true,
            },
            scrollCollapse: true,
            scrollX: true,
            scrollY: 600,
        });

    });
</script>
@endpush