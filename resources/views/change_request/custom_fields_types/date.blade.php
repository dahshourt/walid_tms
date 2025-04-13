@if($item->CustomField->type === "date")
    @php
        $fieldName = $item->CustomField->name;
        $fieldLabel = $item->CustomField->label;
        $isRequired = isset($item->validation_type_id) && $item->validation_type_id == 1;
        $isEnabled = isset($item->enable) && $item->enable == 1;
        $disabled = $isEnabled ? '' : 'disabled';
        $required = $isRequired ? 'required' : '';
        $value = isset($cr) ? ($custom_field_value ?? $cr->{$fieldName}) : old($fieldName);
    @endphp

    <div class="col-md-6 change-request-form-field field_{{ $fieldName }}">
        <label for="{{ $fieldName }}">
            {{ $fieldLabel }}
            @if($isRequired)
                <span style="color: red;">*</span>
            @endif
        </label>

        @if($isEnabled)
            <input
                type="date"
                name="{{ $fieldName }}"
                value="{{ $value }}"
                class="form-control form-control-lg"
                style="color:black; font-weight: bold; font-size:15px; text-align:center"
                {{ $required }}
            />
        @else
            @if(isset($cr))
                <label class="form-control form-control-lg">{{ $cr->{$fieldName} }}</label>
            @endif
        @endif

        {!! $errors->first($fieldName, '<span class="form-control-feedback">:message</span>') !!}
    </div>
@endif
