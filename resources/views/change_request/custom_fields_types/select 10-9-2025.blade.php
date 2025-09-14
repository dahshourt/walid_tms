@if($item->CustomField->type == "select")
    <div class="col-md-6 change-request-form-field field_{{$item->CustomField->name}}">
        <label for="user_type">{{ $item->CustomField->label }} </label>
        @if(isset($item->validation_type_id) && ($item->validation_type_id == 1))
            <span style="color: red;">*</span>
        @endif
		
        {{-- Route to appropriate select partial --}}
        @switch($item->CustomField->name)
            @case('application_id')
				@if(!isset($cr))
					@include('change_request.custom_fields_types.partials.selects.application-select',['target_system' => $target_system])
				@else
					@include('change_request.custom_fields_types.partials.selects.default-select')
				@endif	
                @break
            @case('cr_member')
                @include('change_request.custom_fields_types.partials.selects.cr-member-select')
                @break
            @case('assignment_user_id')
                @include('change_request.custom_fields_types.partials.selects.assignment-user-select')
                @break
            @case('rejection_reason_id')
                @include('change_request.custom_fields_types.partials.selects.rejection-reason-select')
                @break
            @case('deployment_impact')
                @include('change_request.custom_fields_types.partials.selects.deployment-impact-select')
                @break
            @case('new_status_id')
                @include('change_request.custom_fields_types.partials.selects.status-select')
                @break
            @case('release_name')
                @include('change_request.custom_fields_types.partials.selects.release-select')
                @break
            @default
                @include('change_request.custom_fields_types.partials.selects.default-select')
        @endswitch
    </div>

    {{-- Include scripts --}}
    @include('change_request.custom_fields_types.partials.scripts.select-field-scripts')
@endif