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
            <button type="button" class="close" data-close="alert" aria-label="Close"></button>
        </div>
    </div>
    @endif

@if (Str::contains(request()->url(), 'show_release'))
	
	{{$disable = "disabled"}}
@else
{{$disable = ""}}
@endif


    <div class="form-group form-group-last"></div>

													<div class="form-group form-group-last">
														
													</div>
													@php 
														if(isset($row))
														{
															$Rtm_disbaled = true; 
															$status_disabled = true; 
														} 
														else
														{
															$Rtm_disbaled = false; 
															$status_disabled = false; 
														}
													@endphp
													@can('Edit Release')
														@php  
															$Rtm_disbaled = false; 
															$status_disabled = false; 
														@endphp
													@endcan
													@can('Edit Release Status')
														@php  $status_disabled = false;  @endphp
													@endcan
													<div class="form-group">
														<label>Name:</label>
														<input type="text" class="form-control form-control-lg" placeholder="Name" name="name" value="{{ isset($row) ? $row->name : old('name') }}" {{ $Rtm_disbaled ? "disabled" :"" }} />
														{!! $errors->first('name', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													@if(isset($row))
													<div class="form-group">
														<label for="status_id">Release Status</label>
														<select class="form-control form-control-lg" id="status_id" name="status" {{ $Rtm_disbaled && $status_disabled ? "disabled" :"" }}>
															@if(isset($current_status))
															@foreach($current_status as $item)
															<option  selected="true" disabled="disabled"> {{ $item->status_name }} </option>
															@endforeach
															@endif
															@if($statuses)
															@foreach($statuses as $item)
															<option value="{{ $item->id }}" {{ isset($row) && $row->status_id == $item->id ? "selected" : "" }}> {{ $item->status_name }} </option>
															@endforeach
															@endif
														</select>
														{!! $errors->first('status_id', '<span class="form-control-feedback">:message</span>') !!}
													</div>
													@endif

													

													<div class="form-group">
														<label>Planned Start IOT Date:</label>
														<input type="date" class="form-control form-control-lg" name="planned_start_iot_date" 
															value="{{ isset($row) ? $row->planned_start_iot_date : old('planned_start_iot_date') }}" {{ $Rtm_disbaled ? "disabled" :"" }} />
														{!! $errors->first('planned_start_iot_date', '<span class="form-control-feedback">:message</span>') !!}
													</div>
													
													<div class="form-group">
														<label>Planned End IOT Date:</label>
														<input type="date" class="form-control form-control-lg" name="planned_end_iot_date" 
															value="{{ isset($row) ? $row->planned_end_iot_date : old('planned_end_iot_date') }}" {{ $Rtm_disbaled ? "disabled" :"" }} />
														{!! $errors->first('planned_end_iot_date', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<div class="form-group">
														<label>Planned Start E2E Date:</label>
														<input type="date" class="form-control form-control-lg" name="planned_start_e2e_date" 
															value="{{ isset($row) ? $row->planned_start_e2e_date : old('planned_start_e2e_date') }}" {{ $Rtm_disbaled ? "disabled" :"" }} />
														{!! $errors->first('planned_start_e2e_date', '<span class="form-control-feedback">:message</span>') !!}
													</div>
													
													<div class="form-group">
														<label>Planned End E2E Date:</label>
														<input type="date" class="form-control form-control-lg" name="planned_end_e2e_date" 
															value="{{ isset($row) ? $row->planned_end_e2e_date : old('planned_end_e2e_date') }}" {{ $Rtm_disbaled ? "disabled" :"" }} />
														{!! $errors->first('planned_end_e2e_date', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<div class="form-group">
														<label>Planned Start UAT Date:</label>
														<input type="date" class="form-control form-control-lg" name="planned_start_uat_date" 
															value="{{ isset($row) ? $row->planned_start_uat_date : old('planned_start_uat_date') }}" {{ $Rtm_disbaled ? "disabled" :"" }} />
														{!! $errors->first('planned_start_uat_date', '<span class="form-control-feedback">:message</span>') !!}
													</div>
													
													<div class="form-group">
														<label>Planned End UAT Date:</label>
														<input type="date" class="form-control form-control-lg" name="planned_end_uat_date" 
															value="{{ isset($row) ? $row->planned_end_uat_date : old('planned_end_uat_date') }}" {{ $Rtm_disbaled ? "disabled" :"" }} />
														{!! $errors->first('planned_end_uat_date', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<div class="form-group">
														<label>Planned Start Smoke Test Date:</label>
														<input type="date" class="form-control form-control-lg" name="planned_start_smoke_test_date" 
															value="{{ isset($row) ? $row->planned_start_smoke_test_date : old('planned_start_smoke_test_date') }}" {{ $Rtm_disbaled ? "disabled" :"" }} />
														{!! $errors->first('planned_start_smoke_test_date', '<span class="form-control-feedback">:message</span>') !!}
													</div>
													
													<div class="form-group">
														<label>Planned End Smoke Test Date:</label>
														<input type="date" class="form-control form-control-lg" name="planned_end_smoke_test_date" 
															value="{{ isset($row) ? $row->planned_end_smoke_test_date : old('planned_end_smoke_test_date') }}" {{ $Rtm_disbaled ? "disabled" :"" }} />
														{!! $errors->first('planned_end_smoke_test_date', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<div class="form-group">
														<label>Go Live Planned Date:</label>
														<input type="date" class="form-control form-control-lg" name="go_live_planned_date" 
															value="{{ isset($row) ? $row->go_live_planned_date : old('go_live_planned_date') }}" {{ $Rtm_disbaled ? "disabled" :"" }} />
														{!! $errors->first('go_live_planned_date', '<span class="form-control-feedback">:message</span>') !!}
													</div>


													
										
													
													
												</div>



</div>

@push('script')
<script>
	$(document).ready(function() {
    //$('#new_status_id').on('change', function() {
        // Get the selected status
        //var status = $('select[name="new_status_id"]').val();
        var status = $('select[name="status"] option:selected').val();
		
        // Check if status is "Reject" or "Closed"
        if (status === "Closed") {
            // Disable all input fields
			console.log(status); 
			console.log('test'); 
            $('input, select, textarea').prop('disabled', true);
        } else {
            // Enable all input fields if status is not "Reject" or "Closed"
            $('input, select, textarea').prop('disabled', false);
        }
        
        // Keep the status field enabled to allow changing
        //$('#status_id').prop('disabled', false);
    //});
});
    $('#user_type').change(function() {
        if ($(this).val() != 1) {
            $(".local_password_div").show();
        } else {
            $(".local_password_div").hide();
        }
    });
</script>
@endpush
