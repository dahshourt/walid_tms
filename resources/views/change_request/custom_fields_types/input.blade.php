@if($item->CustomField->type == "input")
    @php
        $fieldName = $item->CustomField->name;
        $fieldLabel = $item->CustomField->label;
        $isRequired = isset($item->validation_type_id) && $item->validation_type_id == 1;
        $isEditable = isset($item->enable) && $item->enable == 1;
        $isEstimationField = in_array($fieldName, ['dev_estimation','design_estimation','testing_estimation']);
        $fieldType = $isEstimationField ? 'number' : ($fieldName === 'requester_email' || $fieldName === 'division_manager' ? 'email' : 'text');

        // Determine value
        if (isset($cr)) {
            $fieldValue = old($fieldName, $cr->{$fieldName} ?? ($custom_field_value ?? ''));
        } else {
            $fieldValue = old($fieldName, $custom_field_value ?? '');
        }
    @endphp

    <div class="col-md-6 change-request-form-field field_{{ $fieldName }}">
        <label for="{{ $fieldName }}">{{ $fieldLabel }}</label>
        @if($isRequired)
            <span style="color: red;">*</span>
        @endif

     
        @if($isEditable || $isEstimationField)

            {{-- Special case: division_manager --}}
            @if($fieldName === 'division_manager')
                <input 
                    type="email" 
                    id="division_manager"
                    name="{{ $fieldName }}" 
                    class="form-control form-control-lg @error($fieldName) is-invalid @enderror" 
                    value="{{ $fieldValue }}"
                    @if($isRequired) required @endif
                />
                @error($fieldName)
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                <small id="email_feedback" class="form-text text-danger"></small>

            
            @elseif(in_array($fieldName, ['requester_name', 'requester_email']))
                <input 
                    type="{{ $fieldType }}"
                    name="{{ $fieldName }}"
                    class="form-control form-control-lg @error($fieldName) is-invalid @enderror"
                    value="{{ $fieldName === 'requester_name' ? auth()->user()->name : auth()->user()->email }}"
                    readonly
                    @if($isRequired) required @endif
                />
                @error($fieldName)
                    <small class="text-danger">{{ $message }}</small>
                @enderror

         
            @else
                <input 
                    type="{{ $fieldType }}"
                    name="{{ $fieldName }}"
                    class="form-control form-control-lg @error($fieldName) is-invalid @enderror"
                    value="{{ $fieldValue }}"
                    @if($isRequired) required @endif
                />
                @error($fieldName)
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            @endif

       
        @elseif(isset($cr))
            <input 
                type="{{ $fieldType }}"
                name="{{ $fieldName }}"
                class="form-control form-control-lg"
                value="{{ $fieldValue }}"
                disabled
            />
        @endif
    </div>
@endif
