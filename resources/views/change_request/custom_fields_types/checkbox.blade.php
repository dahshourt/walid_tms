@if($item->CustomField->type === "checkbox")
    @php
        $fieldName = $item->CustomField->name;
        $fieldLabel = $item->CustomField->label;
        $isRequired = isset($item->validation_type_id) && $item->validation_type_id == 1;
        $isEnabled = isset($item->enable) && $item->enable == 1;
        $isChecked = isset($custom_field_value) && $custom_field_value == 1;
    @endphp

    <div class="col-md-6 change-request-form-field">
        <label for="{{ $fieldName }}">{{ $fieldLabel }}
            @if($isRequired)
                <span style="color: red;">*</span>
            @endif
        </label>

        <div class="form-group">
            <div class="checkbox-inline">
                <label class="checkbox">
                    <input type="hidden" name="{{ $fieldName }}" value="0">
                    <input
                        type="checkbox"
                        name="{{ $fieldName }}"
                        value="1"
                        class="form-control form-control-lg form-group col-md-3"
                        {{ $isChecked ? 'checked' : '' }}
                        {{ $isEnabled ? '' : 'disabled' }}
                        {{ $isRequired ? 'required' : '' }}
                    >
                    <span></span>Yes
                </label>
            </div>
        </div>
    </div>
@endif
