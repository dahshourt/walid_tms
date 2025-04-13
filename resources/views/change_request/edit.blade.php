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
											<div class="card-header">
												<h3 class="card-title">{{ $form_title.' #  '.$cr->id }}

                                               
                                                </h3>
                                                
												
											</div>
											<!--begin::Form-->
											<form class="form" action='{{url("$route")}}/{{ $cr->id }}' method="post" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                {{ method_field('PATCH') }}
												<input type="hidden" name="workflow_type_id" value="{{$workflow_type_id}}">
												<input type="hidden" name="old_status_id" value="{{$cr->current_status->new_status_id}}">
                                                <input type="hidden" name="cab_cr_flag" value="{{isset($cab_cr_flag)?$cab_cr_flag:0}}">
												<div class="card-body">
													
													<div class="form-group row">
														@include("$view.custom_fields")
													</div>
													
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
                                                
												<div class="card-footer" style="width: 100%;float: right;">
                                                    @if(count($cr->set_status) > 0)
                                                        @if($cr->getCurrentStatus()?->status?->id == 68 && $workflow_type_id == 9 && count($reminder_promo_tech_teams) > 0)
                                                            <button type="button" class="btn btn-success mr-2" id="show_error_message">
                                                                Submit
                                                            </button>
                                                        @else
                                                            <button type="submit" class="btn btn-success mr-2">
                                                                Submit
                                                            </button>
                                                        @endif
                                                    @endif
													@can('Show CR Logs')
										    			<button type="button" id="openModal" class="btn btn-primary">View History Logs</button>
													@endcan	
                                                    
												</div>
											</form>
											<!--end::Form-->
                                            
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
                                                            <td>{{ $value->assigned_team->title }}</td>
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
        } else {
            //$('input, select, textarea').prop('disabled', false);
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

    // Function to handle the visibility of rejection reasons field and label
    function handleRejectionReasonsVisibility() {
        if (isStatusReject()) {
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
        } else {
            $(".field_cap_users").hide();
        }
    }

    // Check the status on page load
    handlecapusersVisibility();

    // Add an event listener to the status field to handle change events
    if (statusField) {
        statusField.addEventListener("change", handlecapusersVisibility);
    }
}); 

// handle worlkload validation.. mandatory when transfer status from Analysis to Release plan and optional when transfer status from Analysis to Pending business Feedback

$(document).ready(function () {
    const statusField = $('select[name="new_status_id"]'); 
    const workLoadField = $(".field_cr_workload input");  

    //console.log("Status Field and Work Load Field Found");


    function handleWorkLoadValidation() {
        const selectedStatus = statusField.find("option:selected").text().trim();  
        //console.log("Selected Status:", selectedStatus); 


        if (selectedStatus === "Release Plan") {
            workLoadField.prop("required", true); // mandatory
            console.log("Work Load is now mandatory");
        } else if (selectedStatus === "Pending Business") {
            workLoadField.prop("required", false); // optional
        }
    }

    
    handleWorkLoadValidation();

    statusField.on("change", handleWorkLoadValidation);
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

</script>
@endpush
