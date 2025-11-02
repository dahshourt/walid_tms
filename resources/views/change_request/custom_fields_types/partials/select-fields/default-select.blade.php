{{-- partials/select-fields/default-select.blade.php --}}

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
        @include('change_request.custom_fields_types.partials.select-fields.status-options', compact('cr', 'custom_field_value'))
        <div id="reason-wrapper"></div>

    @elseif($item->CustomField->name == "release_name")
        <option value="">select</option>
        @foreach($cr->get_releases() as $release)
            <option value="{{ $release->id }}" {{ $custom_field_value == $release->id ? 'selected' : '' }}>
                {{ $release->name }}
            </option>
        @endforeach

    @else
        @include('change_request.custom_fields_types.partials.select-fields.general-options', compact('item', 'custom_field_value', 'cr', 'rtm_members', 'developer_users', 'testing_users', 'sa_users'))
    @endif
</select>
