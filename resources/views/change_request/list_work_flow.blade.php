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
						<h2 class="text-white font-weight-bold my-2 mr-5">Target System</h2>
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
				<div class="row">
					<div class="col-md-12">
						<!--begin::Card-->
						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<h3 class="card-title">Choose Target System</h3>
							</div>
							<!--begin::Form-->
							<form class="form" action="{{ url('change_request/create') }}" method="get">
								<div class="card-body">
									@if($errors->any())
										<div class="m-alert m-alert--icon alert alert-danger" role="alert" id="m_form_1_msg">
											<div class="m-alert__icon">
												<i class="la la-warning"></i>
											</div>
											<div class="m-alert__text">
												There are some errors
											</div>
											<div class="m-alert__close">
												<button type="button" class="close" data-close="alert" aria-label="Close">
												</button>
											</div>
										</div>
									@endif

									<div class="form-group">
										<label for="target_system_id">Target System</label>
										<select name="target_system_id" id="target_system_id"
											class="form-control form-control-lg select2">
											<option value="">Select</option>
											@foreach($target_systems as $item)
												<option value="{{$item->id}}">{{$item->name}}</option>
											@endforeach
										</select>
									</div>

									<div class="card-footer">
										<button type="submit" class="btn btn-success mr-2">Next</button>
									</div>
								</div>
							</form>
							<!--end::Form-->
						</div>
						<!--end::Card-->
					</div>
				</div>
			</div>
			<!--end::Container-->
		</div>
		<!--end::Entry-->
	</div>
	<!--end::Content-->

@endsection

@push('script')
	<script>
		$(document).ready(function () {
			$('#target_system_id').select2({
				placeholder: "Select Target System",
				allowClear: true
			});
		});
	</script>
@endpush