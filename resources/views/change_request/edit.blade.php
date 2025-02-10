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
													
													<div class="form-group row">
														@include("$view.custom_fields")
													</div>
													
													@if(count($cr['attachments'])  > 0  )
													<div class="form-group col-md-12" style="float:left">
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
        @endforeach
    </tbody>
</table>

													</div>
													@endif
												</div>
                                                
												<div class="card-footer" style="width: 100%;float: right;">
                                                    @if(count($cr->set_status) > 0)
												    <button type="submit" class="btn btn-success mr-2">
                                                        Submit
                                                    </button>
                                                    @endif
													@can('Show CR Logs')
										    			<button id="openModal" class="btn btn-primary">View History Logs</button>
													@endcan	

												</div>
											</form>
											<!--end::Form-->
										</div>
										
										 <!-- Button to trigger the modal -->
										
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
            $('input, select, textarea').prop('disabled', false);
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


</script>
@endpush
