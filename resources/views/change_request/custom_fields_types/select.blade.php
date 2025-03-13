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
                                {{ $status->workflowstatus[0]->to_status->high_level ? $status->workflowstatus[0]->to_status->high_level->name : $status->workflowstatus[0]->to_status->status_name }}
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
    $def1= $cr->defects()->count(); 
    $def2=  $cr->defects()->whereIn('status_id', [86, 87])->count();
?>
@if($item->CustomField->name == "new_status_id" && ($def1 != $def2))
    <script>
        async function checkStatusBeforeSubmit(event) {
            const statusDropdown = document.getElementById("{{ $item->CustomField->name }}");
            const selectedOption = statusDropdown.options[statusDropdown.selectedIndex];
          
            const defectValue = selectedOption.getAttribute('data-defect');

            if (defectValue == "1") {
                // Prevent the form from submitting immediately
                event.preventDefault();

                // Show SweetAlert2 confirmation dialog
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

                // If the user confirms, submit the form programmatically
                if (result.isConfirmed) {
                    document.querySelector("form").submit();
                } else {
                    // Do nothing or handle the cancellation
                }
            }
        }

        // Attach the event listener to the form's submit event
        document.querySelector("form").addEventListener("submit", function(event) {
            checkStatusBeforeSubmit(event);
        });
    </script>
@endif