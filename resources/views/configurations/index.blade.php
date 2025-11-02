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
					<h2 class="text-white font-weight-bold my-2 mr-5"> </h2>
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
						<h3 class="card-label"> </h3>
					</div>
				</div>

				<div class="card-body">
					<!--begin::Form-->
					<form method="POST" action="{{ route('configurations.update') }}">
						@csrf
						<table class="table table-separate table-head-custom table-checkable" id="configTable">
							<thead>
								<tr>
									<th>#</th>
									<th>Configuration Name</th>
									<th>Configuration Value</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($configs as $index => $config)
									<tr>
										<td>{{ $index + 1 }}</td>
										<td>{{ $config->configuration_name }}</td>
										<td>
											<input type="text" name="configurations[{{ $config->id }}]" 
												value="{{ $config->configuration_value }}" 
												class="form-control" />
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>

						<div class="mt-4 text-right">
							<button type="submit" class="btn btn-primary font-weight-bold">
								Update Configurations
							</button>
						</div>
					</form>
					<!--end::Form-->
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
