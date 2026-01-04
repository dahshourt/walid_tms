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
											<h3 class="card-label">{{ $title }}
										</div>
										
									</div>
									@php
									$roles_name = auth()->user()->roles->pluck('name');
									@endphp
									<div class="card-body">
										<!--begin: Datatable-->
										<table class="table table-separate table-head-custom table-checkable" id="example2">
											<thead>
												<tr>
												<th>ID#</th>
 													<th>Title</th>
													<th>Description</th>
													<th>Status</th>
												
												
													@can('Edit cr pending cap')
													<th>Actions</th>
													@endcan
												</tr>
											</thead>
											<tbody>
											@include("$view.cr_pending_cap_loop")
											</tbody>
										</table>
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
										<!--end: Datatable-->
									</div>
								</div>
								{{ $collection->links() }}
								<!--end::Card-->
							</div>
							<!--end::Container-->
						</div>
						<!--end::Entry-->
					</div>
					<!--end::Content-->
					

@endsection



@push('script')
    
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": false,
                "lengthChange": false,
                "autoWidth": true,
                "ordering": false,
                "buttons": ["excel"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": true,
                "responsive": false,
				"scrollX": true,
				order: [[ 0, 'desc' ]]
                
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