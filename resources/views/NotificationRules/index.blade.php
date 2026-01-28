@extends('layouts.app')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-6 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex flex-column">
                    <h2 class="text-white font-weight-bold my-2 mr-5">{{ $title }}</h2>
                </div>
            </div>
        </div>
    </div>
    <!--end::Subheader-->

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="card card-custom gutter-b">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">{{ $title }}
                            <span class="d-block text-muted pt-2 font-size-sm">Manage your Notification Rules</span>
                        </h3>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center">
                            @can('Create Notification Rules')
                            <a href='{{ url("$route/create") }}' class="btn btn-primary font-weight-bolder">
                                <span class="svg-icon svg-icon-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                         width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <circle fill="#000000" cx="9" cy="15" r="6"/>
                                            <path
                                                d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z"
                                                fill="#000000" opacity="0.3"/>
                                        </g>
                                    </svg>
                                </span>New Rule
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center" id="notificationRulesTable">
                            <thead>
                                <tr class="text-uppercase text-center">
                                    <th style="min-width: 50px">ID</th>
                                    <th style="min-width: 200px">Name</th>
                                    <th style="min-width: 120px">Event</th>
                                    <th style="min-width: 100px">Recipients</th>
                                    <th style="min-width: 80px">Status</th>
                                    <th style="min-width: 130px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @include("$view.loop")
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Entry-->
</div>

@endsection

@push('script')
<script>
$(document).ready(function() {
    // Initialize DataTable with search and pagination (no page reload)
    $('#notificationRulesTable').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthChange: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[0, 'desc']], // Order by ID descending
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ rules",
            paginate: {
                first: '<i class="ki ki-double-arrow-back"></i>',
                last: '<i class="ki ki-double-arrow-next"></i>',
                previous: '<i class="ki ki-arrow-back"></i>',
                next: '<i class="ki ki-arrow-next"></i>'
            }
        },
        columnDefs: [
            { orderable: false, targets: -1 } // Disable ordering on actions column
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        initComplete: function() {
            // Style the search input
            $('.dataTables_filter input').addClass('form-control form-control-sm');
            $('.dataTables_filter input').css({
                'width': '250px',
                'margin-left': '10px'
            });
            // Style the length dropdown
            $('.dataTables_length select').addClass('form-control form-control-sm');
        }
    });
});
</script>
@endpush
