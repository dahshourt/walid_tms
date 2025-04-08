@if($item->CustomField->type === "button")
    @php
        $fieldName = $item->CustomField->name;
        $fieldLabel = $item->CustomField->label;
        $isRequired = isset($item->validation_type_id) && $item->validation_type_id == 1;
        $isEnabled = isset($item->enable) && $item->enable == 1;
    @endphp

    <div class="col-md-6 change-request-form-field field_{{ $fieldName }}">
        <label for="{{ $fieldName }}">{{ $fieldLabel }}</label>
        @if($isRequired)
            <span style="color: red;">*</span>
        @endif

        @if($isEnabled)
            <a
                href="{{ url('create_defect/cr_id/' . $cr->id) }}"
                target="_blank"
                name="{{ $fieldName }}"
                class="form-control form-control-lg btn-primary"
                style="color:white; font-weight: bold; font-size:15px; text-align:center"
            >
                {{ $fieldLabel }}
            </a>
        @elseif(isset($cr))
            <label class="form-control form-control-lg">{{ $cr->$fieldName }}</label>
        @endif
    </div>
@endif
