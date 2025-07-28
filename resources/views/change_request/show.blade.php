
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
												<h3 class="card-title">{{ $form_title.' #  '.$cr->cr_no }}</h3>
												
											</div>
											<!--begin::Form-->
									
												
												<div class="card-body">
													@foreach($CustomFields as $item)
													@php 
                                                    $custom_field_value = $cr->change_request_custom_fields->where('custom_field_name', $item->CustomField->name)->sortByDesc('id')->first();
													$custom_field_value = $custom_field_value  ? $custom_field_value->custom_field_value  : $cr->{$item->CustomField->name}
													@endphp
														@if($item->CustomField->type == "input")
														<div class="form-group col-md-6" style="float:left">
																	<label for="user_type">
																		{{ $item->CustomField->label }}</label>
                                                                        <input type="text" name="{{ $item->CustomField->name }}" 
                                                                        class="form-control form-control-lg" 
                                                                        value="{{ $custom_field_value }}" disabled />
																</div>
														@elseif($item->CustomField->type == "select")
														<div class="form-group col-md-6" style="float:left">
																	<label for="user_type">{{ $item->CustomField->label }} </label>
																	<select name="{{ $item->CustomField->name }}" class="form-control form-control-lg" disabled>
                                                                        <option value="">Select</option>
																		@if($item->CustomField->name == "new_status_id")
																		
																		
																		<option value="{{ $status_name }}" selected>
																			{{ $status_name }}
																		</option>
																		
                                                                            
																		@else
                                                                        @foreach($item->CustomField->getCustomFieldValue() as $value)
                                                                            <option value="{{ $value->id }}" 
                                                                                {{ $custom_field_value == $value->id ? 'selected' : '' }}>
                                                                                {{ $value->name }}
                                                                            </option>
                                                                        @endforeach
																		@endif
                                                                    </select>
																</div>
														@elseif($item->CustomField->type == "textArea")	
														<div class="form-group col-md-6" style="float:left">
																	<label for="user_type">
																		{{ $item->CustomField->label }}</label>
                                                                        <textarea name="{{ $item->CustomField->name }}" disabled
                                                                        class="form-control form-control-lg">{{ $custom_field_value }}</textarea>
																</div>	
														@endif		
														
													@endforeach
												</div>

												
                                                 <?php
                                            $technical_feedback = $cr->change_request_custom_fields->where('custom_field_name', 'technical_feedback' )->sortByDesc('updated_at');
                                            $business_feedback = $cr->change_request_custom_fields->where('custom_field_name', 'business_feedback' )->sortByDesc('updated_at');
                                             ?>
                                            <div class="form-group col-md-12" style="float:left">
                                            @can('View Technical Feedback')
                                            @if($technical_feedback->count() > 0  )
                                                    <h5>Technichal Feedback</h5>
													<table class="table table-bordered">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th>Feedback</th>
                                                                <th>Updated By</th>
                                                                <th>Updated At</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-center">
                                                            @foreach ($technical_feedback as $index => $feedback)
                                                            <tr>
                                                                <td>{{ $feedback->custom_field_value }}</td>
                                                                <td>{{ optional($feedback->user)->user_name ?? 'N/A' }}</td>
                                                                <td>{{ $feedback->updated_at }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                            @endif
                                            @endcan
                                            @can('View Business Feedback')
                                            @if($business_feedback->count() > 0  )
                                                    <h5>Business Feedback</h5>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th>Feedback</th>
                                                                <th>Updated By</th>
                                                                <th>Updated At</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-center">
                                                            @foreach ($business_feedback as $index => $feedback)
                                                            <tr>
                                                                <td>{{ $feedback->custom_field_value }}</td>
                                                                <td>{{ optional($feedback->user)->user_name ?? 'N/A' }}</td>
                                                                <td>{{ $feedback->updated_at }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                            @endif
                                            @endcan
                                            <!-- end feedback table -->
											@if(count($cr['attachments'])  > 0  )
													<div class="form-group col-md-12" style="float:left">
                                                    @can('View Technichal Attachments')
                                                    <h5>Technichal Attachments</h5>
													<table class="table table-bordered">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th>#</th>
                                                                <th>File Name</th>
                                                                <th>User Name</th>
                                                                <th>Uploaded At</th>
                                                                <th>File Size (MB)</th>
                                                                <th>Download</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-center">
                                                            @foreach ($cr['attachments'] as $key => $file)
                                                            @if ($file->flag == 1)
                                                            <tr>
                                                                <td>{{ ++$key }}</td>
                                                                <td>{{ $file->file }}</td>
                                                                <td>{{ $file->user->user_name }} ({{ $file->user->defualt_group->title }})</td>
                                                                <td>{{ $file->created_at }}</td>
                                                                <td>
                                                                    @if (isset($file->size)) <!-- Ensure the file size is available -->
                                                                    {{ round($file->size / 1024) }} KB
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <a href="{{ route('files.download', $file->id) }}" class="btn btn-light btn-sm">
                                                                        Download
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    @endcan
                                                    @can('View Business Attachments')
                                                    <h5>Business Attachments</h5>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th>#</th>
                                                                <th>File Name</th>
                                                                <th>User Name</th>
                                                                <th>Uploaded At</th>
                                                                <th>File Size (MB)</th>
                                                                <th>Download</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-center">
                                                            @foreach ($cr['attachments'] as $key => $file)
                                                            @if ($file->flag == 2)
                                                            <tr>
                                                                <td>{{ ++$key }}</td>
                                                                <td>{{ $file->file }}</td>
                                                                <td>{{ $file->user->user_name }} ({{ $file->user->defualt_group->title }})</td>
                                                                <td>{{ $file->created_at }}</td>
                                                                <td>
                                                                    @if (isset($file->size)) <!-- Ensure the file size is available -->
                                                                    {{ round($file->size / 1024) }} KB
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <a href="{{ route('files.download', $file->id) }}" class="btn btn-light btn-sm">
                                                                        Download
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    @endcan

													</div>
													@endif
											
											</div>
												
										
											<!--end::Form-->
										</div>
										<!-- Button to trigger the modal -->
										@can('Show CR Logs')
										    <button id="openModal" class="btn btn-primary">View History Logs</button>
										@endcan	

										@include("$view.cr_logs")
										
										
										
										



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




    </script>

@endpush