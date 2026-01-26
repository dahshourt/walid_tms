@if($item->CustomField->type == "file")
    @php
        $isDisabled = isset($item->enable) && $item->enable != 1;
        $isRequired = isset($item->validation_type_id) && $item->validation_type_id == 1;
        $fieldName = $item->CustomField->name . '[]';
    @endphp

    <div class="col-md-6 change-request-form-field">
        <label>{{ $item->CustomField->label }}
            @if($isRequired) <span class="text-danger">*</span> @endif
        </label>

        <div class="crt-file-upload-wrapper {{ $isDisabled ? 'disabled' : '' }}">
            <input type="file" multiple name="{{ $fieldName }}" class="crt-file-input" {{ $isDisabled ? 'disabled' : '' }}
                {{ $isRequired ? 'required' : '' }} onchange="
                                                var names = Array.from(this.files).map(f => f.name).join(', ');
                                                this.nextElementSibling.querySelector('.crt-file-name-display').textContent = names || '';
                                            " />

            <div class="crt-file-content">
                <div class="crt-file-icon">
                    <i class="flaticon-upload text-primary" style="font-size: 2rem;"></i>
                </div>
                <div class="crt-file-label">Click to upload files</div>
                <div class="crt-file-hint">Max file size: 50MB</div>
                <div class="crt-file-hint">file types: doc, docx, xls, xlsx, pdf, zip, rar, jpeg, jpg, png, gif, msg</div>
                <div class="crt-file-name-display"></div>
            </div>
        </div>
    </div>
@endif