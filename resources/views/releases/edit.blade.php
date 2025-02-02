

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
										<h2 class="text-white font-weight-bold my-2 mr-5">{{$title}}</h2>
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
												<h3 class="card-title">Edit {{ $form_title }}</h3>
												
											</div>
											<!--begin::Form-->
											<form class="form" action='{{url("$route")}}/{{ $row->id }}' method="post" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                {{ method_field('PATCH') }}
                                                <input type="hidden" name="id" value="{{ $row->id }}">
                                                @include("$view.form")
												<div class="card-footer">
												@if($row->release_status != '49') 
													<button type="submit" class="btn btn-success mr-2">Submit</button>
													<a href="{{ route('releases.index') }}" class="btn btn-primary">cancel</a>
												@endif
													<a class="btn btn-danger" href="" onclick="OpenPopUpWindow('{{ url('/') }}/release/logs/{{ $row->id }}');return false;" target="_parent">
													
												Release Logs</a>
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
function OpenPopUpWindow(url) {
    window.open(url, "mywindow", "location=1,status=1,scrollbars=1,width=1000,height=900");
}

</script>

@endpush