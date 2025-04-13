@if($item->CustomField->type === "file")
    @php
        $fieldName = $item->CustomField->name;
        $fieldLabel = $item->CustomField->label;
        $isRequired = isset($item->validation_type_id) && $item->validation_type_id == 1;
    @endphp

    <div class="col-md-6 change-request-form-field">
        <label for="{{ $fieldName }}">
            {{ $fieldLabel }}
            @if($isRequired)
                <span style="color: red;">*</span>
            @endif
        </label>

        <input
            type="file"
            name="{{ $fieldName }}[]"
            multiple
            class="form-control form-control-lg"
            {{ $isRequired ? 'required' : '' }}
        />
    </div>
@endif
