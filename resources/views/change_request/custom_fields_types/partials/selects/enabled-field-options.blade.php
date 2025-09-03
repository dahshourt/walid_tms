{{-- partials/selects/enabled-field-options.blade.php --}}
<option value="">Select</option>

{{-- Role-specific user options --}}
@switch($item->CustomField->name)
    @case('rtm_member')
        @foreach($rtm_members as $rtm_member)
            <option value="{{ $rtm_member->id }}" 
                    {{ old($rtm_member->user_name, $custom_field_value) == $rtm_member->id ? 'selected' : '' }}>
                {{ $rtm_member->user_name }}
            </option>
        @endforeach
        @break
    
    @case('developer_id')
        @foreach($developer_users as $developer)
            <option value="{{ $developer->id }}" 
                    {{ old($developer->user_name, $custom_field_value) == $developer->id ? 'selected' : '' }}>
                {{ $developer->user_name }}
            </option>
        @endforeach
        @break
    
    @case('tester_id')
        @foreach($testing_users as $users)
            <option value="{{ $users->id }}" 
                    {{ old($users->user_name, $custom_field_value) == $users->id ? 'selected' : '' }}>
                {{ $users->user_name }}
            </option>
        @endforeach
        @break
    
    @case('designer_id')
        @foreach($sa_users as $users)
            <option value="{{ $users->id }}" 
                    {{ old($users->user_name, $custom_field_value) == $users->id ? 'selected' : '' }}>
                {{ $users->user_name }}
            </option>
        @endforeach
        @break
@endswitch

{{-- Custom field values for other fields --}}
@foreach($item->CustomField->getCustomFieldValue() as $value)
    @unless(in_array($item->CustomField->name, ['developer_id', 'tester_id', 'designer_id','rtm_member']))
        <option value="{{ $value->id }}" {{ old($item->CustomField->name, $custom_field_value) == $value->id ? 'selected' : '' }}>
            @if($item->CustomField->name == "parent_id")
                {{ $value->change_request->cr_no }} - ({{ $value->change_request->application->name }}) - ({{ $value->change_request->description }})
            @else
                {{ $value->name }}
            @endif
        </option>
    @endunless
@endforeach