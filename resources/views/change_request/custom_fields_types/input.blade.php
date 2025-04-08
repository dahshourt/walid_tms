@if($item->CustomField->type === 'input')
    @php
        $fieldName = $item->CustomField->name;
        $fieldLabel = $item->CustomField->label;
        $isRequired = isset($item->validation_type_id) && $item->validation_type_id == 1;
        $isEnabled = isset($item->enable) && $item->enable == 1;
        $inputType = $fieldName === 'division_manager' ? 'email' : 'text';
        $inputValue = isset($cr) ? old($fieldName, $custom_field_value) : old($fieldName);
    @endphp

    <div class="col-md-6 change-request-form-field field_{{ $fieldName }}">
        <label for="{{ $fieldName }}">
            {{ $fieldLabel }}
            @if($isRequired)
                <span style="color: red;">*</span>
            @endif
        </label>

        @if($isEnabled)
            @if(isset($cr) || $fieldName !== 'division_manager')
                <input 
                    type="{{ $inputType }}" 
                    name="{{ $fieldName }}" 
                    class="form-control form-control-lg @error($fieldName) is-invalid @enderror" 
                    value="{{ $inputValue }}" 
                    {{ $isRequired ? 'required' : '' }}
                />
                @error($fieldName)
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            @else
                <input 
                    type="email" 
                    id="division_manager" 
                    name="{{ $fieldName }}" 
                    class="form-control form-control-lg @error($fieldName) is-invalid @enderror" 
                    value="{{ $inputValue }}" 
                    {{ $isRequired ? 'required' : '' }}
                />
                @error($fieldName)
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                <small id="email_feedback" class="form-text text-danger"></small>
            @endif
        @elseif(isset($cr))
            <label class="form-control form-control-lg">{{ $cr->{$fieldName} }}</label>
        @endif
    </div>
@endif
