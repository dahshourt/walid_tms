
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
								<div class="row">
									
                                    
									<div class="col-md-12">
										


										<!--begin::Card-->
										<div class="card card-custom gutter-b example example-compact">
											<div class="card-header">
												<h3 class="card-title">Add {{ $form_title }}</h3>
												
											</div>
											<!--begin::Form-->
											<form class="form" action='{{url("$route")}}' method="post" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                @include("sla.calculations.form")
												<div class="card-footer">
													<button type="submit" class="btn btn-success mr-2">Submit</button>
													<a href="{{ route('stages.index') }}" class="btn btn-primary">cancel</a>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
/*$(document).ready(function () {
    $('#status_id').on('change', function () {
	 
        var statusId = $(this).val();

        // Clear and disable the group dropdown initially
        $('#group_id').empty().append('<option value="">-- Select Group --</option>').prop('disabled', true);
		 
        if (statusId) {
            $.ajax({
                url: "{{ route('get.groups', '') }}/" + statusId,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $('#group_id').prop('disabled', false);
                    $.each(data, function (key, group) {
                        $('#group_id').append('<option value="' + group.id + '">' + group.name + '</option>');
                    });
                },
                error: function () {
                    alert('Failed to fetch groups. Please try again.');
                }
            });
        }
    });
});*/
</script>