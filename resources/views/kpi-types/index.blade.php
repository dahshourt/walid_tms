@extends('layouts.app')

@section('content')

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Heading-->
                <div class="d-flex flex-column">
                    <!--begin::Title-->
                    <h2 class="text-white font-weight-bold my-2 mr-5">{{ $title }}</h2>
                    <!--end::Title-->
                </div>
                <!--end::Heading-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Card-->
            <div class="card">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">{{ $title }}</h3>
                    </div>
                    <div class="card-toolbar">
                        @can('Create KPI Types')
                        <!--begin::Button-->
                        <a href='{{ route("kpi-types.create") }}' class="btn btn-primary font-weight-bolder">
                            <span class="svg-icon svg-icon-md">
                                <!--begin::Svg Icon-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <circle fill="#000000" cx="9" cy="15" r="6" />
                                        <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>New {{ $form_title }}
                        </a>
                        <!--end::Button-->
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <!--begin: Datatable-->
                    <table class="table table-separate table-head-custom table-checkable" id="kt_datatable2">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @include("$view.loop")
                        </tbody>
                    </table>
                    <!--end: Datatable-->
                </div>
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

@endsection

@push('script')
@can('Edit KPI Types')
<script>
$(document).ready(function() {
    // Handle status toggle
    $('.status-toggle').on('change', function() {
        const checkbox = $(this);
        const isChecked = checkbox.is(':checked');
        const statusText = isChecked ? 'activate' : 'deactivate';
        const confirmText = isChecked ? 'Yes, activate it!' : 'Yes, deactivate it!';
        const itemId = checkbox.data('id');
        const itemName = checkbox.data('name');

        Swal.fire({
            title: 'Are you sure?',
            text: `You want to ${statusText} "${itemName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading alert
                Swal.fire({
                    title: 'Processing...',
                    text: 'Updating status...',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route("kpi-types.update-status") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: itemId
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Updated!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                            checkbox.prop('checked', !isChecked);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to update status',
                            icon: 'error'
                        });
                        checkbox.prop('checked', !isChecked);
                    }
                });
            } else {
                checkbox.prop('checked', !isChecked);
            }
        });
    });
});
</script>
@endcan
@endpush

