{{-- partials/selects/cr-member-select.blade.php --}}
<select name="{{ $item->CustomField->name }}" class="form-control form-control-lg"
        @cannot('Set Time For Another User') disabled @endcannot>
    <option value="">Select</option>
    
    @foreach($item->CustomField->getCustomFieldValue() as $value)
        @if($value->defualt_group->title === 'CR Team Admin')
            <option value="{{ $value->id }}" {{ $custom_field_value == $value->id ? 'selected' : '' }}>
                {{ $value->name }}
            </option>
        @endif
    @endforeach
</select>