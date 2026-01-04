{{-- partials/select-fields/general-options.blade.php --}}

@if((isset($item->enable) && ($item->enable == 1)))
    <option value="">Select</option>

    @if($item->CustomField->name == "rtm_member")
        @foreach($rtm_members as $rtm_member)
            <option value="{{ $rtm_member->id }}" {{ old($rtm_member->user_name, $custom_field_value) == $rtm_member->id ? 'selected' : '' }}>
                {{ $rtm_member->user_name }}
            </option>
        @endforeach
    @endif

    @if($item->CustomField->name == "developer_id")
        @foreach($developer_users as $developer)
            <option value="{{ $developer->id }}" {{ old($developer->user_name, $custom_field_value) == $developer->id ? 'selected' : '' }}>
                {{ $developer->user_name }}
            </option>
        @endforeach
    @endif

    @if($item->CustomField->name == "tester_id")
        @foreach($testing_users as $users)
            <option value="{{ $users->id }}" {{ old($users->user_name, $custom_field_value) == $users->id ? 'selected' : '' }}>
                {{ $users->user_name }}
            </option>
        @endforeach
    @endif

    @if($item->CustomField->name == "designer_id")
        @foreach($sa_users as $users)
            <option value="{{ $users->id }}" {{ old($users->user_name, $custom_field_value) == $users->id ? 'selected' : '' }}>
                {{ $users->user_name }}
            </option>
        @endforeach
    @endif

    @php
        $values = $item->CustomField->getCustomFieldValue();
        
        // Filter to show only active requester departments
        if ($item->CustomField->name === 'requester_department') {
            $values = $values->filter(function($value) {
                return $value->active == 1; // Assuming there's an 'active' column in the requester_departments table
            });
        }
    @endphp
    
    @foreach($values as $value)
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
@else
    @php
        $selectedValue = isset($cr) ? old($item->CustomField->name, $custom_field_value) : "";
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
