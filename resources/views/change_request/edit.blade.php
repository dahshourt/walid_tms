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
        overflow-y: auto;
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        border-radius: 5px;
        overflow-y: auto;
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
											
											<!--begin::Form-->
											 @php
                                             foreach ($cr->change_request_custom_fields as $key => $value) {
                                                 if($value->custom_field_name == "testable")
                                                 {
                                                    $testable = $value->custom_field_value;
                                                 }
                                             }
                                                
                                             @endphp
											<form class="form" action='{{url("$route")}}/{{ $cr->id }}' method="post" enctype="multipart/form-data">

                                                {{ csrf_field() }}
                                                {{ method_field('PATCH') }}

                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h3 class="card-title m-0">{{ $form_title.' #  '.$cr->cr_no }}</h3>
                                                    <div class="d-flex">
                                                        
														@can('Show CR Logs')
										    			<button type="button" id="openModal" class="btn btn-primary">View History Logs</button>
														@endcan	
                                                    
                                                    </div>
                                                </div>

                                                
												<input type="hidden" name="testable_flag" value="@if(!empty($testable)){{$testable}}@else{{0}}@endif" />
												<input type="hidden" name="workflow_type_id" value="{{$workflow_type_id}}">
												<input type="hidden" name="old_status_id" value="{{$cr->current_status->new_status_id}}">
                                                <input type="hidden" name="cab_cr_flag" value="{{isset($cab_cr_flag)?$cab_cr_flag:0}}">
												@if(request()->reference_status)
													<input type="hidden" name="reference_status" value="{{ request()->reference_status }}">
												@endif	
                                               
												<div class="card-body">
													
													<div class="form-group row">
														@include("$view.custom_fields")
													</div>
                                                    @if($cr->current_status->new_status_id == 113)
                                                        @if(count($man_day) > 0)
                                                            @php
                                                                $manDayText = '';
                                                                foreach ($man_day as $item) {
                                                                    $manDayText .= $item['custom_field_value'] . ' ';
                                                                }
                                                                $manDayText = trim($manDayText);
                                                            @endphp
 
                                                            <p><label class="form-control-lg">MD's</label> => {{ $manDayText }}</p>
                                                        @endif
                                                    @endif
													
												</div>

                                                <div class="card-footer" style="width: 100%;float: right;">
                                                    @if(count($cr->set_status) > 0)
                                                        @if($cr->getCurrentStatus()?->status?->id == 68 && $workflow_type_id == 9 && count($reminder_promo_tech_teams) > 0)
														{{--<button type="button" id="submit_button" class="btn btn-success mr-2" id="show_error_message">
                                                                Submit
														</button>--}}
														<button type="submit" id="submit_button" class="btn btn-success mr-2">
                                                                Submit
                                                            </button>
                                                        @else
                                                            <button type="submit" id="submit_button" class="btn btn-success mr-2">
                                                                Submit
                                                            </button>
                                                        @endif
                                                    @endif
													
												
												</div>
                                                
												
											</form>
											<!--end::Form-->

                                            <!-- start feedback table -->
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

                                            @if($man_days)
                                                    <h5>Man Days Logs</h5>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th>Man Day</th>
                                                                <th>Group</th>
                                                                <th>Created At</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-center">
                                                            @foreach ($man_days as $index => $value)
                                                            <tr>
                                                                <td>{{ $value->man_day }}</td>
                                                                <td>{{ $value->group->title }}</td>
                                                                <td>{{ $value->created_at }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                            @endif
                                            <!-- end feedback table -->
											@if(count($cr['attachments'])  > 0  )
													<div class="form-group col-md-12" style="float:left">
                                                    @can('View Technichal Attachments')
                                                    @if(count($cr['attachments']->where('flag', 1))  > 0  )
                                                    <h5>Technichal Attachments</h5>
													<table class="table table-bordered">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th>#</th>
                                                                <th>File Name</th>
                                                                <th>User Name</th>
                                                                <th>Uploaded At</th>
                                                                <th>File Size (MB)</th>
                                                                <th>Action</th>
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
                                                                    @if($file->user->id == \Auth::user()->id || \Auth::user()->hasRole('Super Admin'))
                                                                    <a href="{{ route('files.delete', $file->id) }}" class="btn btn-danger btn-sm">
                                                                        Delete
                                                                    </a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    @endif
                                                    @endcan
                                                    @can('View Business Attachments')
                                                    @if(count($cr['attachments']->where('flag', 2))  > 0  )
                                                    <h5>Business Attachments</h5>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th>#</th>
                                                                <th>File Name</th>
                                                                <th>User Name</th>
                                                                <th>Uploaded At</th>
                                                                <th>File Size (MB)</th>
                                                                <th>Action</th>
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
                                                                    @if($file->user->id == \Auth::user()->id || \Auth::user()->hasRole('Super Admin'))
                                                                    <a href="{{ route('files.delete', $file->id) }}" class="btn btn-danger btn-sm">
                                                                        Delete
                                                                    </a> 
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    @endif
                                                    @endcan

													</div>
													@endif
											
										</div>
                                        @if(count($all_defects) > 0  && $workflow_type_id == 9 )
                                        <div class="card-footer">
                                            <div class="container mt-4">
                                                <h2 class="mb-3">CR Defects</h2>

                                                <table class="table table-striped table-hover table-bordered">
                                                    <thead class="table-primary">
                                                        <tr>
                                                            <th scope="col">User Name</th>
                                                            <th scope="col">Defect Name</th>
                                                            <th scope="col">Group</th>
                                                            <th scope="col">Status</th>
                                                            <th scope="col">Created At </th>
                                                            <th scope="col">Action </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @forelse ($all_defects as $value)
                                                     
                                                        <tr>
                                                            <td>{{ $value->User_created->user_name }}</td>
                                                            <td>{{ $value->subject }}</td>
                                                            <td>{{ $value?->assigned_team?->title }}</td>
                                                            <td>{{ $value->current_status->status_name }}</td>
                                                            <td>{{ $value->created_at->format('Y-m-d H:i:s') }}</td>
                                                            <td> <a href="{{url('edit_defect')}}/{{$value->id}}">Edit </a> </td>
                                                         </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted">No Defects Found.</td>
                                                        </tr>
                                                    @endforelse
                                                    </tbody>
                                                </table>

                                                <!-- Pagination (If Needed) -->
                                                <div class="d-flex justify-content-center">
                                                    
                                                </div>
                                            </div>
                                        </div>
										@endif
										@include("$view.cr_logs")
                                        
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
    var modal = document.getElementById("modal");
    var btn = document.getElementById("openModal");
    var closeBtn = document.getElementById("close_logs");

    btn.onclick = function () {
        modal.style.display = "block";
    };

    closeBtn.onclick = function () {
        modal.style.display = "none";
    };

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
 
    $(document).ready(function () {
        var status = $('select[name="new_status_id"] option:selected').val();
        if (status === "Reject" || status === "Closed" || status === "CR Closed") {
            $('input, select, textarea').prop('disabled', true);
        } 
        $('#new_status_id').prop('disabled', false);
    });
	
$(window).on("load", function () {
    $(".field_rejection_reason_id").hide();
    const statusField = document.querySelector('select[name="new_status_id"]');
    // Function to check if the status is "Reject"
    function isStatusReject() {
        if (statusField) {
            const selectedText = statusField.options[statusField.selectedIndex].text;
            return selectedText === "Reject";
        }
        return false;
    }

    function isStatusPromo() {
        if (statusField) {
            const selectedStatusPromo = statusField.options[statusField.selectedIndex].text;
            return selectedStatusPromo === "Promo Validation";
        }
        return false;
    }

    // Function to handle the visibility of rejection reasons field and label
    function handleRejectionReasonsVisibility() {
        if (isStatusReject()  || isStatusPromo() ) {
            $(".field_rejection_reason_id").show();
        } else {
            $(".field_rejection_reason_id").hide();
        }
    }

    // Check the status on page load
    handleRejectionReasonsVisibility();

    // Add an event listener to the status field to handle change events
    if (statusField) {
        statusField.addEventListener("change", handleRejectionReasonsVisibility);
    }
}); 



 $(window).on("load", function () {
    $(".field_cap_users").hide();
    
    const statusField = document.querySelector('select[name="new_status_id"]');
    // Function to check if the status is "Reject"
    function isStatusReject() {
        if (statusField) {
            const selectedText = statusField.options[statusField.selectedIndex].text;
            return selectedText === "Pending CAB";
        }
        return false;
    }

    // Function to handle the visibility of rejection reasons field and label
    function handlecapusersVisibility() {
        if (isStatusReject()) {
            $(".field_cap_users").show();
			$('select[name="cap_users[]"]').prop('required', true);
        } else {
            $(".field_cap_users").hide();
			$('select[name="cap_users[]"]').prop('required', false);
        }
    }
	
	// Function to handle the technical estimation require
    function handleTechnicalEstimationRequire() {
		const TechnicalEstimationtext = statusField.options[statusField.selectedIndex].text.trim();
		const isPending = TechnicalEstimationtext === "Pending implementation";
		const $dev = $('input[name="dev_estimation"]');

		if (isPending) {
			$dev.prop('required', true);
			// Regex = positive integers only (>=1)
			$dev.attr('pattern', '^[1-9]\\d*$');
			$dev.attr('title', 'Please enter a number greater than 0');
		} else {
			$dev.prop('required', false);
			$dev.removeAttr('pattern');
			$dev.removeAttr('title');
		}
	}

    // Check the status on page load
    handlecapusersVisibility();
    handleTechnicalEstimationRequire();

    // Add an event listener to the status field to handle change events
    if (statusField) {
        statusField.addEventListener("change", handlecapusersVisibility);
        statusField.addEventListener("change", handleTechnicalEstimationRequire);
    }
}); 

// handle worlkload validation.. mandatory when transfer status from Analysis to Release plan and optional when transfer status from Analysis to Pending business Feedback
// handle promo instatus "Review CD" and "SA FB"
$(document).ready(function () {
    const statusField = $('select[name="new_status_id"]'); 
    const workLoadField = $(".field_cr_workload input");
    //const technicalAttachmentField = $(".field_technical_attachments input"); 
    const technicalAttachmentField = $('input[name="technical_attachments[]"]'); 

    //console.log("Status Field and Work Load Field Found");


    function handleWorkLoadValidation() {
        const selectedStatus = statusField.find("option:selected").text().trim();  
        //console.log("Selected Status:", selectedStatus); 
        //console.log("Technical Attachment Field:", technicalAttachmentField.length ? "Found" : "Not found");


        if (selectedStatus === "Release Plan") {
            workLoadField.prop("required", true); // mandatory
            //console.log("Work Load is now mandatory");
        } else if (selectedStatus === "Pending Business") {
            workLoadField.prop("required", false); // optional
        }

        if (selectedStatus === "Test Case Approval") {
            technicalAttachmentField.prop("required", true); // mandatory
            //console.log("Technical Attachment is now mandatory");
        }
        else {
            technicalAttachmentField.prop("required", false); // optional
        }
    }
    //$(document).on('change', 'input[name="need_design"]', handlePromoStatusValidation);

    // function to handle promo, technical teams will be mandatory when selected status is "SA FB" and "Need Design" checkbox is checked
    function handlePromoStatusValidation(){
        
        const selectedStatus = statusField.find("option:selected").text().trim(); 
        const needDesignCheckbox = $('input[name="need_design"]');
        const technicalTeamsField = $('select[name="technical_teams[]"]');
        const techLabel = $('.field_technical_teams label');

        console.log("Selected Status:", selectedStatus); 
        console.log("Need Design Checkbox:", needDesignCheckbox.length ? "Found" : "Not found");
        console.log("Technical Teams Field:", technicalTeamsField.length ? "Found" : "Not found");

        // Check if status is "SA FB" and need_design is checked
        if (selectedStatus === "SA FB" && needDesignCheckbox.is(':checked')) {
            // Make technical teams required
            technicalTeamsField.prop("required", true);
            
            // Add red asterisk if not already there
            if (techLabel.length && !techLabel.find(".required-mark").length) {
                techLabel.append('<span class="required-mark" style="color: red;"> *</span>');
            }
            
            // Add visual styling to indicate required field
            //technicalTeamsField.addClass('required-field');
            
            console.log("Technical Teams is now mandatory - Status: SA FB, Need Design: checked");
        } else {
            // Remove required if conditions are not met
            technicalTeamsField.prop("required", false);
            
            // Remove the asterisk if it exists
            if (techLabel.length) {
                techLabel.find(".required-mark").remove();
            }
            
            // Remove visual styling
            technicalTeamsField.removeClass('required-field');
            
            console.log("Technical Teams is now optional");
        }
    }

    // handle promo, technical teams will be disabled when need_design is checked and enabled when need_design is unchecked
    function handlePromoTechnicalTeams(){
        const currentStatus = "{{ $cr->current_status->new_status_id}}";
        const selectedStatus = statusField.find("option:selected").text().trim(); 
        const needDesignCheckbox = $('input[name="need_design"]');
        const technicalTeamsField = $('select[name="technical_teams[]"]');
        const techLabel = $('.field_technical_teams label');
        const needDesign = "{{ optional($cr->change_request_custom_fields->where('custom_field_name', 'need_design')->first())->custom_field_value ?? 'null' }}";

        console.log("Current Status:", currentStatus); 
        console.log("Selected Status:", selectedStatus); 
        console.log("Need Design Checkbox:", needDesignCheckbox.length ? "Found" : "Not found");
        console.log("Technical Teams Field:", technicalTeamsField.length ? "Found" : "Not found");
        console.log("Need Design:", needDesign);
        // 141 = SA FB
        if (currentStatus == "141"){
            if (needDesign != 'null'){
                //technicalTeamsField.prop("disabled", true);
                console.log("Technical Teams is now disabled");
            }else{
                technicalTeamsField.prop("disabled", false);
                technicalTeamsField.prop("required", true);
                if (techLabel.length && !techLabel.find(".required-mark").length) {
                    techLabel.append('<span class="required-mark" style="color: red;"> *</span>');
                }
                console.log("Technical Teams is now enabled and required");
            }
        }
       
    }
    const currentStatus = "{{ $cr->current_status->new_status_id}}";
    // 141 = SA FB
    // 100 = Review CD
    if (currentStatus == "141"){
        //handlePromoTechnicalTeams();
        statusField.on("change", handlePromoTechnicalTeams);
    }else if(currentStatus == "100"){
        handlePromoStatusValidation();
        statusField.on("change", handlePromoStatusValidation);
        $(document).on('change', 'input[name="need_design"]', handlePromoStatusValidation);

    }else{
        handleWorkLoadValidation();
        statusField.on("change", handleWorkLoadValidation);

    }
    
    /* Also check on page load for initial state
    $(document).ready(function() {
        handlePromoStatusValidation();
    }); */

    
    
});




$(window).on("load", function () {
    const statusField = document.querySelector('select[name="new_status_id"]');
    const responsibleDesignerField = document.querySelector('select[name="designer_id"]'); // Assuming the field is an input field
    const responsibleDesignerLabel = Array.from(document.querySelectorAll('label')).find(label => label.textContent.trim() === "Responsible Designer");
    const DesigneEstimationLabel = Array.from(document.querySelectorAll('label')).find(label => label.textContent.trim() === "Design Estimation");
    const DesigneEstimationInput = document.querySelector('input[name="design_estimation"]');
    
    // Function to check if the status is "Pending Design"
    function isStatusPendingDesign() {
        if (statusField) {
            const selectedText = statusField.options[statusField.selectedIndex].text;
            return selectedText === "Pending Design";
        }
        return false;
    }

    // Function to handle the field as optional or required
    function handleOptionalOrRequiredOption() {
        if (isStatusPendingDesign()) {
            // Add "*" above the field name "Responsible Designer" and make the field required
            if (responsibleDesignerLabel && !responsibleDesignerLabel.innerHTML.includes("*")) {
                /*responsibleDesignerLabel.innerHTML = " * " + responsibleDesignerLabel.innerHTML;
                DesigneEstimationLabel.innerHTML = " * " + DesigneEstimationLabel.innerHTML;*/
                responsibleDesignerLabel.innerHTML = `<span style="color: red;">*</span> ` + responsibleDesignerLabel.innerHTML;
                DesigneEstimationLabel.innerHTML = `<span style="color: red;">*</span> ` + DesigneEstimationLabel.innerHTML;
            }
            if (responsibleDesignerField) {
                responsibleDesignerField.setAttribute("required", true);
                DesigneEstimationInput.setAttribute("required", true);
            }
        } else {
            // Remove "*" above the field name "Responsible Designer" and make the field optional
            if (responsibleDesignerLabel && responsibleDesignerLabel.innerHTML.includes("*")) {
                /*responsibleDesignerLabel.innerHTML = responsibleDesignerLabel.innerHTML.replace("*", "");
                DesigneEstimationLabel.innerHTML = DesigneEstimationLabel.innerHTML.replace("*", "");*/
                responsibleDesignerLabel.innerHTML = responsibleDesignerLabel.innerHTML.replace(/<span style="color: red;">\*<\/span> /, "");
                DesigneEstimationLabel.innerHTML = DesigneEstimationLabel.innerHTML.replace(/<span style="color: red;">\*<\/span> /, "");
            }
            if (responsibleDesignerField) {
                responsibleDesignerField.removeAttribute("required");
                DesigneEstimationInput.removeAttribute("required");
            }
        }
    }

    // Check the status on page load
    handleOptionalOrRequiredOption();

    // Add an event listener to the status field to handle change events
    if (statusField) {
        statusField.addEventListener("change", handleOptionalOrRequiredOption);
    }
});


$("#show_error_message").click(function(){
    let message = " There are group(s) ({{$reminder_promo_tech_teams_text}}) still not transfer CR to Smoke test yet!"
    Swal.fire ('Warning...', message, 'error')
});
document.addEventListener("DOMContentLoaded", function () {
    const selectStatus = document.querySelector('select[name="new_status_id"]');
    const technicalTeams = document.querySelector('select[name="technical_teams[]"]');
    const techLabel = document.querySelector('.field_technical_teams label'); 

    if (selectStatus && technicalTeams && techLabel) {
        selectStatus.addEventListener("change", function () {
            const selectedText = selectStatus.options[selectStatus.selectedIndex].text;


            if (selectedText === "Pending CD FB" || selectedText === "Request MD's") {
                // Make technical teams required
                technicalTeams.setAttribute("required", "required");

                // Add red asterisk if not already there
                if (!techLabel.querySelector(".required-mark")) {
                    const span = document.createElement("span");
                    span.textContent = " *";
                    span.style.color = "red";
                    span.classList.add("required-mark");
                    techLabel.appendChild(span);
                }
            } else {
                // Remove required if status is changed away
                technicalTeams.removeAttribute("required");

                // Remove the asterisk if it exists
                const mark = techLabel.querySelector(".required-mark");
                if (mark) {
                    mark.remove();
                }
            }
        });
    }
});


// Initialize Select2 for all kt-select2 elements
jQuery(document).ready(function() {
    $('.kt-select2').select2({
        placeholder: "Select options",
        allowClear: true,
        width: '100%'
    });
    
    // Reinitialize Select2 after AJAX loads
    $(document).ajaxComplete(function() {
        $('.kt-select2').select2({
            placeholder: "Select options",
            allowClear: true,
            width: '100%'
        });
    });
});


// Testable flag and testing estimation handler - Hidden field version
document.addEventListener('DOMContentLoaded', function() {
    
    // Get the elements (no checkbox needed)
    const testingEstimationInput = document.querySelector('input[name="testing_estimation"]');
    const testableFlagInput = document.querySelector('input[name="testable_flag"]');
    const statusSelectInput = document.querySelector('select[name="new_status_id"]');
    const statusText  = statusSelectInput.options[statusSelectInput.selectedIndex].text.trim(); // visible text

    // Check if elements exist
    if (!testingEstimationInput || !testableFlagInput) {
        console.warn('Testing estimation input or testable_flag hidden field not found');
        return;
    }

    // Function to update UI based on hidden field value
    function updateEstimationFieldState() {
        let flagValue = testableFlagInput.value;
        
        // Handle empty or undefined values - treat them as '0'
        if (!flagValue || flagValue === '' || flagValue.trim() === '') {
            flagValue = '0';
            testableFlagInput.value = '0';
        }
        
        const isTestable = flagValue === '1';
        
        if (isTestable) {
            // Enable the input field
            testingEstimationInput.disabled = false;
            testingEstimationInput.classList.remove('disabled', 'bg-gray-100');
            testingEstimationInput.classList.add('bg-white');
            testingEstimationInput.placeholder = 'Enter testing estimation (must be > 0)';
            
            // Add visual feedback to label
            const label = document.querySelector('label[for="testing_estimation"]');
            if (label) {
                label.classList.remove('text-gray-400');
                label.classList.add('text-gray-700');
            }
            
        } else {
            // Disable the input field and set to 0
            testingEstimationInput.disabled = true;
            testingEstimationInput.classList.add('disabled', 'bg-gray-100');
            testingEstimationInput.classList.remove('bg-white');
            testingEstimationInput.value = '0';
            testingEstimationInput.placeholder = 'Testing not required';
            
            // Clear any validation errors
            clearValidationError(testingEstimationInput);
            
            // Add visual feedback to label
            const label = document.querySelector('label[for="testing_estimation"]');
            if (label) {
                label.classList.add('text-gray-400');
                label.classList.remove('text-gray-700');
            }
        }
    }

    // Function to show validation error
    function showValidationError(input, message) {
        // Remove existing error
        clearValidationError(input);
        
        // Add error class to input
        input.classList.add('border-red-500', 'focus:border-red-500');
        input.classList.remove('border-gray-300');
        
        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.className = 'text-red-500 text-sm mt-1 validation-error';
        errorDiv.textContent = message;
        
        // Insert error message after the input
        input.parentNode.insertBefore(errorDiv, input.nextSibling);
    }

    // Function to clear validation error
    function clearValidationError(input) {
        // Remove error classes
        input.classList.remove('border-red-500', 'focus:border-red-500');
        input.classList.add('border-gray-300');
        
        // Remove error message
        const errorElement = input.parentNode.querySelector('.validation-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    // Function to validate on form submit
    function validateForSubmit() {
        const testableFlagValue = testableFlagInput.value;
        const value = parseFloat(testingEstimationInput.value);
       // const status = statusInput.value;
        // Clear previous validation
        clearValidationError(testingEstimationInput);
        
        const isTestable = testableFlagValue === '1';

       // console.log(statusText);
       /* if(isTestable && statusSelectInput.value == 41){
            showValidationError(testingEstimationInput, 'Testing estimation must be greater than 0 when testable is enabled');
            return false;
        }*/
        console.log((!testingEstimationInput.value || isNaN(value)  ) && statusSelectInput.value == 41 );
        if (isTestable && (!testingEstimationInput.value || isNaN(value) || value <= 0  ) && statusSelectInput.value == 41 ) {
            showValidationError(testingEstimationInput, 'Testing estimation must be greater than 0 when testable is enabled 2   ');
            return false;
        }
        
        return true;
    }

    // Listen for changes to the hidden field (if changed programmatically)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                updateEstimationFieldState();
            }
        });
    });
    
    // Observe the hidden field for value changes
    observer.observe(testableFlagInput, {
        attributes: true,
        attributeFilter: ['value']
    });
    
    // Also listen for input events on hidden field
    testableFlagInput.addEventListener('input', updateEstimationFieldState);
    testableFlagInput.addEventListener('change', updateEstimationFieldState);
    
    // Form submission validation
    const form = testingEstimationInput.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForSubmit()) {
                e.preventDefault();
                e.stopPropagation();
                testingEstimationInput.focus();
                return false;
            }
        });
    }

    // Initialize the state based on hidden field value
    updateEstimationFieldState();
    
    // Function to manually update testable flag (for external use)
    window.updateTestableFlag = function(value) {
        testableFlagInput.value = value ? '1' : '0';
        updateEstimationFieldState();
    };
});

// jQuery version - simplified for hidden field only
if (typeof jQuery !== 'undefined') {
    $(document).ready(function() {
        
        const $testingEstimationInput = $('input[name="testing_estimation"]');
        const $testableFlagInput = $('input[name="testable_flag"]');
        
        if ($testingEstimationInput.length === 0 || $testableFlagInput.length === 0) {
            return;
        }
        
        function updateEstimationFieldStateJQuery() {
            let flagValue = $testableFlagInput.val();
            
            // Handle empty values
            if (!flagValue || flagValue === '' || flagValue.trim() === '') {
                flagValue = '0';
                $testableFlagInput.val('0');
            }
            
            const isTestable = flagValue === '1';
            
            if (isTestable) {
                $testingEstimationInput.prop('disabled', false)
                    .removeClass('disabled bg-gray-100')
                    .addClass('bg-white')
                    .attr('placeholder', 'Enter testing estimation (must be > 0)');
                    
                $('label[for="testing_estimation"]').removeClass('text-gray-400').addClass('text-gray-700');
                
            } else {
                $testingEstimationInput.prop('disabled', true)
                    .addClass('disabled bg-gray-100')
                    .removeClass('bg-white')
                    .val('0')
                    .attr('placeholder', 'Testing not required');
                    
                $('label[for="testing_estimation"]').addClass('text-gray-400').removeClass('text-gray-700');
            }
        }
        
        // Listen for changes to hidden field
        $testableFlagInput.on('change input', updateEstimationFieldStateJQuery);
        
        // Initialize
        updateEstimationFieldStateJQuery();
        
        // Global function for external use
        window.updateTestableFlag = function(value) {
            $testableFlagInput.val(value ? '1' : '0');
            updateEstimationFieldStateJQuery();
        };
    });
}
</script>
@endpush
