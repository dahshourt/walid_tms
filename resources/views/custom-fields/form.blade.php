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
            <label for="related_table">Related Table <span class="text-danger" id="related_table_required"
                                                           style="display: none;">*</span></label>
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

    <div class="col-lg-6">
        <!--begin::Form Group-->
        <div class="form-group">
            <label for="log_message">
                Log Message
                <button type="button" class="btn btn-sm btn-light-primary ml-2" onclick="insertPlaceholder('log_message', ':cf_label')">
                    CF Label
                </button>
                <button type="button" class="btn btn-sm btn-light-primary ml-1" onclick="insertPlaceholder('log_message', ':cf_value')">
                    CF Value
                </button>
                <button type="button" class="btn btn-sm btn-light-primary ml-1" onclick="insertPlaceholder('log_message', ':user_name')">
                    User Name
                </button>
            </label>
            <textarea
                class="form-control form-control-solid @error('log_message') is-invalid @enderror"
                id="log_message" name="log_message" rows="3"
                placeholder="Optional log message">{{ old('log_message', $row->log_message ?? '') }}</textarea>
            @error('log_message')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <span class="form-text text-muted">Optional log message for this custom field</span>
        </div>
        <!--end::Form Group-->
    </div>
</div>

@if(isset($row) && $row->id)
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <button type="button" class="btn btn-primary" id="btn-log-messages" data-custom-field-id="{{ $row->id }}">
                <i class="la la-comment"></i>
                Log messages
            </button>
        </div>
    </div>
</div>
@endif

<!-- Log Messages Modal -->
<div class="modal fade" id="logMessagesModal" tabindex="-1" role="dialog" aria-labelledby="logMessagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logMessagesModalLabel">Manage Log Messages by Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="log-messages-loader" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading log messages...</p>
                </div>
                <div id="log-messages-save-loader" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Saving...</span>
                    </div>
                    <p class="mt-2">Saving log messages...</p>
                </div>
                <div id="log-messages-content">
                    <div id="log-messages-repeater" class="mb-3">
                        <!-- Repeater rows will be added here -->
                    </div>
                    <div class="form-group mb-0">
                        <button type="button" class="btn btn-success" id="btn-add-log-message-row">
                            <i class="la la-plus"></i> Add New Row
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-save-log-messages">
                    <span id="save-text">Save</span>
                </button>
            </div>
        </div>
    </div>
</div>


@push('css')
<style>
    /* Clean Modal Styles */
    #logMessagesModal .modal-body {
        padding: 1.5rem;
    }
    
    #logMessagesModal .log-message-row {
        transition: all 0.2s ease;
        padding-right: 3rem !important; /* Make room for X button */
    }
    
    #logMessagesModal .log-message-row:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
    }
    
    #logMessagesModal .form-group {
        margin-bottom: 1rem;
    }
    
    #logMessagesModal .form-group label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #495057;
    }
    
    #logMessagesModal .log-message-textarea {
        resize: vertical;
        min-height: 80px;
    }
    
    #logMessagesModal .btn-delete-log-message-row {
        width: 28px;
        height: 28px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    
    #logMessagesModal .btn-delete-log-message-row i {
        font-size: 1rem;
    }
</style>
@endpush

@push('script')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            // Check if jQuery is available
            if (typeof $ === 'undefined') {
                return;
            }

            // Auto-generate field name from label
            $('#label').on('input', function () {
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
            $('#name').on('input', function () {
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

                switch (selectedType) {
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
                    success: function (response) {
                        if (response.success && response.data) {
                            var options = '';

                            if (selectType === 'select') {
                                options += '<option value="">Select default option</option>';
                            }

                            // For multiselect, split current value by comma if it exists
                            var selectedValues = [];
                            if (currentValue && selectType === 'multiselect') {
                                selectedValues = currentValue.split(',').map(function (val) {
                                    return val.trim();
                                });
                            }

                            response.data.forEach(function (item) {
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
                    error: function (xhr) {
                        defaultValueSelect.html('<option value="">Error loading options</option>');
                    }
                });
            }

            // Initialize default value input on page load
            setTimeout(function () {
                updateDefaultValueInput();

                // If we're in edit mode and have a related table for select/multiselect, load options
                var initialType = $('#type').val();
                var initialTable = $('#related_table').val();
                if ((initialType === 'select' || initialType === 'multiselect') && initialTable) {
                    setTimeout(function () {
                        loadTableOptions(initialType);
                    }, 200);
                }
            }, 100);

            // Update default value input when type changes
            $(document).on('change', '#type', function () {
                updateDefaultValueInput();
            });

            // Update default value options when related table changes
            $(document).on('input blur', '#related_table', function () {
                var selectedType = $('#type').val();
                if (selectedType === 'select' || selectedType === 'multiselect') {
                    loadTableOptions(selectedType);
                }
            });
        });

        // Function to insert placeholder at cursor position in textarea (for modal)
        function insertPlaceholderInModal(textareaId, placeholder) {
            var textarea = document.getElementById(textareaId);
            if (!textarea) return;
            
            var start = textarea.selectionStart;
            var end = textarea.selectionEnd;
            var text = textarea.value;
            
            // Insert placeholder at cursor position
            textarea.value = text.substring(0, start) + placeholder + text.substring(end);
            
            // Set cursor position after inserted text
            var newPosition = start + placeholder.length;
            textarea.setSelectionRange(newPosition, newPosition);
            
            // Focus the textarea
            textarea.focus();
        }

        // Function to insert placeholder at cursor position in textarea (for main form)
        function insertPlaceholder(textareaId, placeholder) {
            var textarea = document.getElementById(textareaId);
            if (!textarea) return;
            
            var start = textarea.selectionStart;
            var end = textarea.selectionEnd;
            var text = textarea.value;
            
            // Insert placeholder at cursor position
            textarea.value = text.substring(0, start) + placeholder + text.substring(end);
            
            // Set cursor position after inserted text
            var newPosition = start + placeholder.length;
            textarea.setSelectionRange(newPosition, newPosition);
            
            // Focus the textarea
            textarea.focus();
        }

        // Log Messages Modal Functionality
        var logMessageRowIndex = 0;
        var allStatuses = [];
        var customFieldId = null;

        // Initialize log messages functionality
        $(document).ready(function() {
            // Get custom field ID from button
            var $btnLogMessages = $('#btn-log-messages');
            if ($btnLogMessages.length) {
                customFieldId = $btnLogMessages.data('custom-field-id');
                
                // Load statuses on page load
                loadStatuses();
            }

            // Open modal button click
            $(document).on('click', '#btn-log-messages', function() {
                customFieldId = $(this).data('custom-field-id');
                if (!customFieldId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Custom field ID is required. Please save the custom field first.'
                    });
                    return;
                }
                openLogMessagesModal();
            });

            // Add new row button
            $(document).on('click', '#btn-add-log-message-row', function() {
                addLogMessageRow();
            });

            // Delete row button
            $(document).on('click', '.btn-delete-log-message-row', function() {
                $(this).closest('.log-message-row').remove();
            });

            // Save log messages
            $(document).on('click', '#btn-save-log-messages', function() {
                saveLogMessages();
            });

            // Initialize select2 when modal is shown
            $('#logMessagesModal').on('shown.bs.modal', function() {
                $('.log-message-status-select').select2({
                    dropdownParent: $('#logMessagesModal'),
                    width: '100%'
                });
            });
        });

        function loadStatuses() {
            $.ajax({
                url: '{{ route("custom-fields.log-messages.statuses") }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        allStatuses = response.data;
                    }
                },
                error: function() {
                    console.error('Failed to load statuses');
                }
            });
        }

        function openLogMessagesModal() {
            $('#logMessagesModal').modal('show');
            $('#log-messages-repeater').empty();
            $('#log-messages-content').hide();
            $('#log-messages-loader').show();

            // Load existing log messages
            $.ajax({
                url: '{{ route("custom-fields.log-messages.index", ":id") }}'.replace(':id', customFieldId),
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#log-messages-loader').hide();
                    $('#log-messages-content').show();

                    if (response.success && response.data.length > 0) {
                        // Load existing rows
                        response.data.forEach(function(item) {
                            addLogMessageRow(item.status_id, item.log_message);
                        });
                    } else {
                        // Add one empty row
                        addLogMessageRow();
                    }
                },
                error: function(xhr) {
                    $('#log-messages-loader').hide();
                    $('#log-messages-content').show();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load log messages. Please try again.'
                    });
                    addLogMessageRow();
                }
            });
        }

        function addLogMessageRow(statusId = '', logMessage = '') {
            logMessageRowIndex++;
            var rowHtml = `
                <div class="log-message-row border border-left border-secondary border-left-3 rounded p-3 mb-3 bg-light shadow-sm position-relative" data-row-index="${logMessageRowIndex}">
                    <button type="button" class="btn btn-sm btn-icon btn-danger btn-delete-log-message-row position-absolute" style="top: 0.5rem; right: 0.5rem; z-index: 10;" title="Delete this row">
                        <i class="la la-times"></i>
                    </button>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark mb-2">Status <span class="text-danger">*</span></label>
                                <select class="form-control log-message-status-select kt-select2" 
                                        name="statuses[${logMessageRowIndex}][status_id]" 
                                        data-placeholder="Select Status" required>
                                    <option value="">Select Status</option>
                                    ${allStatuses.map(function(status) {
                                        return `<option value="${status.id}" ${status.id == statusId ? 'selected' : ''}>${status.name}</option>`;
                                    }).join('')}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark mb-2">
                                    Log Message <span class="text-danger">*</span>
                                </label>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-light-primary mr-1 mb-1" onclick="insertPlaceholderInModal('log_message_${logMessageRowIndex}', ':cf_label')">
                                        <i class="la la-tag"></i> CF Label
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light-primary mr-1 mb-1" onclick="insertPlaceholderInModal('log_message_${logMessageRowIndex}', ':cf_value')">
                                        <i class="la la-tag"></i> CF Value
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light-primary mr-1 mb-1" onclick="insertPlaceholderInModal('log_message_${logMessageRowIndex}', ':user_name')">
                                        <i class="la la-user"></i> User Name
                                    </button>
                                </div>
                                <textarea class="form-control log-message-textarea" 
                                          id="log_message_${logMessageRowIndex}"
                                          name="statuses[${logMessageRowIndex}][log_message]" 
                                          rows="3" 
                                          placeholder="Enter log message" 
                                          required>${logMessage || ''}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#log-messages-repeater').append(rowHtml);
            
            // Initialize select2 for the new select
            $('#logMessagesModal').find('.log-message-status-select').last().select2({
                dropdownParent: $('#logMessagesModal'),
                width: '100%'
            });
        }

        function saveLogMessages() {
            var $saveBtn = $('#btn-save-log-messages');
            
            // Collect data from all rows
            var statuses = [];
            var hasErrors = false;
            var errorMessages = [];
            
            $('.log-message-row').each(function() {
                var $row = $(this);
                var $statusSelect = $row.find('.log-message-status-select');
                var $logTextarea = $row.find('.log-message-textarea');
                var statusId = $statusSelect.val();
                var logMessage = $logTextarea.val().trim();
                
                // Reset validation classes
                $statusSelect.removeClass('is-invalid');
                $logTextarea.removeClass('is-invalid');
                
                // Validate: if log message is filled, status must be selected
                if (logMessage && !statusId) {
                    $statusSelect.addClass('is-invalid');
                    $logTextarea.addClass('is-invalid');
                    hasErrors = true;
                    if (errorMessages.indexOf('Status is required when log message is provided.') === -1) {
                        errorMessages.push('Status is required when log message is provided.');
                    }
                }
                
                // Validate: if status is selected, log message must be filled
                if (statusId && !logMessage) {
                    $logTextarea.addClass('is-invalid');
                    hasErrors = true;
                    if (errorMessages.indexOf('Log message is required when status is selected.') === -1) {
                        errorMessages.push('Log message is required when status is selected.');
                    }
                }
                
                // Only add to statuses array if both are valid
                if (statusId && logMessage) {
                    statuses.push({
                        status_id: statusId,
                        log_message: logMessage
                    });
                }
            });

            if (hasErrors) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: errorMessages.join('\n')
                });
                return;
            }

            // Show loader on modal
            $('#log-messages-content').hide();
            $('#log-messages-save-loader').show();
            $saveBtn.prop('disabled', true);

            // Send AJAX request
            $.ajax({
                url: '{{ route("custom-fields.log-messages.store", ":id") }}'.replace(':id', customFieldId),
                type: 'POST',
                data: {
                    statuses: statuses,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Show success alert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Log messages saved successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function() {
                            // Close modal
                            $('#logMessagesModal').modal('hide');
                            
                            // Reset button
                            $saveBtn.prop('disabled', false);
                            $('#log-messages-save-loader').hide();
                            $('#log-messages-content').show();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to save log messages.'
                        });
                        $saveBtn.prop('disabled', false);
                        $('#log-messages-save-loader').hide();
                        $('#log-messages-content').show();
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Failed to save log messages.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = [];
                        for (var key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                errorMessages.push(errors[key].join(', '));
                            }
                        }
                        errorMessage = 'Validation errors:\n' + errorMessages.join('\n');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                    $saveBtn.prop('disabled', false);
                    $('#log-messages-save-loader').hide();
                    $('#log-messages-content').show();
                }
            });
        }
    </script>
@endpush
<!--end::Form-->
