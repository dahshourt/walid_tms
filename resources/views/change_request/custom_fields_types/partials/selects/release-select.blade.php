{{-- partials/selects/release-select.blade.php --}}
<select name="{{ $item->CustomField->name }}" id="{{ $item->CustomField->name }}" class="form-control form-control-lg" 
        @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif
        {{ (isset($item->enable) && ($item->enable == 1)) ? 'enabled' : 'disabled' }}>
    
    <option value="">Select</option>
    
    @foreach($cr->get_releases() as $release)
        <option value="{{ $release->id }}" {{ $custom_field_value == $release->id ? 'selected' : '' }}>
            {{ $release->name }}
        </option>
    @endforeach
</select>