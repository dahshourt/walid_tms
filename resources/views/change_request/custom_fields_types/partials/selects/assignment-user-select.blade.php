{{-- partials/selects/assignment-user-select.blade.php --}}
<select name="{{ $item->CustomField->name }}" class="form-control form-control-lg"
        @cannot('Set Time For Another User') disabled @endcannot>
    <option value="">Select</option>
    
    @foreach($assignment_users as $assignment_user)
        <option value="{{ $assignment_user->id }}" 
                {{ old($assignment_user->user_name, $custom_field_value) == $assignment_user->id ? 'selected' : '' }}>
            {{ $assignment_user->user_name }}
        </option>
    @endforeach
</select>