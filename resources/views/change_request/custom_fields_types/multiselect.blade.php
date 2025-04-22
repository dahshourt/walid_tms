@if($item->CustomField->type == "multiselect")
    <div class="col-md-6 change-request-form-field field_{{ $item->CustomField->name }}">

        {{-- Smart label rendering --}}
        @php
            $fieldName = $item->CustomField->name;
            $durationFieldMap = [
                'tester_id' => 'test_duration',
                'designer_id' => 'design_duration',
                'developer_id' => 'develop_duration',
            ];
            $showLabelName = $durationFieldMap[$fieldName] ?? null;
        @endphp

        @if($showLabelName && !empty($cr->{$durationFieldMap[$fieldName]}))
            <label type="text" class="form-control form-control-lg"> {{ $cr->{$fieldName == 'tester_id' ? 'tester' : ($fieldName == 'designer_id' ? 'designer' : 'developer')}->name }} </label>
        @else
            <label for="{{ $fieldName }}">{{ $item->CustomField->label }}</label>
        @endif

        @if(isset($item->validation_type_id) && $item->validation_type_id == 1)
            <span style="color: red;">*</span>
        @endif

        {{-- Special handling for specific fields --}}
        @if(!isset($cr) && $fieldName === 'application_id')
            <select name="{{ $fieldName }}" class="form-control form-control-lg" multiple>
                <option value="{{ $target_system->id }}">{{ $target_system->name }}</option>
            </select>
        @elseif($fieldName === 'cr_member')
            <select name="{{ $fieldName }}" class="form-control form-control-lg" multiple>
                <option value="">Select</option>
                @foreach($item->CustomField->getCustomFieldValue() as $value)
                    @if($value->defualt_group->title === 'CR Team Admin')
                        <option value="{{ $value->id }}" {{ isset($cr) && $cr->{$fieldName} == $value->id ? 'selected' : '' }}>
                            {{ $value->name }}
                        </option>
                    @endif
                @endforeach
            </select>
        @else
            @php
                $required = isset($item->validation_type_id) && $item->validation_type_id == 1 ? 'required' : '';
                $disabled = isset($item->enable) && $item->enable != 1 ? 'disabled' : '';
                $customOptions = $item->CustomField->getCustomFieldValue();
            @endphp

            <select name="{{ $fieldName }}[]" class="form-control form-control-lg" multiple {{ $required }} {{ $disabled }}
                @cannot('Set Time For Another User')
                    @if(in_array($fieldName, ['tester_id', 'designer_id', 'developer_id'])) disabled @endif
                @endcannot>

                {{-- Permissions logic --}}
                @cannot('Set Time For Another User')
                    @if(in_array($fieldName, ['tester_id', 'designer_id', 'developer_id']))
                        <option value="{{ auth()->user()->id }}" selected>{{ auth()->user()->name }}</option>
                    @endif
                @endcannot

                {{-- Dynamic options --}}
                @switch($fieldName)
                    @case('new_status_id')
                        <option value="{{ $cr->getCurrentStatus()?->status?->status_name }}" disabled selected>
                            {{ $cr->getCurrentStatus()?->status?->status_name }}
                        </option>
                        @foreach($cr->set_status as $status)
                            <option value="{{ $status->id }}" {{ $cr->{$fieldName} == $status->id ? 'selected' : '' }}>
                                {{ $status->same_time == 1 
                                    ? $status->to_status_label 
                                    : ($status->workflowstatus[0]->to_status->high_level->name ?? $status->workflowstatus[0]->to_status->status_name) }}
                            </option>
                        @endforeach
                        @break

                    @case('release_name')
                        <option value="">Select</option>
                        @foreach($cr->get_releases() as $release)
                            <option value="{{ $release->id }}" {{ $cr->{$fieldName} == $release->id ? 'selected' : '' }}>
                                {{ $release->name }}
                            </option>
                        @endforeach
                        @break

                    @case('developer_id')
                        <option value="">Select</option>
                        @foreach($developer_users as $dev)
                            <option value="{{ $dev->id }}" {{ old($fieldName, $cr->{$fieldName}) == $dev->id ? 'selected' : '' }}>
                                {{ $dev->user_name }}
                            </option>
                        @endforeach
                        @break

                    @case('tester_id')
                        <option value="">Select</option>
                        @foreach($testing_users as $tester)
                            <option value="{{ $tester->id }}" {{ old($fieldName, $cr->{$fieldName}) == $tester->id ? 'selected' : '' }}>
                                {{ $tester->user_name }}
                            </option>
                        @endforeach
                        @break

                    @case('sa_users')
                        <option value="">Select</option>
                        @foreach($sa_users as $sa)
                            <option value="{{ $sa->id }}" {{ old($fieldName, $cr->{$fieldName}) == $sa->id ? 'selected' : '' }}>
                                {{ $sa->user_name }}
                            </option>
                        @endforeach
                        @break

                    @case('cap_users')
                        <option value="">Select</option>
                        @foreach($cap_users as $cap)
                            <option value="{{ $cap->user_id }}">{{ $cap->user->name }}</option>
                        @endforeach
                        @break

                    @case('technical_teams')
                        @if($status_name == 'Pending HL Design')
                                <option value="">Select...</option>
                            @foreach($technical_teams as $team)
                                <option  value="{{ $team->id }}">{{ $team->title }}</option>
                            @endforeach    
                        @elseif($status_name == 'Rollback'  OR  $status_name == 'Pending fixation on production' OR  $status_name == 'Pending Rework' )
                                <option disabled value="">Select...</option>
                            @foreach($technical_team_disabled as $team)
                                <option disabled value="{{ $team->id }}">{{ $team->title }}</option>
                            @endforeach 
                        @else
                            <option disabled value="">Select...</option>
                            @foreach($technical_teams as $team)
                                <option disabled value="{{ $team->id }}">{{ $team->title }}</option>
                            @endforeach    
                        @endif
                        @break

                    @default
                        @if(isset($customOptions) && count($customOptions))
                            <option value="">Select</option>
                            @foreach($customOptions as $option)
                                <option value="{{ $option->id }}" {{ old($fieldName, $cr->{$fieldName}) == $option->id ? 'selected' : '' }}>
                                    {{ $option->name }}
                                </option>
                            @endforeach
                        @endif
                @endswitch
            </select>
        @endif
    </div>
@endif
