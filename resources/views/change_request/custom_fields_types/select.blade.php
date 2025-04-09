@if($item->CustomField->type == "select")
    <div class="col-md-6 change-request-form-field field_{{$item->CustomField->name}}">
        <label for="user_type">{{ $item->CustomField->label }} </label>
        @if(isset($item->validation_type_id) && ($item->validation_type_id == 1))
            <span style="color: red;">*</span>
        @endif

        @if(!isset($cr) && $item->CustomField->name === 'application_id')
            <select name="{{ $item->CustomField->name }}" class="form-control form-control-lg">
                <option value="{{$target_system->id}}">{{$target_system->name}}</option>
            </select>
        @elseif($item->CustomField->name == "cr_member")
            <select name="{{ $item->CustomField->name }}" class="form-control form-control-lg">
                <option value="">Select</option>
                @foreach($item->CustomField->getCustomFieldValue() as $value)
                    @if($value->defualt_group->title === 'CR Team Admin')
                        <option value="{{ $value->id }}" {{ $custom_field_value == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                    @endif
                @endforeach
            </select>
        @else
            <select name="{{ $item->CustomField->name }}" id="{{ $item->CustomField->name }}" class="form-control form-control-lg" 
                @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif
                @cannot('Set Time For Another User')
                    @if($item->CustomField->name == 'tester_id' || $item->CustomField->name == 'designer_id' || $item->CustomField->name == 'developer_id')
                        disabled
                    @endif
                @endcannot
                {{ (isset($item->enable) && ($item->enable == 1)) ? 'enabled' : 'disabled' }}>

                @cannot('Set Time For Another User')
                    @if($item->CustomField->name == 'tester_id' || $item->CustomField->name == 'designer_id' || $item->CustomField->name == 'developer_id')
                        <option value="{{ auth()->user()->id }}" selected>{{ auth()->user()->name }}</option>
                    @endif
                @endcannot

                @if($item->CustomField->name == "new_status_id")
                    <option value="{{$cr->getCurrentStatus()?->status?->status_name}}" disabled selected>{{ $cr->getCurrentStatus()?->status?->status_name }}</option>
                    @foreach($cr->set_status as $status)
                        @if($status->same_time == 1)
                            <option value="{{ $status->id }}" {{ $custom_field_value == $status->id ? 'selected' : '' }}>{{ $status->to_status_label }}</option>
                        @else
                            <option value="{{ $status->id }}" 
                                {{ $custom_field_value == $status->id ? 'selected' : '' }}
                                data-status-name="{{ $status->workflowstatus[0]->to_status->status_name }}"
                                data-defect="{{ $status->workflowstatus[0]->to_status->defect }}">
                                @if($status->workflowstatus[0]->to_status->high_level)
                                {{$status->workflowstatus[0]->to_status->high_level->name}}
                                @else
                                    @if($status->to_status_label)
                                        {{$status->to_status_label }}
                                    @else
                                        {{$status->workflowstatus[0]->to_status->status_name }}
                                    @endif
                                @endif
                            </option>
                        @endif
                    @endforeach
                @elseif($item->CustomField->name == "release_name")
                    <option value=""> select </option>
                    @foreach($cr->get_releases() as $release)
                        <option value="{{ $release->id }}" {{ $custom_field_value == $release->id ? 'selected' : '' }}>{{ $release->name }} </option>
                    @endforeach
                @else
                    @if((isset($item->enable) && ($item->enable == 1)))
                        <option value="">Select</option>
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
                            @if($item->CustomField->name == "developer_id")
                            @elseif($item->CustomField->name == "tester_id")
                            @elseif($item->CustomField->name == "designer_id")
                            @else
                                @if(isset($cr))
                                    <option value="{{ $value->id }}" {{ old($item->CustomField->name, $custom_field_value) == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                                @else
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endif
                            @endif
                        @endforeach
                    @else
                        @php
                            $selectedValue = "";
                            if(isset($cr)) $selectedValue = old($item->CustomField->name, $custom_field_value);
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
<?php 
    $def1 = 0;
    $def2 = 0;
    if(isset($cr))
    {
        $def1= $cr->defects()->count(); 
        $def2=  $cr->defects()->whereIn('status_id', [86, 87])->count();
    }
   $status_id= $cr->getCurrentStatus()?->status?->id;
?>
@if($status_id==120||$status_id==105)
<script>
document.addEventListener("DOMContentLoaded", function () {
    const statusSelect = document.querySelector('select[name="new_status_id"]');

    let techTeamWrapper = null;
    let techTeamSelect = null;
    let originalParent = null;

    document.querySelectorAll('select[name="technical_teams[]"]').forEach(select => {
        techTeamSelect = select;
        techTeamWrapper = select.closest('.change-request-form-field');
        originalParent = techTeamWrapper?.parentNode;
    });

    let storedTechTeamWrapper = null;

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
        const hideStatuses = ["260", "107"];
        const requiredStatuses = ["257", "106"];

        if (hideStatuses.includes(value) && techTeamWrapper) {
            storedTechTeamWrapper = techTeamWrapper;
            techTeamWrapper.remove();
            techTeamWrapper = null;
        } else if (requiredStatuses.includes(value) && !techTeamWrapper && storedTechTeamWrapper) {
            originalParent.appendChild(storedTechTeamWrapper);
            techTeamWrapper = storedTechTeamWrapper;
            storedTechTeamWrapper = null;

            const restoredSelect = techTeamWrapper.querySelector('select[name="technical_teams[]"]');
            if (restoredSelect) {
                restoredSelect.setAttribute("required", "required");
                addAsteriskIfNeeded(techTeamWrapper);
            }
        } else if (requiredStatuses.includes(value) && techTeamWrapper) {
            techTeamSelect?.setAttribute("required", "required");
            addAsteriskIfNeeded(techTeamWrapper);
        } else if (!hideStatuses.includes(value) && techTeamWrapper) {
            techTeamSelect?.removeAttribute("required");
            removeAsterisk(techTeamWrapper);
        } else if (!hideStatuses.includes(value) && !techTeamWrapper && storedTechTeamWrapper) {
            originalParent.appendChild(storedTechTeamWrapper);
            techTeamWrapper = storedTechTeamWrapper;
            storedTechTeamWrapper = null;

            const restoredSelect = techTeamWrapper.querySelector('select[name="technical_teams[]"]');
            if (restoredSelect) {
                restoredSelect.removeAttribute("required");
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
});
</script>
@endif


@if($def1 != $def2)

    <script>
  
  document.addEventListener("DOMContentLoaded", function () {
    async function checkStatusBeforeSubmit(event) {
        let selectElement = document.querySelector('select[name="new_status_id"]');
        if (!selectElement) {
            alert("Dropdown not found");
            return;
        }

        let selectedOption = selectElement.options[selectElement.selectedIndex];
        let defectValue = selectedOption?.getAttribute('data-defect') || "0";
     

        console.log("Defect Value:", defectValue);

       

        if (defectValue == "1") {
          
            event.preventDefault();

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
                document.querySelector("form").submit();
            }
        }

        
    }

    document.querySelector("form")?.addEventListener("submit", checkStatusBeforeSubmit, { once: true });
});

    </script>
@endif