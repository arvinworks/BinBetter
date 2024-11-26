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
                            <h4 class="mb-0">List of Garbage Tips post reports data.</h4>
                        </div>

                    </div>

                    <!-- table  -->
                    <div class="card-body p-0">
                        @component('components.datatable', ['tableId' => 'dynamic-garbagetipsreport-table'])
                        @endcomponent
                    </div>

                </div>

                @component('components.modal', [
                'modalId' => 'descriptionModal',
                'size' => 'modal-lg',
                'title' => 'Garbage Tip Description',
                'confirmText' => '',
                'confirmButtonId' => ''
                ])

                @endcomponent


                @component('components.modal', [
                'modalId' => 'reportModal',
                'size' => 'modal-xl',
                'title' => 'Show Report',
                'confirmText' => '',
                'confirmButtonId' => ''
                ])


                <div class="card border-0 bg-transparent shadow-none">
                    <!-- table  -->
                    <div class="card-body p-0">
                        <table class="table text-nowrap table-hover display">
                            <thead class="table-light text-uppercase">
                                <tr>
                                    <th>User</th>
                                    <th>Report Type</th>
                                    <th>Report Message</th>
                                </tr>
                            </thead>
                            <tbody id="reportContents">
                            </tbody>
                        </table>
                    </div>

                </div>


                @endcomponent



            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let tableId = 'dynamic-garbagetipsreport-table';
        let gtReportId;

        let headers = ['Title', 'Description', 'Report Count', 'Action'];
        headers.forEach(header => {
            $(`#${tableId}-headers`).append(`<th class="px-5">${header}</th>`);
        });

        // Initialize DataTable
        var garbageTipsDataTable = $(`#${tableId}`).DataTable({
            ajax: {
                url: '/report-garbagetip/create',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'title',
                    class: 'px-5'
                },
                {
                    data: 'description',
                    class: 'px-5',
                    render: function(data, type, row) {
                        var plainText = data.replace(/<\/?[^>]+(>|$)/g, "");
                        var words = plainText.split(/\s+/);
                        var truncated = words.slice(0, 4).join(' ');

                        if (words.length > 4) {
                            truncated += '... <a href="#" class="show-full-description" data-tutorialdescription="' + plainText + '">Read more</a>';
                        }

                        return truncated;
                    }
                },
                {
                    data: 'reportcount',
                    class: 'px-5'
                },
                {
                    data: 'actions',
                    render: function(data) {
                        return data;
                    }
                }
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

        $(document).on('click', '.show-full-description', function(e) {
            e.preventDefault();
            var fullDescription = $(this).data('tutorialdescription');
            $('#descriptionModal .modal-body').text(fullDescription);
            $('#descriptionModal').modal('show');
        });


        $(document).on('click', '.show-btn', function() {
            var reports = $(this).data('reports');

            $('#reportContents').empty();

            if (reports.length > 0) {
                reports.forEach(function(report) {
                    $('#reportContents').append(`
                        <tr>
                            <td>${report.username}</td>
                            <td>${report.report_type}</td>
                            <td>${report.report_message}</td>
                        </tr>
                    `);
                });
            } else {
                $('#reportContents').append(`
                    <tr>
                        <td colspan="3" class="text-center">No report found</td>
                    </tr>
                `);
            }

            $('#reportModal').modal('show');
        });

        $(document).on('click', '.approve-btn', function() {
            let ids = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This post will be remove in garbage tips content",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#000',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    removeContent(ids)
                }
            })

        });


        function removeContent(ids) {

            $.ajax({
                type: 'DELETE',
                url: `/report-garbagetip/${ids}`,
                dataType: 'json',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            }).done(function(data) {
                toast(data.type, data.message)
                garbageTipsDataTable.ajax.reload();
            }).fail(function(data) {
                console.log(data)
            });

        }

    });
</script>
@endpush