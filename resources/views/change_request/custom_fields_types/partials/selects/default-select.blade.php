{{-- partials/selects/default-select.blade.php --}}
<select name="{{ $item->CustomField->name }}" id="{{ $item->CustomField->name }}" class="form-control form-control-lg" 
        @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif
        @cannot('Set Time For Another User')
            @if(in_array($item->CustomField->name, ['tester_id', 'designer_id', 'developer_id','rtm_member']))
                disabled
            @endif
        @endcannot
        {{ (isset($item->enable) && ($item->enable == 1)) ? 'enabled' : 'disabled' }}>

    {{-- Handle permissions and selected values for role-based fields --}}
    @include('change_request.custom_fields_types.partials.selects.permission-based-options')

    {{-- Handle enabled/disabled field options --}}
    @if((isset($item->enable) && ($item->enable == 1)))
        @include('change_request.custom_fields_types.partials.selects.enabled-field-options')
    @else
        @include('change_request.custom_fields_types.partials.selects.disabled-field-options')
    @endif
</select>