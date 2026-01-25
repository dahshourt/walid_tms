@if($item->CustomField->type == "checkbox")
    @php
        $isDisabled = isset($item->enable) && $item->enable != 1;
        $isRequired = isset($item->validation_type_id) && $item->validation_type_id == 1;
        $isChecked = isset($custom_field_value) && $custom_field_value == 1;
        $fieldName = $item->CustomField->name;
    @endphp

    <div class="col-md-6 change-request-form-field">
        <label>{{ $item->CustomField->label }} 
            @if($isRequired) <span class="text-danger">*</span> @endif
        </label>
        
        <div class="form-group">
            <input type="hidden" name="{{ $fieldName }}" value="0" {{ $isDisabled ? "disabled" : "" }}>
            
            <label class="crt-checkbox-wrapper {{ $isChecked ? 'checked' : '' }} {{ $isDisabled ? 'disabled' : '' }}">
                <input 
                    type="checkbox" 
                    name="{{ $fieldName }}" 
                    value="1" 
                    class="d-none crt-real-checkbox" 
                    {{ $isChecked ? 'checked' : '' }}
                    {{ $isDisabled ? 'disabled' : '' }}
                    {{ $isRequired ? 'required' : '' }}
                    onchange="this.parentElement.classList.toggle('checked', this.checked)"
                >
                <div class="crt-custom-checkbox"></div>
                <span class="crt-checkbox-label">Yes</span>
            </label>
        </div>
    </div>
@endif
                                                          