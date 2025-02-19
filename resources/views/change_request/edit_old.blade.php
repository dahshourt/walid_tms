@extends('layouts.app')

@section('content')
<style>
        
        .modal {
            display: none; 
            position: fixed;
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); 
        }

      
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%; 
            border-radius: 5px;
        }

       
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        
        .ticket-history {
            max-height: 300px;
            overflow-y: auto;
        }

        .ticket-history ul {
            list-style-type: none;
            padding: 0;
        }

        .ticket-history li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

/* Modal styles */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* Timeline styles */
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    margin-bottom: 20px;
}

.timeline-time {
    font-size: 1.25rem;
    font-weight: bold;
}

.timeline-description {
    padding: 5px;
    background-color: #f4f4f4;
    display: inline-block;
    border-radius: 5px;
}

.timeline-status {
    display: block;
    font-size: 0.85rem;
    margin-top: 5px;
}


    </style>
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
												<h3 class="card-title">{{ $form_title.' #  '.$cr->id }}</h3>
												
											</div>
											<!--begin::Form-->
											<form class="form" action='{{url("$route")}}/{{ $cr->id }}' method="post" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                {{ method_field('PATCH') }}
												<input type="hidden" name="workflow_type_id" value="{{$workflow_type_id}}">
												<input type="hidden" name="old_status_id" value="{{$cr->current_status->new_status_id}}">
												<div class="card-body">
													
													@foreach($CustomFields as $ky => $item)
												  
														@if($item->CustomField->type == "file")
															<div class="form-group col-md-6" style="float:left">
																	{{ $item->CustomField->label }}</label>
																			@if( isset($item->validation_type_id)&&($item->validation_type_id==1))
																		<span style="color: red;">*</span>
																		@endif
																	<input type="file"  multiple name="attach[]" class="form-control form-control-lg" @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif />
																</div>	
														@endif		
														@if($item->CustomField->type == "input")
														
															<div class="form-group col-md-6" style="float:left">
																	<label for="user_type">
																		{{ $item->CustomField->label }}</label>
																		@if( isset($item->validation_type_id)&&($item->validation_type_id==1))
																	<span style="color: red;">*</span>
																	@endif
																		@if(($item->CustomField->name=='testing_estimation')&&!empty($cr->test_duration))
                                                                        <label type="text"
                                                                        class="form-control form-control-lg" 
                                                                       > {{$cr->test_duration}} </label>

																	   @elseif(($item->CustomField->name=='dev_estimation')&&!empty($cr->develop_duration))
                                                                        <label type="text"
                                                                        class="form-control form-control-lg" 
                                                                       > {{$cr->develop_duration}} </label>
																	   @elseif(($item->CustomField->name=='design_estimation')&&!empty($cr->design_duration))
                                                                        <label type="text"
                                                                        class="form-control form-control-lg" 
                                                                       > {{$cr->design_duration}} </label>
																@else
																@if((isset($item->enable)&&($item->enable==1)))
																
																<input type="text" name="{{ $item->CustomField->name }}" 
                                                                        class="form-control form-control-lg" 
                                                                        
																		value="{{ old($item->CustomField->name, $cr->{$item->CustomField->name}) }}" @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif  />
																		@else
																		
																		<label 
                                                                        class="form-control form-control-lg">{{ ($cr->{$item->CustomField->name}) }} </label>
																		@endif
															
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
															<input type="checkbox" name="{{ $item->CustomField->name }}"  
															@if(isset($cr->{$item->CustomField->name}) && $cr->{$item->CustomField->name} == 1) 
        checked 
    @endif
															
															class="form-control form-control-lg form-group col-md-3" @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif     />

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
																	@if(($item->CustomField->name=='tester_id')&&!empty($cr->test_duration))
																		<label type="text"
                                                                        class="form-control form-control-lg" 
                                                                       > {{$cr->tester->name}} </label>
																	   @elseif(($item->CustomField->name=='designer_id')&&!empty($cr->design_duration))
                                                                        <label type="text"
                                                                        class="form-control form-control-lg" 
                                                                       > {{$cr->designer->name}} </label>
																	   @elseif(($item->CustomField->name=='developer_id')&&!empty($cr->develop_duration))
                                                                        <label type="text"
                                                                        class="form-control form-control-lg" 
                                                                       > {{$cr->developer->name}} </label>
																	   @else

																	

																	<select name="{{ $item->CustomField->name }}" class="form-control form-control-lg" @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif
																		@cannot('Set Time For Another User')
																			@if($item->CustomField->name == 'tester_id' || $item->CustomField->name == 'designer_id' || $item->CustomField->name == 'developer_id')
																				 disabled
																			@endif
																		@endcannot   {{(isset($item->enable)&&($item->enable==1))?'enabled':'disabled'}}>

																		@cannot('Set Time For Another User')
																			@if($item->CustomField->name == 'tester_id' || $item->CustomField->name == 'designer_id' || $item->CustomField->name == 'developer_id')
																				 
																				<option value="{{ auth()->user()->id }}" selected>{{ auth()->user()->name }}</option>
																			@endif
																		@endcannot
                                                                        
																		@if($item->CustomField->name == "new_status_id")
																		<option value="{{$cr->getCurrentStatus()?->status?->status_name}}" disabled selected>{{ $cr->getCurrentStatus()?->status?->status_name }}</option>
																		@foreach($cr->set_status as $status)
																		
																		@if($status->same_time == 1)
																		<option value="{{ $status->id }}" 
																		{{ $cr->{$item->CustomField->name} == $status->id ? 'selected' : '' }}>
                                                                                {{ $status->to_status_label }} 
                                                                            </option>
																		@else
																		
																		<option value="{{ $status->id }}" 
                                                                                {{ $cr->{$item->CustomField->name} == $status->id ? 'selected' : '' }}>
                                                                                {{ $status->workflowstatus[0]->to_status->status_name }}
                                                                            </option>
																		@endif            
                                                                        @endforeach
																		@elseif($item->CustomField->name == "release_name")

																		<option value=""> select </option>
																		@foreach($cr->get_releases() as $release)
																		
																		<option value="{{ $release->id }}" 
																		{{ $cr->{$item->CustomField->name} == $release->id ? 'selected' : '' }}>
                                                                                {{ $release->name }} 
                                                                            </option>
																		           
                                                                        @endforeach

																		@else
																		@if((isset($item->enable)&&($item->enable==1)))
																		<option value="">Select</option>
																		<!-- tarek -->
																		@if($item->CustomField->name == "developer_id" )
																			@foreach($developer_users as $developer)
                                                                        		<option value="{{ $developer->id }}" 
	                                                                                {{ old($developer->user_name, $cr->{$item->CustomField->name}) == $developer->id ? 'selected' : '' }}>
	                                                                                {{ $developer->user_name }}
	                                                                            </option>
	                                                                        @endforeach
																		@endif
																		@if($item->CustomField->name == "tester_id" )
																			@foreach($testing_users as $users)
                                                                        		<option value="{{ $users->id }}" 
	                                                                                {{ old($users->user_name, $cr->{$item->CustomField->name}) == $users->id ? 'selected' : '' }}>
	                                                                                {{ $users->user_name }}
	                                                                            </option>
	                                                                        @endforeach
																		@endif
																		@if($item->CustomField->name == "sa_users" )
																			@foreach($sa_users as $users)
                                                                        		<option value="{{ $users->id }}" 
	                                                                                {{ old($users->user_name, $cr->{$item->CustomField->name}) == $users->id ? 'selected' : '' }}>
	                                                                                {{ $users->user_name }}
	                                                                            </option>
	                                                                        @endforeach
																		@endif
																		<!-- -------------------- -->
                                                                        @foreach($item->CustomField->getCustomFieldValue() as $value)
                                                                        	<!-- if($item->CustomField->name == "developer_id"){echo "Yes";} -->
                                                                        	@if($item->CustomField->name == "developer_id" )
                                                                        	@elseif($item->CustomField->name == "tester_id" )
                                                                        	@elseif($item->CustomField->name == "designer_id" )
                                                                        	@else
	                                                                        	<option value="{{ $value->id }}" 
	                                                                                {{ old($item->CustomField->name, $cr->{$item->CustomField->name}) == $value->id ? 'selected' : '' }}>
	                                                                                {{ $value->name }}
	                                                                            </option>		
                                                                        	 @endif
                                                                       	@endforeach
																		@else
																		@php
																		    // Get the selected value from old input or the current record (cr)
																		    $selectedValue = old($item->CustomField->name, $cr->{$item->CustomField->name});
																		@endphp

																		@if($selectedValue)
																		    @foreach($item->CustomField->getCustomFieldValue() as $value)
																		        @if($value->id == $selectedValue)
																		            <option value="{{ $value->id }}" selected>
																		                {{ $value->name }}
																		            </option>
																		        @endif
																		    @endforeach
																		@else
																		    <option value="">Select</option>
																		@endif
                                                                       
																		@endif
																		@endif
                                                                    </select>
																
																	@endif
																</div>
														@elseif($item->CustomField->type == "textArea")	
														<div class="form-group col-md-6" style="float:left">
																	<label for="user_type">
																		{{ $item->CustomField->label }}</label>
																		@if( isset($item->validation_type_id)&&($item->validation_type_id==1))
																	<span style="color: red;">*</span>
																	@endif
																		@if((isset($item->enable)&&($item->enable==1)))
                                                                        <textarea name="{{ $item->CustomField->name }}" 
                                                                        class="form-control form-control-lg" @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif>{{ old($item->CustomField->name, $cr->{$item->CustomField->name}) }}</textarea>
																@else
																<textarea name="{{ $item->CustomField->name }}" disabled
                                                                        class="form-control form-control-lg">{{ old($item->CustomField->name, $cr->{$item->CustomField->name}) }}</textarea>
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
																	<input type="checkbox" name="{{ $item->CustomField->name }}"  @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif {{ $cr->{$item->CustomField->name} ? "checked" : "" }}/>
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
													@if(count($cr['attachments'])  > 0  )
													<div class="form-group col-md-12" style="float:left">
														<table class="table table-bordered">
														    <thead>
														        <tr class="text-center">
														            <th>#</th>
														            <th>File Name</th>
														            <th>User Name</th>
														            <th>Uploaded At</th>
														            <th>Download</th>
														        </tr>
														    </thead>
														    <tbody  class="text-center">
														        @foreach ($cr['attachments'] as $key => $file)
														        <tr>
														            <td>{{++$key}}</td>
														            <td>{{ $file->file }}</td>
														            <td>{{ $file->user->user_name }}({{$file->user->defualt_group->title}})</td>
														            <td>{{ $file->created_at }}</td>
														            <td class="text-center">
														                <a href="{{ route('files.download', $file->id) }}" class="btn btn-light btn-sm">
														                    Download
														                </a>
														            </td>
														        </tr>
														       @endforeach
															</tbody>
														</table>
													</div>
													@endif
												</div>
                                                
												<div class="card-footer" style="width: 100%;float: right;">
												    <button type="submit" class="btn btn-success mr-2">Submit</button>
												</div>
											</form>
											<!--end::Form-->
										</div>
										
										 <!-- Button to trigger the modal -->
										 @can('Show CR Logs')
										    <button id="openModal" class="btn btn-primary">View History Logs</button>
										@endcan	

										@include("$view.cr_logs")

										    <!-- Modal -->
										   <!--  <div id="modal" class="modal">
										        <div class="modal-content">
										            <span class="close">&times;</span>
										            <h2>Ticket History Logs</h2>
										            <div class="timeline">
										            	@foreach($logs_ers as $log)
										                <div class="timeline-item">
										                    <span class="timeline-time">{{$log->created_at}}</span>
										                    <p class="timeline-description">{{$log->log_text}}</p>
										                </div>
										                @endforeach
										            </div>
										        </div>
										    </div> -->


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
      
        // Get modal element
var modal = document.getElementById("modal");
// Get open modal button
var btn = document.getElementById("openModal");
// Get close button
//var closeBtn = document.getElementsByClassName("close")[0];
var closeBtn = document.getElementById("close_logs");
// Listen for open click
btn.onclick = function() {
    modal.style.display = "block";
}

// Listen for close click
closeBtn.onclick = function() {
    modal.style.display = "none";
}

// Listen for outside click
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}



$(document).ready(function() {
    //$('#new_status_id').on('change', function() {
        // Get the selected status
        //var status = $('select[name="new_status_id"]').val();
        var status = $('select[name="new_status_id"] option:selected').val();
        // Check if status is "Reject" or "Closed"
        if (status === "Reject" || status === "Closed" || status === "CR Closed") {
            // Disable all input fields
            $('input, select, textarea').prop('disabled', true);
        } else {
            // Enable all input fields if status is not "Reject" or "Closed"
            $('input, select, textarea').prop('disabled', false);
        }
        
        // Keep the status field enabled to allow changing
        $('#new_status_id').prop('disabled', false);
    //});
});


    </script>

@endpush