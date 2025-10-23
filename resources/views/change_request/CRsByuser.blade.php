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
											<div class="d-flex">
											
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

												<!--begin::Export Button-->
												@if($collection->count() > 0)
													<a href="{{ route('change_request.export_user_created_crs', ['workflow_type' => request('workflow_type', 'In House')]) }}" 
													   class="btn btn-success font-weight-bolder ml-3">
														<span class="svg-icon svg-icon-md">
															<!--begin::Svg Icon | path:assets/media/svg/icons/Files/Download.svg-->
															<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																	<rect x="0" y="0" width="24" height="24"/>
																	<path d="M7,18 L17,18 C18.1045695,18 19,18.8954305 19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 C5,18.8954305 5.8954305,18 7,18 Z M7,20 L17,20 C17.5522847,20 18,20.4477153 18,21 C18,21.5522847 17.5522847,22 17,22 L7,22 C6.44771525,22 6,21.5522847 6,21 C6,20.4477153 6.44771525,20 7,20 Z" fill="#000000" fill-rule="nonzero"/>
																	<path d="M12,2 C12.5522847,2 13,2.44771525 13,3 L13,13.5857864 L15.2928932,11.2928932 C15.6834175,10.9023689 16.3165825,10.9023689 16.7071068,11.2928932 C17.0976311,11.6834175 17.0976311,12.3165825 16.7071068,12.7071068 L12.7071068,16.7071068 C12.3165825,17.0976311 11.6834175,17.0976311 11.2928932,16.7071068 L7.29289322,12.7071068 C6.90236893,12.3165825 6.90236893,11.6834175 7.29289322,11.2928932 C7.68341751,10.9023689 8.31658249,10.9023689 8.70710678,11.2928932 L11,13.5857864 L11,3 C11,2.44771525 11.4477153,2 12,2 Z" fill="#000000"/>
																</g>
															</svg>
															<!--end::Svg Icon-->
														</span>Export Excel
													</a>
												@else
													<span class="btn btn-secondary font-weight-bolder ml-3" style="opacity: 0.6; cursor: not-allowed;" title="No data available to export">
														<span class="svg-icon svg-icon-md">
															<!--begin::Svg Icon | path:assets/media/svg/icons/Files/Download.svg-->
															<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																	<rect x="0" y="0" width="24" height="24"/>
																	<path d="M7,18 L17,18 C18.1045695,18 19,18.8954305 19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 C5,18.8954305 5.8954305,18 7,18 Z M7,20 L17,20 C17.5522847,20 18,20.4477153 18,21 C18,21.5522847 17.5522847,22 17,22 L7,22 C6.44771525,22 6,21.5522847 6,21 C6,20.4477153 6.44771525,20 7,20 Z" fill="#000000" fill-rule="nonzero"/>
																	<path d="M12,2 C12.5522847,2 13,2.44771525 13,3 L13,13.5857864 L15.2928932,11.2928932 C15.6834175,10.9023689 16.3165825,10.9023689 16.7071068,11.2928932 C17.0976311,11.6834175 17.0976311,12.3165825 16.7071068,12.7071068 L12.7071068,16.7071068 C12.3165825,17.0976311 11.6834175,17.0976311 11.2928932,16.7071068 L7.29289322,12.7071068 C6.90236893,12.3165825 6.90236893,11.6834175 7.29289322,11.2928932 C7.68341751,10.9023689 8.31658249,10.9023689 8.70710678,11.2928932 L11,13.5857864 L11,3 C11,2.44771525 11.4477153,2 12,2 Z" fill="#000000"/>
																</g>
															</svg>
															<!--end::Svg Icon-->
														</span>Export Excel
													</span>
												@endif
												<!--end::Export Button-->
											</div>
										</div>
									</div>
									@php
									$roles_name = auth()->user()->roles->pluck('name');
									@endphp 
									<div class="card-body">
										<form method="GET" action="{{ url()->current() }}">
											<div class="form-group">
												<label for="workflow_type">Select Type:</label>
												<select name="workflow_type" id="workflow_type" class="form-control" onchange="this.form.submit()">
													
													<option value="In House" {{ request('workflow_type', 'In House') == 'In House' ? 'selected' : '' }}>In House</option>
													<option value="Vendor" {{ request('workflow_type') == 'Vendor' ? 'selected' : '' }}>Vendor</option>
													<option value="Promo" {{ request('workflow_type') == 'Promo' ? 'selected' : '' }}>Promo</option>
													<option value="On Going" {{ request('workflow_type') == 'On Going' ? 'selected' : '' }}>On Going</option>


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
													@if(request('workflow_type', 'In House') == 'In House')
													<th>Design Duration</th>
													<th>Start Design Time</th>
													<th>End Design Time</th>
													<th>Development Duration</th>
													<th>Start Development Time</th>
													<th>End Development Time</th>
													<th>Test Duration</th>
													<th>Start Test Time</th>
													<th>End Test Time</th>
													<th>CR Duration</th>
													<th>Start CR Time</th>
													<th>End CR Time</th>
													@endif
													@if(request('workflow_type') == 'Vendor')
													<th>Release</th>
													<th>Planned Start IOT Date</th>
													<th>Planned End IOT Date</th>
													<th>Planned Start E2E Date</th>
													<th>Planned End E2E Date</th>
													<th>Planned Start UAT Date</th>
													<th>Planned End UAT Date</th>
													<th>Planned Start Smoke Test Date</th>
													<th>Planned End Smoke Test Date</th>
													<th>Go Live Planned Date</th>
                                                    @endif
													@endif
													@canany(['Edit ChangeRequest' , 'Show ChangeRequest'])
													@if(request('workflow_type') == 'Promo')
													<th>created at</th>
													@endif
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