@php
        $fieldName = $item->CustomField->name;
        $fieldLabel = $item->CustomField->label;
        $isRequired = isset($item->validation_type_id) && $item->validation_type_id == 1;
        $isEnabled = isset($item->enable) && $item->enable == 1;
        $inputType = $fieldName === 'division_manager' ? 'email' : 'text';
        $inputValue = isset($cr) ? old($fieldName, $custom_field_value) : old($fieldName);
    @endphp
@if($item->CustomField->type == "multiselect")
    <div class="col-md-6 change-request-form-field field_{{ $item->CustomField->name }}">

        {{-- Smart label rendering --}}
		<label for="{{ $fieldName }}">{{ $item->CustomField->label }}
</label>

        @if(isset($item->validation_type_id) && $item->validation_type_id == 1)
            <span style="color: red;">*</span>
        @endif

        {{-- Special handling for specific fields --}}
       
            @php
                $required = isset($item->validation_type_id) && $item->validation_type_id == 1 ? 'required' : '';
                $disabled = isset($item->enable) && $item->enable != 1 ? 'disabled' : '';
                $customOptions = $item->CustomField->getCustomFieldValue();
            @endphp

            <select name="{{ $fieldName }}[]" class="form-control form-control-lg kt-select2" multiple="multiple" 
			{{ $required }} {{ $disabled }} >
               
                {{-- Dynamic options --}}
                @switch($fieldName)
                    

                        @case('technical_teams')
                            @if(count($selected_technical_teams) > 0)
								@php
									$selected_teams_ids = array_column($selected_technical_teams,'id');
								@endphp
                                @if($isEnabled)
                                    @if($status_name == "Rollback" OR $status_name == "Pending Rollback" OR $status_name == "Pending fixation on production")
                                        <option value="">Select...</option>
                                        @foreach($technical_teams as $team)
                                            <option value="{{ $team['id'] }}" {{ in_array($team['id'],$selected_teams_ids) ? "selected" : "" }}>{{ $team['title'] }}</option>
                                        @endforeach
                                    @else
                                        <option  value="">Select...</option>
                                        @foreach($technical_teams as $team)
                                            <option  value="{{ $team['id'] }}" {{ in_array($team['id'],$selected_teams_ids) ? "selected" : "" }}>{{ $team['title'] }}</option>
                                        @endforeach
                                    @endif
                                @else
                                    <option  value="">Select...</option>
                                    @foreach($technical_teams as $team)
                                        <option  value="{{ $team['id'] }}" {{ in_array($team['id'],$selected_teams_ids) ? "selected" : "" }}>{{ $team['title'] }}</option>
                                    @endforeach
                                @endif
                            @else
                                <option value="">Select...</option>
                                @foreach($technical_teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->title }}</option>
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
            {{-- Hidden inputs to preserve POST data when disabled --}}
            @if(!$isEnabled && count($selected_technical_teams) > 0)
                @foreach($selected_technical_teams as $team)
				{{-- <input type="hidden" name="technical_teams[]" value="{{ $team['id'] }}">--}}
                @endforeach
            @endif
    </div>
@endif
