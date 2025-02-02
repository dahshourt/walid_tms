
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
												@if(session('status'))
													@php
														// to get the cr id from the session message
														preg_match('/CR#(\d+)/', session('status'), $matches);
														$cr_id = $matches[1] ?? null;
														$cr_link = $cr_id ? route('show.cr', $cr_id) : null;
													@endphp

													<div id="success-message" style="margin-top: 20px; color: rgb(2, 8, 2); font-weight: bold;">
														{{ session('status') }}:
														@if($cr_link)		
															<a href="#" onclick="viewCR('{{ $cr_link }}')" target="_blank">View CR</a>
														@endif
													</div>
												@endif
												
											</div>

										
											<!--begin::Form-->
											<form class="form" action='{{url("$route")}}' method="post" enctype="multipart/form-data">
                                                {{ csrf_field() }}
												<input type="hidden" name="workflow_type_id" value="{{$workflow_type_id}}">
												<div class="card-body">
													@foreach($CustomFields as $item)
													@if($item->CustomField->type == "file")
															<div class="form-group col-md-6" style="float:left">
																	{{ $item->CustomField->label }}</label>
																			@if( isset($item->validation_type_id)&&($item->validation_type_id==1))
																		<span style="color: red;">*</span>
																		@endif
																	<input type="file"  multiple name="attach[]" class="form-control form-control-lg" />
																</div>	
													@endif
													@if($item->CustomField->type == "input")
    <div class="form-group col-md-6" style="float:left">
        <label for="{{ $item->CustomField->name }}">
            {{ $item->CustomField->label }}
            @if(isset($item->validation_type_id) && $item->validation_type_id == 1)
                <span style="color: red;">*</span>
            @endif
        </label>

        @if(isset($item->enable) && $item->enable == 1)
            @if($item->CustomField->name === 'division_manager')
                <input type="email" id="division_manager" name="{{ $item->CustomField->name }}" 
                       class="form-control form-control-lg @error($item->CustomField->name) is-invalid @enderror" 
                       @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif />
					   @error($item->CustomField->name)
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                <small id="email_feedback" class="form-text text-danger"></small>
            @else
                <input type="text" name="{{ $item->CustomField->name }}" 
                       class="form-control form-control-lg @error($item->CustomField->name) is-invalid @enderror" 
                       @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif />
					   @error($item->CustomField->name)
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
            @endif
            
            <!-- Display all errors under the field -->
            @if($errors->has($item->CustomField->name))
                @foreach($errors->get($item->CustomField->name) as $error)
                    <small class="text-danger d-block">{{ $error }}</small>
                @endforeach
            @endif
        @else
            <label class="form-control form-control-lg">{{ $item->CustomField->name }}</label>
        @endif
 

    </div>






@elseif($item->CustomField->type == "checkbox")

<div class="form-group col-md-6" style="float:left">
 
																	<label for="user_type">
																	{{ $item->CustomField->label }} </label>
																		
 
																		@if((isset($item->enable)&&($item->enable==1)))
																	@if( isset($item->validation_type_id)&&($item->validation_type_id==1))
																	<span style="color: red;">*</span>
																	@endif
																	<div class="form-group">
														
														<div class="checkbox-inline">
															<label class="checkbox">
															<input type="checkbox" name="{{ $item->CustomField->name }}"  class="form-control form-control-lg form-group col-md-3 @error($item->CustomField->name) is-invalid @enderror"   
															
                       @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif
															
															/>
															@error($item->CustomField->name)
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
															<span></span>Yes</label>
															
														</div>
														
													</div>
																	
																	@endif
																</div>


 
														@elseif($item->CustomField->type == "select")
														<div class="form-group col-md-6" style="float:left">
																	<label for="user_type">{{ $item->CustomField->label }} </label>
																	@if( isset($item->validation_type_id)&&($item->validation_type_id==1))
																	<span style="color: red;">*</span>
																	@endif
																	@if((isset($item->enable)&&($item->enable==1)))
																	
																	@if($item->CustomField->name === 'application_id')
																	<select name="{{ $item->CustomField->name }}" class="form-control form-control-lg"  >
																		<option value="{{$target_system->id}}">{{$target_system->name}}</option>
																		
																		
																	</select>
																	@else
																	@if($item->CustomField->name=="cr_member")
																	<select name="{{ $item->CustomField->name }}" class="form-control form-control-lg">
    <option value="">Select</option>
    @foreach($item->CustomField->getCustomFieldValue() as $value)
        @if($value->group_name === 'CR Team Admin') {{-- Filter by group name --}}
            <option value="{{ $value->id }}">{{ $value->name }}</option> 

        @endif
    @endforeach
</select>
@else
																	


																	<select name="{{ $item->CustomField->name }}" class="form-control form-control-lg"  >
																		<option value="">select</option>
																		@foreach($item->CustomField->getCustomFieldValue() as $value)
																			<option value="{{$value->id}}">{{$value->group_name}}</option>
																		@endforeach
																		
																		
																	</select>
																	@endif
																	@endif
																	@else
																	<select name="{{ $item->CustomField->name }}" class="form-control form-control-lg"  disabled>
																	<option value="">select</option>
																	</select>
																	@endif
																</div>
																@elseif($item->CustomField->type == "textArea")
    <div class="form-group col-md-6" style="float:left">
        <label for="user_type">
            {{ $item->CustomField->label }}
            @if(isset($item->validation_type_id) && $item->validation_type_id == 1)
                <span style="color: red;">*</span>
            @endif
        </label>
        
        @if(isset($item->enable) && $item->enable == 1)
            <textarea name="{{ $item->CustomField->name }}" 
                      class="form-control form-control-lg @error($item->CustomField->name) is-invalid @enderror"
                      @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif></textarea>
					  @error($item->CustomField->name)
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
            
            <!-- Display all errors under the field -->
            @if($errors->has($item->CustomField->name))
                @foreach($errors->get($item->CustomField->name) as $error)
                    <small class="text-danger d-block">{{ $error }}</small>
                @endforeach
            @endif
        @else
            <label class="form-control form-control-lg"></label>
        @endif
    </div>


																

														@elseif($item->CustomField->type == "checkbox")
														<div class="form-group col-md-6" style="float:left"
															<label>{{ $item->CustomField->label }}</label>
															@if( isset($item->validation_type_id)&&($item->validation_type_id==1))
																	<span style="color: red;">*</span>
																	@endif
															<div class="checkbox-inline">
																<label class="checkbox checkbox-rounded">
																	<input type="checkbox" name="{{ $item->CustomField->name }}"
																	class="@error($item->CustomField->name) is-invalid @enderror"
                           @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif />
						   @error($item->CustomField->name)
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
																
																	<span></span>
																	{{ $item->CustomField->label }}
																</label>
																
															</div>
														</div>														
														@endif			
														
													@endforeach
													<!-- <div class="form-group col-md-6" style="float:left">
														<input type="file"  multiple name="attach[]" class="form-control form-control-lg" />
													</div> -->
												</div>
                                                
												<div class="card-footer" style="width: 100%;float: right;">
												    <button type="submit" id="submit_button" class="btn btn-success mr-2"  disabled>Submit</button>
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
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");

        form.addEventListener("submit", function (event) {
        
            const submitButton = form.querySelector("button[type='submit']");
            submitButton.disabled = true;
        });
    });
</script>

<script>
	function viewCR(url) {
        
        window.location.href = url;
    }
</script>
=======

<script>
	
	$(window).on('load', function() { 
		check_division_manager_email();
	});
	const submitButton = $('#submit_button');
	const emailFeedback = $('#email_feedback');
	
	$("#division_manager").on('change',function(){
		
		check_division_manager_email();
	});

	function check_division_manager_email()
	{
		submitButton.prop("disabled", true);
		emailFeedback.text("");
		emailFeedback.removeClass('text-success');
		const email = $("#division_manager").val();
		const divisionManagerInput = $("#division_manager");
		if (email) {
			
			$.ajax({
				headers: {
				'X-CSRF-TOKEN': "{{ csrf_token() }}"
				},
				url: '{{url("/")}}/check-division-manager',
				//data: JSON.stringify({ email: email }),
				//processData: false,
				data: {email: email},
                dataType: 'JSON',
				type: 'POST',
				success: function ( data ) {
					if (data.valid) {
						//submitButton.disabled = false;
						submitButton.prop("disabled", false);
						divisionManagerInput.removeClass('is-invalid');
						divisionManagerInput.addClass('is-valid');
						emailFeedback.text(data.message);
						emailFeedback.removeClass('text-danger');
						emailFeedback.addClass('text-success');
					}
					else {
							
						submitButton.prop("disabled", true);
						divisionManagerInput.removeClass('is-valid');
						divisionManagerInput.addClass('is-invalid');
						emailFeedback.text(data.message);
						emailFeedback.removeClass('text-success');
						emailFeedback.addClass('text-danger');
						
					}
				}
			});
		}
	}
	
	
</script>
@endpush



