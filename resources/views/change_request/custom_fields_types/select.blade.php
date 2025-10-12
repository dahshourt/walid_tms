@if($item->CustomField->type == "select")
    <div class="col-md-6 change-request-form-field field_{{$item->CustomField->name}}">
        <label for="user_type">{{ $item->CustomField->label }} </label>
        @if(isset($item->validation_type_id) && ($item->validation_type_id == 1))
            <span style="color: red;">*</span>
        @endif

        {{-- Various special select inputs --}}
        @if(!isset($cr) && $item->CustomField->name === 'application_id')
            <select name="{{ $item->CustomField->name }}" class="form-control form-control-lg">
                <option value="{{$target_system->id}}">{{$target_system->name}}</option>
            </select>
		@elseif($item->CustomField->name == "sub_application_id")
			@if(!empty($sub_applications))
				<select name="{{ $item->CustomField->name }}" class="form-control form-control-lg" {{ (isset($item->enable)&&($item->enable!=1)) ? "disabled" : "" }}>
						<option value="">Select</option>
						@foreach($sub_applications as $sub_application)
							<option value="{{$sub_application?->id}}" {{ $custom_field_value == $sub_application->id ? 'selected' : '' }}>{{$sub_application?->name}}</option>
							@endforeach					   
				</select>
			@endif
        @elseif($item->CustomField->name == "cr_member")
            <select name="{{ $item->CustomField->name }}" class="form-control form-control-lg"
			@cannot('Set Time For Another User') disabled @endcannot >
                <option value="">Select</option>
				
                @foreach($item->CustomField->getCustomFieldValue() as $value)
                    @if($value->defualt_group->title === 'CR Team Admin')
                        <option value="{{ $value->id }}" {{ $custom_field_value == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                    @endif
                @endforeach
            </select>
		 @elseif($item->CustomField->name == "assignment_user_id")
            <select name="{{ $item->CustomField->name }}" class="form-control form-control-lg"
			@cannot('Set Time For Another User') disabled @endcannot >
                <option value="">Select</option>
				
                @foreach($assignment_users as $assignment_user)
                        <option value="{{ $assignment_user->id }}" {{ old($assignment_user->user_name, $custom_field_value) == $assignment_user->id ? 'selected' : '' }}>{{ $assignment_user->user_name }}</option>
                @endforeach
            </select>	

            @elseif($item->CustomField->name == "rejection_reason_id")
            
            <select name="{{ $item->CustomField->name }}" class="form-control form-control-lg">
                <option value="">Select</option>
                @if(!empty($rejects))
              
                @foreach($rejects as $reject)
                <option value="{{$reject?->id}}">{{$reject?->name}}</option>
                @endforeach
                @endif
               
</select>


        @elseif($item->CustomField->name == "deployment_impact")
            <select name="{{ $item->CustomField->name }}" class="form-control form-control-lg">
                <option value="">Select</option>
                @foreach($item->CustomField->getCustomFieldValue() as $value)
                 
                    @if(in_array($value->id, $ApplicationImpact->pluck('impacts_id')->toArray()  ))
                        <option value="{{ $value->id }}" {{ $custom_field_value == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                     @endif
                     @if(empty($ApplicationImpact->pluck('impacts_id')->toArray()))
                     <option value="{{ $value->id }}" {{ $custom_field_value == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                     @endif
                @endforeach
            </select>
        @else
        
            <select name="{{ $item->CustomField->name }}" id="{{ $item->CustomField->name }}" class="form-control form-control-lg" 
                @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif
                @cannot('Set Time For Another User')
                    @if(in_array($item->CustomField->name, ['tester_id', 'designer_id', 'developer_id','rtm_member']))
                        disabled
                    @endif
                @endcannot
                {{ (isset($item->enable) && ($item->enable == 1)) ? 'enabled' : 'disabled' }}>

                {{-- Handle permissions and selected values --}}
                @cannot('Set Time For Another User')
                    @if(in_array($item->CustomField->name, ['tester_id', 'designer_id', 'developer_id']))
                        <option value="{{ auth()->user()->id }}" selected>{{ auth()->user()->name }}</option>
                    @endif
                @endcannot

                {{-- Custom logic for statuses --}}
                @if($item->CustomField->name == "new_status_id")
                {{-- Default selected option from current workflow status --}}
<option value="{{ $cr->getCurrentStatus()?->status?->id ?? '' }}" disabled selected>
    {{ $cr->getCurrentStatus()?->status?->status_name ?? 'Select Status' }}
</option>

@foreach($cr->set_status as $status)
    @php
        $toStatus = $status->workflowstatus[0]->to_status ?? null;
    @endphp

    @if($toStatus)
        {{-- If test_duration is 0, null, or empty: allow ID 20 --}}
        @if($cr->test_duration === 0 || $cr->test_duration === null || $cr->test_duration === '')
            @if($toStatus->id == 20 ||$toStatus->id == 48)
                <option value="{{ $status->id }}" 
                    {{ $custom_field_value == $status->id ? 'selected' : '' }}
                    data-status-name="{{ $toStatus->status_name }}"
                    data-defect="{{ $toStatus->defect }}">
                    @if($toStatus->high_level)
                        {{ $toStatus->high_level->name }}
                    @elseif($status->to_status_label)
                        {{ $status->to_status_label }}
                    @else
                        {{ $toStatus->status_name }}
                    @endif
                </option>
 @else
                            <option value="{{ $status->id }}" 
                                {{ $custom_field_value == $status->id ? 'selected' : '' }}
                                data-status-name="{{ $status->workflowstatus[0]->to_status->status_name }}"
                                data-defect="{{ $status->workflowstatus[0]->to_status->defect }}">
                                @if($status->workflowstatus[0]->to_status->high_level)
                                    {{$status->workflowstatus[0]->to_status->high_level->name}}
                                @elseif($status->to_status_label)
                                    {{$status->to_status_label }}
                                @else
                                    {{$status->workflowstatus[0]->to_status->status_name }}
                                @endif
                            </option>
                        @endif

           
        @else
			@if($toStatus->id == 20)
				<option value="{{ $status->id }}" 
                                {{ $custom_field_value == $status->id ? 'selected' : '' }}
                                data-status-name="{{ $status->workflowstatus[0]->to_status->status_name }}"
                                data-defect="{{ $status->workflowstatus[0]->to_status->defect }}">
                                @if($status->workflowstatus[0]->to_status->high_level)
                                    {{$status->workflowstatus[0]->to_status->high_level->name}}
                                @elseif($status->to_status_label)
                                    {{$status->to_status_label }}
                                @else
                                    {{$status->workflowstatus[0]->to_status->status_name }}
                                @endif
                            </option>
			@endif		
            {{-- Else: only allow ID 11 --}}
            @if($toStatus->id != 20) 
            @if($toStatus->id == 74)
                <option value="{{ $status->id }}" 
                    {{ $custom_field_value == $status->id ? 'selected' : '' }}
                    data-status-name="{{ $toStatus->status_name }}"
                    data-defect="{{ $toStatus->defect }}">
                    @if($toStatus->high_level)
                        {{ $toStatus->high_level->name }}
                    @elseif($status->to_status_label)
                        {{ $status->to_status_label }}
                    @else
                        {{ $toStatus->status_name }}
                    @endif
                </option>
                @else
                @if($toStatus->id != 48) 
            @if($toStatus->id == 21)
                <option value="{{ $status->id }}" 
                    {{ $custom_field_value == $status->id ? 'selected' : '' }}
                    data-status-name="{{ $toStatus->status_name }}"
                    data-defect="{{ $toStatus->defect }}">
                    @if($toStatus->high_level)
                        {{ $toStatus->high_level->name }}
                    @elseif($status->to_status_label)
                        {{ $status->to_status_label }}
                    @else
                        {{ $toStatus->status_name }}
                    @endif
                </option>
                @else
                            <option value="{{ $status->id }}" 
                                {{ $custom_field_value == $status->id ? 'selected' : '' }}
                                data-status-name="{{ $status->workflowstatus[0]->to_status->status_name }}"
                                data-defect="{{ $status->workflowstatus[0]->to_status->defect }}">
                                @if($status->workflowstatus[0]->to_status->high_level)
                                    {{$status->workflowstatus[0]->to_status->high_level->name}}
                                @elseif($status->to_status_label)
                                    {{$status->to_status_label }}
                                @else
                                    {{$status->workflowstatus[0]->to_status->status_name }}
                                @endif
                            </option>
                        
            @endif
            @endif
                        
            @endif
            @endif

       


        @endif
    @endif
@endforeach
<div id="reason-wrapper"></div>
<!-- <option value="{{$cr->getCurrentStatus()?->status?->status_name}}" disabled selected>{{ $cr->getCurrentStatus()?->status?->status_name }}</option>
                    @foreach($cr->set_status as $status)
                        @if($status->same_time == 1)
                            <option value="{{ $status->id }}" {{ $custom_field_value == $status->id ? 'selected' : '' }}>{{ $status->to_status_label }}</option>
                        @else
                            <option value="{{ $status->id }}" 
                                {{ $custom_field_value == $status->id ? 'selected' : '' }}
                                data-status-name="{{ $status->workflowstatus[0]->to_status->status_name }}"
                                data-defect31="{{ $status->workflowstatus[0]->to_status->defect }}">
                                @if($status->workflowstatus[0]->to_status->high_level)
                                    {{$status->workflowstatus[0]->to_status->high_level->name}}
                                @elseif($status->to_status_label)
                                    {{$status->to_status_label }}
                                @else
                                    {{$status->workflowstatus[0]->to_status->status_name }}
                                @endif
                            </option>
                        @endif
                    @endforeach -->

                @elseif($item->CustomField->name == "release_name")
                    <option value=""> select </option>
                    @foreach($cr->get_releases() as $release)
                        <option value="{{ $release->id }}" {{ $custom_field_value == $release->id ? 'selected' : '' }}>{{ $release->name }}</option>
                    @endforeach
                @else
                    @if((isset($item->enable) && ($item->enable == 1)))
                        <option value="">Select</option>
						@if($item->CustomField->name == "rtm_member")
                            @foreach($rtm_members as $rtm_member)
                                <option value="{{ $rtm_member->id }}" {{ old($rtm_member->user_name, $custom_field_value) == $rtm_member->id ? 'selected' : '' }}>{{ $rtm_member->user_name }}</option>
                            @endforeach
                        @endif
                        @if($item->CustomField->name == "developer_id")
                            @foreach($developer_users as $developer)
                                <option value="{{ $developer->id }}" {{ old($developer->user_name, $custom_field_value) == $developer->id ? 'selected' : '' }}>{{ $developer->user_name }}</option>
                            @endforeach
                        @endif
                        @if($item->CustomField->name == "tester_id")
                            @foreach($testing_users as $users)
                                <option value="{{ $users->id }}" {{ old($users->user_name, $custom_field_value) == $users->id ? 'selected' : '' }}>{{ $users->user_name }}</option>
                            @endforeach
                        @endif
                        @if($item->CustomField->name == "designer_id")
                            @foreach($sa_users as $users)
                                <option value="{{ $users->id }}" {{ old($users->user_name, $custom_field_value) == $users->id ? 'selected' : '' }}>{{ $users->user_name }}</option>
                            @endforeach
                        @endif

                        @foreach($item->CustomField->getCustomFieldValue() as $value)
                            @unless(in_array($item->CustomField->name, ['developer_id', 'tester_id', 'designer_id','rtm_member']))
                                <option value="{{ $value->id }}" {{ old($item->CustomField->name, $custom_field_value) == $value->id ? 'selected' : '' }}>
								@if($item->CustomField->name == "parent_id")
									{{ $value->change_request->cr_no }} 
								- ({{ $value->change_request->application->name }}) - ({{ $value->change_request->description }})
								@else
									{{ $value->name }} 
								@endif	
								</option>
                            @endunless
                        @endforeach
                    @else
                        @php
                            $selectedValue = isset($cr) ? old($item->CustomField->name, $custom_field_value) : "";
                        @endphp

                        @if($selectedValue)
                            @foreach($item->CustomField->getCustomFieldValue() as $value)
                                @if($value->id == $selectedValue)
                                    <option value="{{ $value->id }}" selected>{{ $value->name }}</option>
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
@endif

{{-- PHP to get defects --}}
@php

    $def1 = isset($cr) ? $cr->defects()->count() : 0;
    $def2 = isset($cr) ? $cr->defects()->whereIn('status_id', [86, 87])->count() : 0;
	if(isset($cr))
	{	
		$status_id = $cr->getCurrentStatus()?->status?->id ?? null;
	}
@endphp

{{-- JavaScript --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const statusSelect = document.querySelector('select[name="new_status_id"]');
    const techTeamWrapper = document.querySelector('.change-request-form-field select[name="technical_teams[]"]')?.closest('.change-request-form-field');
    const techTeamSelect = document.querySelector('select[name="technical_teams[]"]');

    function addAsteriskIfNeeded(wrapper) {
        const label = wrapper.querySelector("label");
        if (label && !label.innerHTML.includes('*')) {
            const star = document.createElement("span");
            star.style.color = "red";
            star.innerHTML = " *";
            label.appendChild(star);
        }
    }

    function removeAsterisk(wrapper) {
        const label = wrapper.querySelector("label");
        if (label) {
            label.innerHTML = label.innerHTML.replace(/\s*<span[^>]*>\*<\/span>/g, '').replace(/\s*\*/g, '');
        }
    }

    function handleStatusChange(value) {
        const hideStatuses = ["260", "223", "273"];
        const requiredStatuses = ["257", "220", "276", "275"];
        const hideTexts = ["Test in Progress", "Pending HL Design", "Assess the defects"];

        if (!techTeamWrapper || !techTeamSelect) return;

        const selectedOption = statusSelect?.options[statusSelect.selectedIndex];
        const selectedText = selectedOption?.textContent.trim();

        if (hideStatuses.includes(value) || hideTexts.includes(selectedText)) {
            techTeamWrapper.style.display = "none";
            techTeamSelect.removeAttribute("required");
            removeAsterisk(techTeamWrapper);
        } else {
            techTeamWrapper.style.display = "";
            if (requiredStatuses.includes(value)) {
                techTeamSelect.setAttribute("required", "required");
                addAsteriskIfNeeded(techTeamWrapper);
            } else {
                techTeamSelect.removeAttribute("required");
                removeAsterisk(techTeamWrapper);
            }
        }
    }

    if (statusSelect) {
        handleStatusChange(statusSelect.value);
        statusSelect.addEventListener("change", function () {
            handleStatusChange(this.value);
        });
    }


    const select = document.querySelector('select[name="new_status_id"]');
    const reasonWrapper = document.getElementById('reason-wrapper');

    if (!select || !reasonWrapper) return;

    <?php if(isset($cr)): ?>
        select.addEventListener('change', function () {
        const selectedLabel = this.options[this.selectedIndex].text.trim();

        // Current status from backend (Blade injects it here)
        const currentValue = {{ $cr->getCurrentStatus()?->status?->id ?? 'null' }};
        
        // If current = 91 and user selects 200
        if (selectedLabel.toLowerCase() === "reject") {
            // Inject new "reason" select if not already added
            if (!document.querySelector('select[name="reason"]')) {
                const reasonSelect = document.createElement('select');
                reasonSelect.name = "reason";
                reasonSelect.classList.add("form-control", "mt-2");

                // Add options
                // [1, 2, 3, 4].forEach(num => {
                //     const opt = document.createElement('option');
                //     opt.value = num;
                //     opt.textContent = "Reason " + num;
                //     reasonSelect.appendChild(opt);
                // });

                reasonWrapper.appendChild(reasonSelect);
            }
        } else {
            // If user changes to something else, remove reason select if it exists
            const existing = document.querySelector('select[name="reason"]');
            if (existing) {
                existing.remove();
            }
        }
    });

});
<?php endif;?>

</script>


@if($def1 != $def2)

<script>
document.addEventListener("DOMContentLoaded", function () {

    async function checkStatusBeforeSubmit(event) {
        const form = event.target;
        const selectElement = form.querySelector('select[name="new_status_id"]');
        const selectedOption = selectElement?.options[selectElement.selectedIndex];
        const defectValue = selectedOption?.getAttribute('data-defect') || "0";

        if (defectValue === "1") {
            event.preventDefault(); // stop normal submit

            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "There are defects related to this CRS. Are you sure you want to continue?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, continue!',
                cancelButtonText: 'No, cancel!'
            });

            if (result.isConfirmed) {
                // Temporarily remove the listener to prevent duplicate alerts
                form.removeEventListener("submit", checkStatusBeforeSubmit);
                form.submit();
                // Re-attach the listener so future submits also show the alert
                form.addEventListener("submit", checkStatusBeforeSubmit);
            }
        }
    }

    const form = document.querySelector("form");
    if (form) {
        form.addEventListener("submit", checkStatusBeforeSubmit);
    }
});
</script>

@endif



