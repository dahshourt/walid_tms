<!--begin::Form-->
<div class="row">
    <div class="col-lg-6">
        <!--begin::Form Group-->
        <div class="form-group">
            <label for="type">Input Type <span class="text-danger">*</span></label>
            <select class="form-control form-control-solid @error('type') is-invalid @enderror"
                    id="type" name="type" required>
                <option value="">Select Input Type</option>
                @foreach($inputTypes as $key => $value)
                    <option value="{{ $key }}"
                            {{ old('type', $row->type ?? '') == $key ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
            @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <span class="form-text text-muted">Select the type of input field</span>
        </div>
        <!--end::Form Group-->
    </div>

    <div class="col-lg-6">
        <!--begin::Form Group-->
        <div class="form-group">
            <label for="name">Field Name <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control form-control-solid @error('name') is-invalid @enderror"
                   id="name" name="name"
                   value="{{ old('name', $row->name ?? '') }}"
                   placeholder="Enter field name (e.g., user_email)"
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <span class="form-text text-muted">Unique field name (letters and underscores only)</span>
        </div>
        <!--end::Form Group-->
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <!--begin::Form Group-->
        <div class="form-group">
            <label for="label">Field Label <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control form-control-solid @error('label') is-invalid @enderror"
                   id="label" name="label"
                   value="{{ old('label', $row->label ?? '') }}"
                   placeholder="Enter field label (e.g., Email Address)"
                   required>
            @error('label')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <span class="form-text text-muted">Display label for the field</span>
        </div>
        <!--end::Form Group-->
    </div>

    <div class="col-lg-6">
        <!--begin::Form Group-->
        <div class="form-group">
            <label for="class">CSS Class</label>
            <input type="text"
                   class="form-control form-control-solid @error('class') is-invalid @enderror"
                   id="class" name="class"
                   value="{{ old('class', $row->class ?? '') }}"
                   placeholder="Enter CSS classes (e.g., form-control-lg)">
            @error('class')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <span class="form-text text-muted">Optional CSS classes for styling</span>
        </div>
        <!--end::Form Group-->
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <!--begin::Form Group-->
        <div class="form-group">
            <label for="default_value">Default Value</label>
            <div id="default_value_container">
                <!-- Dynamic input will be inserted here -->
                <input type="text"
                       class="form-control form-control-solid @error('default_value') is-invalid @enderror"
                       id="default_value" name="default_value"
                       value="{{ old('default_value', $row->default_value ?? '') }}"
                       placeholder="Enter default value (optional)">
            </div>
            @error('default_value')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <span class="form-text text-muted" id="default_value_help">Optional default value for the field</span>
        </div>
        <!--end::Form Group-->
    </div>

    <div class="col-lg-6">
        <!--begin::Form Group-->
        <div class="form-group">
            <label for="related_table">Related Table <span class="text-danger" id="related_table_required" style="display: none;">*</span></label>
            <input type="text"
                   class="form-control form-control-solid @error('related_table') is-invalid @enderror"
                   id="related_table" name="related_table"
                   value="{{ old('related_table', $row->related_table ?? '') }}"
                   placeholder="Enter related table name">
            @error('related_table')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <span class="form-text text-muted" id="related_table_help">Optional related database table</span>
        </div>
        <!--end::Form Group-->
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <!--begin::Form Group-->
        <div class="form-group">
            <label for="active">Status</label>
            <div class="col-3">
                <span class="switch switch-outline switch-icon switch-success">
                    <label>
                        <input type="checkbox"
                               id="active" name="active" value="1"
                               {{ old('active', $row->active ?? true) ? 'checked' : '' }} />
                        <span></span>
                    </label>
                </span>
                <label class="checkbox-inline ml-2">Active</label>
            </div>
            @error('active')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <span class="form-text text-muted">Enable or disable this custom field</span>
        </div>
        <!--end::Form Group-->
    </div>
</div>


@push('script')
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    // Check if jQuery is available
    if (typeof $ === 'undefined') {
        return;
    }

    // Auto-generate field name from label
    $('#label').on('input', function() {
        if ($('#name').val() === '') {
            var name = $(this).val()
                .toLowerCase()
                .replace(/[^a-z\s]/g, '')
                .replace(/\s+/g, '_')
                .replace(/^_+|_+$/g, '');
            $('#name').val(name);
        }
    });

    // Validate field name on input
    $('#name').on('input', function() {
        var name = $(this).val();
        var isValid = /^[a-zA-Z_]+$/.test(name);

        if (name && !isValid) {
            $(this).addClass('is-invalid');
            if (!$(this).siblings('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Field name must contain only letters and underscores (no numbers allowed).</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });

    // Dynamic default value input based on type
    function updateDefaultValueInput() {
        var selectedType = $('#type').val();
        var currentValue = $('#default_value').val() || '{{ old("default_value", $row->default_value ?? "") }}';
        var container = $('#default_value_container');
        var helpText = $('#default_value_help');

        // Update related table requirement
        updateRelatedTableRequirement(selectedType);

        // Clear container
        container.empty();

        var inputHtml = '';
        var helpMessage = 'Optional default value for the field';
        var errorClass = '{{ $errors->has("default_value") ? "is-invalid" : "" }}';

        switch(selectedType) {
            case 'select':
                inputHtml = '<select class="form-control form-control-solid ' + errorClass + '" id="default_value" name="default_value">' +
                           '<option value="">Select default option</option>' +
                           '</select>';
                helpMessage = 'Select default option from related table';
                loadTableOptions('select');
                break;

            case 'multiselect':
                inputHtml = '<select class="form-control form-control-solid ' + errorClass + '" id="default_value" name="default_value" multiple>' +
                           '</select>';
                helpMessage = 'Select multiple default options from related table';
                loadTableOptions('multiselect');
                break;

            case 'textArea':
                inputHtml = '<textarea class="form-control form-control-solid ' + errorClass + '" id="default_value" name="default_value" rows="3" placeholder="Enter default text for textarea">' + currentValue + '</textarea>';
                helpMessage = 'Default text content for textarea';
                break;

            case 'date':
                inputHtml = '<input type="date" class="form-control form-control-solid ' + errorClass + '" id="default_value" name="default_value" value="' + currentValue + '" placeholder="Select default date">';
                helpMessage = 'Default date value (YYYY-MM-DD format)';
                break;

            case 'datetime-local':
            case 'dateTimePicker':
                inputHtml = '<input type="datetime-local" class="form-control form-control-solid ' + errorClass + '" id="default_value" name="default_value" value="' + currentValue + '" placeholder="Select default date and time">';
                helpMessage = 'Default date and time value';
                break;

            default:
                inputHtml = '<input type="text" class="form-control form-control-solid ' + errorClass + '" id="default_value" name="default_value" value="' + currentValue + '" placeholder="Enter default value">';
                helpMessage = 'Optional default value for the field';
        }

        container.html(inputHtml);
        helpText.text(helpMessage);
    }

    // Update related table requirement based on type
    function updateRelatedTableRequirement(selectedType) {
        var requiredIndicator = $('#related_table_required');
        var helpText = $('#related_table_help');
        var relatedTableInput = $('#related_table');

        if (selectedType === 'select' || selectedType === 'multiselect') {
            requiredIndicator.show();
            helpText.text('Required: Enter the database table name to load options from');
            relatedTableInput.prop('required', true);
        } else {
            requiredIndicator.hide();
            helpText.text('Optional related database table');
            relatedTableInput.prop('required', false);
        }
    }

    // Load table options via Ajax
    function loadTableOptions(selectType) {
        var tableName = $('#related_table').val().trim();
        var defaultValueSelect = $('#default_value');
        var currentValue = '{{ old("default_value", $row->default_value ?? "") }}';
        
        if (!tableName) {
            defaultValueSelect.html('<option value="">Enter related table name first</option>');
            return;
        }

        // Show loading
        defaultValueSelect.html('<option value="">Loading options...</option>');

        $.ajax({
            url: '{{ route("custom-fields.get-table-options") }}',
            type: 'GET',
            data: {
                table: tableName,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success && response.data) {
                    var options = '';
                    
                    if (selectType === 'select') {
                        options += '<option value="">Select default option</option>';
                    }
                    
                    // For multiselect, split current value by comma if it exists
                    var selectedValues = [];
                    if (currentValue && selectType === 'multiselect') {
                        selectedValues = currentValue.split(',').map(function(val) {
                            return val.trim();
                        });
                    }
                    
                    response.data.forEach(function(item) {
                        var value = item.id || item.value || Object.values(item)[0];
                        var text = item.name || item.title || item.label || Object.values(item)[1] || value;
                        var selected = '';
                        
                        // Check if this option should be selected
                        if (selectType === 'select' && currentValue == value) {
                            selected = ' selected';
                        } else if (selectType === 'multiselect' && selectedValues.includes(value.toString())) {
                            selected = ' selected';
                        }
                        
                        options += '<option value="' + value + '"' + selected + '>' + text + '</option>';
                    });
                    
                    defaultValueSelect.html(options);
                } else {
                    defaultValueSelect.html('<option value="">No options found or table does not exist</option>');
                }
            },
            error: function(xhr) {
                defaultValueSelect.html('<option value="">Error loading options</option>');
            }
        });
    }

    // Initialize default value input on page load
    setTimeout(function() {
        updateDefaultValueInput();
        
        // If we're in edit mode and have a related table for select/multiselect, load options
        var initialType = $('#type').val();
        var initialTable = $('#related_table').val();
        if ((initialType === 'select' || initialType === 'multiselect') && initialTable) {
            setTimeout(function() {
                loadTableOptions(initialType);
            }, 200);
        }
    }, 100);

    // Update default value input when type changes
    $(document).on('change', '#type', function() {
        updateDefaultValueInput();
    });

    // Update default value options when related table changes
    $(document).on('input blur', '#related_table', function() {
        var selectedType = $('#type').val();
        if (selectedType === 'select' || selectedType === 'multiselect') {
            loadTableOptions(selectedType);
        }
    });
});
</script>
@endpush
<!--end::Form-->
