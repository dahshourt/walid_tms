@if($item->CustomField->type == "select")
    @php
        // Check if this field should be displayed
        $is_required = false;
        if ($item->CustomField->name == "tech_group_id" && count($technical_groups)> 1) {
            $is_required = true;
        }
    @endphp

        <div class="col-md-6 change-request-form-field field_{{$item->CustomField->name}}">
            <label for="user_type">{{ $item->CustomField->label }}</label>
            @if(isset($item->validation_type_id) && ($item->validation_type_id == 1))
                <span style="color: red;">*</span>
            @endif
			@if($is_required)
				<span style="color: red;">*</span>
			@endif	
            
            @include('change_request.custom_fields_types.partials.select-fields.special-selects', [
                'item' => $item,
                'cr' => $cr ?? null,
                'custom_field_value' => $custom_field_value ?? '',
                'target_system' => $target_system ?? null,
                'sub_applications' => $sub_applications ?? [],
                'assignment_users' => $assignment_users ?? [],
                'rejects' => $rejects ?? [],
                'ApplicationImpact' => $ApplicationImpact ?? null,
                'rtm_members' => $rtm_members ?? [],
                'developer_users' => $developer_users ?? [],
                'testing_users' => $testing_users ?? [],
                'technical_groups' => $technical_groups ?? [],
                'sa_users' => $sa_users ?? []
            ])
        </div>

        
        @include('change_request.custom_fields_types.partials.select-fields.scripts', [
            'cr' => $cr ?? null,
            'def1' => isset($cr) ? $cr->defects()->count() : 0,
            'def2' => isset($cr) ? $cr->defects()->whereIn('status_id', [86, 87])->count() : 0
        ])
@endif