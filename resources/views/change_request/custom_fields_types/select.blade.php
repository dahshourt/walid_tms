@if($item->CustomField->type === "select")
    @php
        $fieldName = $item->CustomField->name;
        $fieldLabel = $item->CustomField->label;
        $isRequired = isset($item->validation_type_id) && $item->validation_type_id == 1;
        $isEnabled = isset($item->enable) && $item->enable == 1;
        $hasPermissionRestriction = in_array($fieldName, ['tester_id', 'designer_id', 'developer_id']);
    @endphp

    <div class="col-md-6 change-request-form-field field_{{ $fieldName }}">
        <label for="{{ $fieldName }}">{{ $fieldLabel }}</label>
        @if($isRequired)<span style="color: red;">*</span>@endif

        @switch(true)
            @case(!isset($cr) && $fieldName === 'application_id')
                <select name="{{ $fieldName }}" class="form-control form-control-lg">
                    <option value="{{ $target_system->id }}">{{ $target_system->name }}</option>
                </select>
                @break

            @case($fieldName === 'cr_member')
                <select name="{{ $fieldName }}" class="form-control form-control-lg">
                    <option value="">Select</option>
                    @foreach($item->CustomField->getCustomFieldValue() as $value)
                        @if($value->defualt_group->title === 'CR Team Admin')
                            <option value="{{ $value->id }}" {{ $custom_field_value == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                        @endif
                    @endforeach
                </select>
                @break

            @default
                <select
                    name="{{ $fieldName }}"
                    id="{{ $fieldName }}"
                    class="form-control form-control-lg"
                    {{ $isRequired ? 'required' : '' }}
                    @cannot('Set Time For Another User')
                        {{ $hasPermissionRestriction ? 'disabled' : '' }}
                    @endcannot
                    {{ $isEnabled ? 'enabled' : 'disabled' }}
                >
                    @cannot('Set Time For Another User')
                        @if($hasPermissionRestriction)
                            <option value="{{ auth()->user()->id }}" selected>{{ auth()->user()->name }}</option>
                        @endif
                    @endcannot

                    @if($fieldName === 'new_status_id')
                        <option value="{{ $cr->getCurrentStatus()?->status?->status_name }}" disabled selected>{{ $cr->getCurrentStatus()?->status?->status_name }}</option>
                        @foreach($cr->set_status as $status)
                            <option value="{{ $status->id }}"
                                {{ $custom_field_value == $status->id ? 'selected' : '' }}
                                @if(!$status->same_time)
                                    data-status-name="{{ $status->workflowstatus[0]->to_status->status_name }}"
                                    data-defect="{{ $status->workflowstatus[0]->to_status->defect }}"
                                @endif
                            >
                                {{ $status->workflowstatus[0]->to_status->high_level->name ?? $status->to_status_label ?? $status->workflowstatus[0]->to_status->status_name }}
                            </option>
                        @endforeach

                    @elseif($fieldName === 'release_name')
                        <option value="">Select</option>
                        @foreach($cr->get_releases() as $release)
                            <option value="{{ $release->id }}" {{ $custom_field_value == $release->id ? 'selected' : '' }}>{{ $release->name }}</option>
                        @endforeach

                    @else
                        @if($isEnabled)
                            <option value="">Select</option>

                            @php
                                $userList = [
                                    'developer_id' => $developer_users ?? [],
                                    'tester_id' => $testing_users ?? [],
                                    'designer_id' => $sa_users ?? []
                                ];
                            @endphp

                            @if(isset($userList[$fieldName]))
                                @foreach($userList[$fieldName] as $user)
                                    <option value="{{ $user->id }}" {{ old($user->user_name, $custom_field_value) == $user->id ? 'selected' : '' }}>{{ $user->user_name }}</option>
                                @endforeach
                            @endif

                            @foreach($item->CustomField->getCustomFieldValue() as $value)
                                @unless(in_array($fieldName, array_keys($userList)))
                                    <option value="{{ $value->id }}" {{ (isset($cr) && old($fieldName, $custom_field_value) == $value->id) ? 'selected' : '' }}>{{ $value->name }}</option>
                                @endunless
                            @endforeach
                        @else
                            @php
                                $selectedValue = isset($cr) ? old($fieldName, $custom_field_value) : '';
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
        @endswitch
    </div>
@endif

@php
    $def1 = isset($cr) ? $cr->defects()->count() : 0;
    $def2 = isset($cr) ? $cr->defects()->whereIn('status_id', [86, 87])->count() : 0;
@endphp

@if($def1 !== $def2)
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelector("form")?.addEventListener("submit", async function checkStatusBeforeSubmit(event) {
                const selectElement = document.querySelector('select[name="new_status_id"]');
                const selectedOption = selectElement?.options[selectElement.selectedIndex];
                const defectValue = selectedOption?.getAttribute('data-defect') || "0";

                if (defectValue === "1") {
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
            }, { once: true });
        });
    </script>
@endif
