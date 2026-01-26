@if($item->CustomField->type === 'textArea')
    {{-- Input rules --}}
    @php
        $fieldName = $item->CustomField->name;
        $isRequired = isset($item->validation_type_id) && $item->validation_type_id == 1 ? 'required' : '';
        $isDisabled = isset($item->enable) && $item->enable != 1 ? 'disabled' : '';
        $value = isset($cr) ? $custom_field_value : old($fieldName);

        if (in_array($fieldName, ['technical_feedback', 'business_feedback', 'analysis_feedback'], true)) {
            $value = null;
        }
    @endphp

    <div class="col-md-6 change-request-form-field field_{{ $item->CustomField->name }}">
        {{-- Label --}}
        <label for="{{ $item->CustomField->name }}">{{ $item->CustomField->label }}</label>
        @if(isset($item->validation_type_id) && $item->validation_type_id == 1)
            <span class="text-danger">*</span>
        @endif
        {{-- Textarea --}}
        <textarea name="{{ $fieldName }}" id="{{ $fieldName }}"
            class="form-control form-control-lg @error($fieldName) is-invalid @enderror" {{ $isRequired }} {{ $isDisabled }}>{{ $value }}</textarea>

        {{-- Error display --}}
        @error($fieldName)
            <small class="text-danger d-block">{{ $message }}</small>
        @enderror
    </div>
@endif