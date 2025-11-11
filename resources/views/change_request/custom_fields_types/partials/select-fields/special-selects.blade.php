{{-- partials/select-fields/special-selects.blade.php --}}

@if(!isset($cr) && $item->CustomField->name === 'application_id')
    @include('change_request.custom_fields_types.partials.select-fields.application-select', compact('item', 'target_system'))

@elseif($item->CustomField->name == "sub_application_id")
    @include('change_request.custom_fields_types.partials.select-fields.sub-application-select', compact('item', 'sub_applications', 'custom_field_value'))
	
@elseif($item->CustomField->name == "tech_group_id")
    @include('change_request.custom_fields_types.partials.select-fields.tech-groups-select', compact('item', 'technical_groups', 'custom_field_value'))	

@elseif($item->CustomField->name == "cr_member")
    @include('change_request.custom_fields_types.partials.select-fields.cr-member-select', compact('item', 'custom_field_value'))

@elseif($item->CustomField->name == "assignment_user_id")
    @include('change_request.custom_fields_types.partials.select-fields.assignment-user-select', compact('item', 'assignment_users', 'custom_field_value'))

@elseif($item->CustomField->name == "rejection_reason_id")
    @include('change_request.custom_fields_types.partials.select-fields.rejection-reason-select', compact('item', 'rejects', 'custom_field_value'))

@elseif($item->CustomField->name == "deployment_impact")
    @include('change_request.custom_fields_types.partials.select-fields.deployment-impact-select', compact('item', 'ApplicationImpact', 'custom_field_value'))

@else
    @include('change_request.custom_fields_types.partials.select-fields.default-select', compact('item', 'custom_field_value', 'cr', 'rtm_members', 'developer_users', 'testing_users', 'sa_users'))
@endif