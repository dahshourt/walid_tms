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
										<h2 class="text-white font-weight-bold my-2 mr-5">List CRs By Users</h2>
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
											<h3 class="card-label">All Crs Created By :  {{$user_name}}
										</div>
										<div class="card-toolbar">

											@can('Create ChangeRequest')
											
											<!--begin::Button-->
											<a href='{{ url("$route/workflow/type") }}' class="btn btn-primary font-weight-bolder">
											<span class="svg-icon svg-icon-md">
												<!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
												<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
													<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
														<rect x="0" y="0" width="24" height="24" />
														<circle fill="#000000" cx="9" cy="15" r="6" />
														<path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
													</g>
												</svg>
												<!--end::Svg Icon-->
											</span>New Record</a>
											<!--end::Button-->
											@endcan
										</div>
									</div>
									@php
									$roles_name = auth()->user()->roles->pluck('name');
									@endphp 
									<div class="card-body">
										<form method="GET" action="{{ url()->current() }}">
											<div class="form-group">
												<label for="workflow_type">Filter by Workflow Type:</label>
												<select name="workflow_type" id="workflow_type" class="form-control" onchange="this.form.submit()">
													<option value="">All</option>
													<option value="Normal" {{ request('workflow_type') == 'Normal' ? 'selected' : '' }}>Normal</option>
													<option value="On Going" {{ request('workflow_type') == 'On Going' ? 'selected' : '' }}>On Going</option>
													<option value="Vendor" {{ request('workflow_type') == 'Vendor' ? 'selected' : '' }}>Vendor</option>
													<option value="Realeas" {{ request('workflow_type') == 'Realeas' ? 'selected' : '' }}>Realeas</option>

												</select>
											</div>
										</form>
										<!--begin: Datatable-->
										<table class="table table-separate table-head-custom table-checkable" id="example2">
											<thead>
												<tr>
												<th>ID#</th>
 													<th>Title</th>
													<th>Status</th>
													@if(!empty($roles_name) && isset($roles_name[0]) && $roles_name[0] != "Viewer")
													<th>Design Duration</th>
													<th>Start Design Time</th>
													<th>End Design Time</th>
													<th>Development Duration</th>
													<th>Start Development Time</th>
													<th>End Development Time</th>
													<th>Test Duration</th>
													<th>Start Test Time</th>
													<th>End Test Time</th>
													@endif
													@canany(['Edit ChangeRequest' , 'Show ChangeRequest'])
													<th>Action</th>
													@endcanany
												</tr>
											</thead>
											<tbody>
											@include("$view.CRsByUsersLoop")
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
        });

    </script>
@endpush