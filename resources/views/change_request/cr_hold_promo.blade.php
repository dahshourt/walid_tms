@extends('layouts.app')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!-- 🔹 Page Header -->
    <div class="subheader py-6 py-lg-10 bg-gradient-primary text-white shadow-sm" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center">
                <div>
                    <h2 class="font-weight-bold mb-0">{{ $title }}</h2>
                    <p class="mb-0 opacity-75">Manage and view all held change requests efficiently</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔹 Page Content -->
    <div class="d-flex flex-column-fluid">
        <div class="container">
            @can('make hold cr')
            <!-- 🔸 Shifting Form Card -->
            <div class="card card-custom shadow-sm mb-8 border-0">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 text-primary">
                        <i class="fas fa-random mr-2 text-primary"></i> Hold {{ $form_title }}
                    </h3>
                </div>
                <form class="form p-5" action='{{ url("/change-requests/hold") }}' method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    @include("$view.promoform")

                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-success px-5">
                            <i class="fas fa-check-circle mr-2"></i> Submit
                        </button>
                    </div>
                </form>
            </div>
            @endcan
            @can('show hold cr')
            <!-- 🔸 Data Table Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-primary mb-0">
                        <i class="fas fa-list-alt mr-2 text-primary"></i> {{ $title }}
                    </h3>
                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @php
                        $roles_name = auth()->user()->roles->pluck('name');
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered" id="example2">
                            <thead class="thead-light">
                                <tr class="text-center text-uppercase">
                                    <th style="width: 70px;">#ID</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                   
                                    
                                    @can('edit hold cr')
													<th>Actions</th>
													@endcan
                                </tr>
                            </thead>
                            <tbody>
                                @include("$view.loop3")
                            </tbody>
                        </table>
                    </div>
                    <div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="descriptionModalLabel">Full Description</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" style="white-space: pre-wrap;"></div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endcan
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(function() {
        $('#example2').DataTable({
            paging: false,
            lengthChange: false,
            searching: false,
            ordering: true,
            info: false,
            autoWidth: false,
            responsive: true,
            scrollX: true,
            order: [[0, 'desc']],
            columnDefs: [{ targets: [0, -1], className: 'text-center' }]
        });

        $(document).on('click', '.description-preview', function (event) {
            event.preventDefault();
            var fullDescription = $(this).data('description') || '';
            $('#descriptionModal .modal-body').text(fullDescription);
            $('#descriptionModal').modal('show');
        });
    });
</script>
@endpush
