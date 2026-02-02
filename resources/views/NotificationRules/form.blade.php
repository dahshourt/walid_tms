<div class="card-body">

@if($errors->any())
    <div class="m-alert m-alert--icon alert alert-danger" role="alert" id="m_form_1_msg">
        <div class="m-alert__icon">
            <i class="la la-warning"></i>
        </div>
        <div class="m-alert__text">
            <strong>There are some errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <div class="m-alert__close">
            <button type="button" class="close" data-close="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

{{-- Basic Information Section --}}
<div class="card card-custom card-border mb-5">
    <div class="card-header">
        <div class="card-title">
            <h3 class="card-label">
                <i class="text-primary"></i>
                Basic Information
            </h3>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Rule Name <span class="text-danger">*</span></label>
                    <input class="form-control form-control-lg" id="name" name="name" 
                           value="{{ isset($row) ? $row->name : old('name') }}" 
                           placeholder="Enter rule name" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="event_class">Event Class <span class="text-danger">*</span></label>
                    <select class="form-control form-control-lg" id="event_class" name="event_class" required>
                        <option value="">-- Select Event --</option>
                        @foreach($eventClasses as $value => $label)
                            <option value="{{ $value }}" 
                                {{ (isset($row) && $row->event_class == $value) || old('event_class') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="template_id">Email Template <span class="text-danger">*</span></label>
                    <select class="form-control form-control-lg" id="template_id" name="template_id" required>
                        <option value="">-- Select Template --</option>
                        @foreach($templates as $id => $name)
                            <option value="{{ $id }}" 
                                {{ (isset($row) && $row->template_id == $id) || old('template_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="priority">Priority</label>
                    <input type="number" class="form-control form-control-lg" id="priority" name="priority" 
                           value="{{ isset($row) ? $row->priority : (old('priority') ?? 0) }}" 
                           min="0" placeholder="0">
                    <small class="form-text text-muted">Higher priority rules are processed first</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Status</label>
                    <div class="checkbox-inline mt-3">
                        <label class="checkbox checkbox-success checkbox-lg">
                            <input type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ (isset($row) && $row->is_active) || (!isset($row) && old('is_active', true)) ? 'checked' : '' }}>
                            <span></span>
                            Active
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Conditions Section --}}
<div class="card card-custom card-border mb-5">
    <div class="card-header">
        <div class="card-title">
            <h3 class="card-label">
                <i class="text-warning"></i>
                Conditions
            </h3>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-primary btn-sm" id="addCondition">
                <i class="la la-plus"></i> Add Condition
            </button>
        </div>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">
            <i class="text-primary"></i>
            Define when this rule should apply. All conditions must be met (AND logic).
        </p>
        
        <div id="conditionsContainer">
            @php
                $existingConditions = [];
                if (isset($row) && $row->conditions && is_array($row->conditions)) {
                    foreach ($row->conditions as $type => $value) {
                        if ($type === 'custom_field' && is_array($value)) {
                            $existingConditions[] = [
                                'type' => 'custom_field',
                                'custom_field_name' => $value['name'] ?? '',
                                'custom_field_value' => $value['value'] ?? ''
                            ];
                        } else {
                            $existingConditions[] = [
                                'type' => $type,
                                'value' => $value
                            ];
                        }
                    }
                }
            @endphp
            
            @if(count($existingConditions) > 0)
                @foreach($existingConditions as $index => $cond)
                    <div class="condition-row border rounded p-3 mb-3" data-index="{{ $index }}">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label>Condition Type <span class="text-danger">*</span></label>
                                    <select class="form-control condition-type-select" name="conditions[{{ $index }}][type]" required>
                                        <option value="">-- Select Type --</option>
                                        @foreach($conditionTypes as $value => $label)
                                            <option value="{{ $value }}" 
                                                data-source="{{ in_array($value, ['workflow_type', 'workflow_type_not']) ? 'workflow' : (in_array($value, ['new_status_id', 'old_status_id']) ? 'status' : 'custom') }}"
                                                {{ $cond['type'] == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 condition-value-col" style="{{ $cond['type'] !== 'custom_field' ? '' : 'display:none;' }}">
                                <div class="form-group mb-0">
                                    <label>Condition Value <span class="text-danger">*</span></label>
                                    <select class="form-control condition-value-select" name="conditions[{{ $index }}][value]">
                                        <option value="">-- Select Value --</option>
                                        @if(in_array($cond['type'], ['workflow_type', 'workflow_type_not']))
                                            @foreach($workflowTypes as $id => $name)
                                                <option value="{{ $id }}" {{ ($cond['value'] ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        @elseif(in_array($cond['type'], ['new_status_id', 'old_status_id']))
                                            @foreach($statuses as $id => $name)
                                                <option value="{{ $id }}" {{ ($cond['value'] ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 custom-field-name-col" style="{{ $cond['type'] === 'custom_field' ? '' : 'display:none;' }}">
                                <div class="form-group mb-0">
                                    <label>Field Name <span class="text-danger">*</span></label>
                                    <select class="form-control custom-field-name" name="conditions[{{ $index }}][custom_field_name]">
                                        <option value="">-- Select Field --</option>
                                        @foreach($customFields as $fieldName => $fieldLabel)
                                            <option value="{{ $fieldName }}" {{ ($cond['custom_field_name'] ?? '') == $fieldName ? 'selected' : '' }}>{{ $fieldLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 custom-field-value-col" style="{{ $cond['type'] === 'custom_field' ? '' : 'display:none;' }}">
                                <div class="form-group mb-0">
                                    <label>Expected Value <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control custom-field-value" 
                                           name="conditions[{{ $index }}][custom_field_value]" 
                                           value="{{ $cond['custom_field_value'] ?? '' }}"
                                           placeholder="e.g. 1">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-light-danger btn-icon remove-condition" title="Remove Condition">
                                    <i class="la la-trash-o"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        
        <div id="noConditionsMessage" class="text-center text-muted py-4" style="{{ count($existingConditions) > 0 ? 'display:none;' : '' }}">
            <i class="flaticon-questions-circular-button icon-3x text-muted mb-3"></i>
            <p class="mb-0">No conditions added. This rule will always execute when the event fires.</p>
            <p class="mb-0">Click "Add Condition" to add filtering conditions.</p>
        </div>
    </div>
</div>

{{-- Recipients Section --}}
<div class="card card-custom card-border mb-5">
    <div class="card-header">
        <div class="card-title">
            <h3 class="card-label">
                <i class="text-success"></i>
                Recipients
            </h3>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-primary btn-sm" id="addRecipient">
                <i class="la la-plus"></i> Add Recipient
            </button>
        </div>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">
            <i class="text-primary"></i>
            Define who should receive this notification. At least one TO recipient is recommended.
        </p>
        
        <div id="recipientsContainer">
            @if(isset($row) && $row->recipients && count($row->recipients) > 0)
                @foreach($row->recipients as $index => $recipient)
                    <div class="recipient-row border rounded p-3 mb-3" data-index="{{ $index }}">
                        <div class="row align-items-end">
                            <div class="col-md-2">
                                <div class="form-group mb-0">
                                    <label>Channel <span class="text-danger">*</span></label>
                                    <select class="form-control" name="recipients[{{ $index }}][channel]" required>
                                        @foreach($channels as $value => $label)
                                            <option value="{{ $value }}" {{ $recipient->channel == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label>Recipient Type <span class="text-danger">*</span></label>
                                    <select class="form-control recipient-type-select" name="recipients[{{ $index }}][recipient_type]" required>
                                        <option value="">-- Select Type --</option>
                                        @foreach($recipientTypes as $category => $types)
                                            <optgroup label="{{ $category }}">
                                                @foreach($types as $type)
                                                    <option value="{{ $type['value'] }}" 
                                                            data-needs-identifier="{{ $type['needs_identifier'] ? 'true' : 'false' }}"
                                                            data-identifier-type="{{ $type['identifier_type'] ?? '' }}"
                                                            {{ $recipient->recipient_type == $type['value'] ? 'selected' : '' }}>
                                                        {{ $type['label'] }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0 identifier-group" 
                                     style="{{ in_array($recipient->recipient_type, ['static_email', 'user', 'group']) ? '' : 'display:none;' }}">
                                    <label class="identifier-label">Identifier</label>
                                    @php
                                        $identifierType = '';
                                        $recipientConfig = collect(config('notification_recipient_types'))->firstWhere('value', $recipient->recipient_type);
                                        if ($recipientConfig) {
                                            $identifierType = $recipientConfig['identifier_type'] ?? '';
                                        }
                                    @endphp
                                    @if($identifierType == 'email')
                                        <input type="email" class="form-control identifier-input" 
                                               name="recipients[{{ $index }}][recipient_identifier]" 
                                               value="{{ $recipient->recipient_identifier }}"
                                               placeholder="email@example.com">
                                    @elseif($identifierType == 'user_id')
                                        <select class="form-control identifier-select identifier-user" name="recipients[{{ $index }}][recipient_identifier]">
                                            <option value="">-- Select User --</option>
                                            @foreach($users as $id => $name)
                                                <option value="{{ $id }}" {{ $recipient->recipient_identifier == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif($identifierType == 'group_id')
                                        <select class="form-control identifier-select identifier-group" name="recipients[{{ $index }}][recipient_identifier]">
                                            <option value="">-- Select Group --</option>
                                            @foreach($groups as $id => $name)
                                                <option value="{{ $id }}" {{ $recipient->recipient_identifier == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" class="form-control identifier-input" 
                                               name="recipients[{{ $index }}][recipient_identifier]" 
                                               value="{{ $recipient->recipient_identifier }}"
                                               placeholder="Not required for this type" disabled>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-light-danger btn-icon remove-recipient" title="Remove Recipient">
                                    <i class="la la-trash-o"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Default empty recipient row --}}
                <div class="recipient-row border rounded p-3 mb-3" data-index="0">
                    <div class="row align-items-end">
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label>Channel <span class="text-danger">*</span></label>
                                <select class="form-control" name="recipients[0][channel]" required>
                                    @foreach($channels as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label>Recipient Type <span class="text-danger">*</span></label>
                                <select class="form-control recipient-type-select" name="recipients[0][recipient_type]" required>
                                    <option value="">-- Select Type --</option>
                                    @foreach($recipientTypes as $category => $types)
                                        <optgroup label="{{ $category }}">
                                            @foreach($types as $type)
                                                <option value="{{ $type['value'] }}" 
                                                        data-needs-identifier="{{ $type['needs_identifier'] ? 'true' : 'false' }}"
                                                        data-identifier-type="{{ $type['identifier_type'] ?? '' }}">
                                                    {{ $type['label'] }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0 identifier-group" style="display:none;">
                                <label class="identifier-label">Identifier</label>
                                <input type="text" class="form-control identifier-input" 
                                       name="recipients[0][recipient_identifier]" 
                                       placeholder="Enter identifier">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-light-danger btn-icon remove-recipient" title="Remove Recipient">
                                <i class="la la-trash-o"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div id="noRecipientsMessage" class="text-center text-muted py-4" style="display:none;">
            <i class="flaticon-user-add icon-3x text-muted mb-3"></i>
            <p class="mb-0">No recipients added. Click "Add Recipient" to add one.</p>
        </div>
    </div>
</div>

</div>

@push('script')
<script>
$(document).ready(function() {
    // Data for condition values
    var workflowTypes = @json($workflowTypes);
    var statuses = @json($statuses);
    var customFields = @json($customFields ?? []);
    var users = @json($users);
    var groups = @json($groups);
    
    // Index counters
    var conditionIndex = {{ isset($row) && $row->conditions ? count($row->conditions) : 0 }};
    var recipientIndex = {{ isset($row) && $row->recipients ? count($row->recipients) : 1 }};
    
    // Build condition type options HTML
    function getConditionTypesHtml() {
        var html = '<option value="">-- Select Type --</option>';
        @foreach($conditionTypes as $value => $label)
            html += '<option value="{{ $value }}" data-source="{{ in_array($value, ["workflow_type", "workflow_type_not"]) ? "workflow" : (in_array($value, ["new_status_id", "old_status_id"]) ? "status" : "custom") }}">{{ $label }}</option>';
        @endforeach
        return html;
    }
    
    // Build custom fields options HTML
    function getCustomFieldsHtml() {
        var html = '<option value="">-- Select Field --</option>';
        $.each(customFields, function(name, label) {
            html += '<option value="' + name + '">' + label + '</option>';
        });
        return html;
    }
    
    // Handle condition type change
    $(document).on('change', '.condition-type-select', function() {
        var $row = $(this).closest('.condition-row');
        var selectedOption = $(this).find('option:selected');
        var source = selectedOption.data('source');
        var $valueSelect = $row.find('.condition-value-select');
        var $valueCol = $row.find('.condition-value-col');
        var $customNameCol = $row.find('.custom-field-name-col');
        var $customValueCol = $row.find('.custom-field-value-col');
        
        if (source === 'custom') {
            // Custom field type - show custom field inputs, hide value dropdown
            $valueCol.hide();
            $customNameCol.show();
            $customValueCol.show();
        } else {
            // Standard condition types
            $valueCol.show();
            $customNameCol.hide();
            $customValueCol.hide();
            
            var options = '<option value="">-- Select Value --</option>';
            
            if (source === 'workflow') {
                $.each(workflowTypes, function(id, name) {
                    options += '<option value="' + id + '">' + name + '</option>';
                });
            } else if (source === 'status') {
                $.each(statuses, function(id, name) {
                    options += '<option value="' + id + '">' + name + '</option>';
                });
            }
            
            $valueSelect.html(options);
        }
    });
    
    // Add condition row
    $('#addCondition').click(function() {
        var newRow = '<div class="condition-row border rounded p-3 mb-3" data-index="' + conditionIndex + '">' +
            '<div class="row align-items-end">' +
                '<div class="col-md-3">' +
                    '<div class="form-group mb-0">' +
                        '<label>Condition Type <span class="text-danger">*</span></label>' +
                        '<select class="form-control condition-type-select" name="conditions[' + conditionIndex + '][type]" required>' +
                            getConditionTypesHtml() +
                        '</select>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-3 condition-value-col">' +
                    '<div class="form-group mb-0">' +
                        '<label>Condition Value <span class="text-danger">*</span></label>' +
                        '<select class="form-control condition-value-select" name="conditions[' + conditionIndex + '][value]">' +
                            '<option value="">-- Select Type First --</option>' +
                        '</select>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-3 custom-field-name-col" style="display:none;">' +
                    '<div class="form-group mb-0">' +
                        '<label>Field Name <span class="text-danger">*</span></label>' +
                        '<select class="form-control custom-field-name" name="conditions[' + conditionIndex + '][custom_field_name]">' +
                            getCustomFieldsHtml() +
                        '</select>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-2 custom-field-value-col" style="display:none;">' +
                    '<div class="form-group mb-0">' +
                        '<label>Expected Value <span class="text-danger">*</span></label>' +
                        '<input type="text" class="form-control custom-field-value" name="conditions[' + conditionIndex + '][custom_field_value]" placeholder="e.g. 1">' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-1">' +
                    '<button type="button" class="btn btn-light-danger btn-icon remove-condition" title="Remove Condition">' +
                        '<i class="la la-trash-o"></i>' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        $('#noConditionsMessage').hide();
        $('#conditionsContainer').append(newRow);
        conditionIndex++;
    });
    
    // Remove condition row
    $(document).on('click', '.remove-condition', function() {
        $(this).closest('.condition-row').remove();
        
        if ($('.condition-row').length === 0) {
            $('#noConditionsMessage').show();
        }
    });
    
    // Handle recipient type change
    $(document).on('change', '.recipient-type-select', function() {
        var $row = $(this).closest('.recipient-row');
        var selectedOption = $(this).find('option:selected');
        var needsIdentifier = selectedOption.data('needs-identifier') === true || selectedOption.data('needs-identifier') === 'true';
        var identifierType = selectedOption.data('identifier-type');
        var $identifierGroup = $row.find('.identifier-group');
        var index = $row.data('index');
        
        if (needsIdentifier) {
            var inputHtml = '';
            if (identifierType === 'email') {
                inputHtml = '<label class="identifier-label">Email Address <span class="text-danger">*</span></label>' +
                            '<input type="email" class="form-control identifier-input" ' +
                            'name="recipients[' + index + '][recipient_identifier]" ' +
                            'placeholder="email@example.com" required>';
            } else if (identifierType === 'user_id') {
                inputHtml = '<label class="identifier-label">Select User <span class="text-danger">*</span></label>' +
                            '<select class="form-control identifier-select" name="recipients[' + index + '][recipient_identifier]" required>' +
                            '<option value="">-- Select User --</option>';
                $.each(users, function(id, name) {
                    inputHtml += '<option value="' + id + '">' + name + '</option>';
                });
                inputHtml += '</select>';
            } else if (identifierType === 'group_id') {
                inputHtml = '<label class="identifier-label">Select Group <span class="text-danger">*</span></label>' +
                            '<select class="form-control identifier-select" name="recipients[' + index + '][recipient_identifier]" required>' +
                            '<option value="">-- Select Group --</option>';
                $.each(groups, function(id, name) {
                    inputHtml += '<option value="' + id + '">' + name + '</option>';
                });
                inputHtml += '</select>';
            }
            $identifierGroup.html(inputHtml).show();
        } else {
            $identifierGroup.hide().html(
                '<label class="identifier-label">Identifier</label>' +
                '<input type="hidden" name="recipients[' + index + '][recipient_identifier]" value="">'
            );
        }
    });
    
    // Add recipient row
    $('#addRecipient').click(function() {
        var channelsHtml = '';
        @foreach($channels as $value => $label)
            channelsHtml += '<option value="{{ $value }}">{{ $label }}</option>';
        @endforeach
        
        var typesHtml = '<option value="">-- Select Type --</option>';
        @foreach($recipientTypes as $category => $types)
            typesHtml += '<optgroup label="{{ $category }}">';
            @foreach($types as $type)
                typesHtml += '<option value="{{ $type['value'] }}" data-needs-identifier="{{ $type['needs_identifier'] ? 'true' : 'false' }}" data-identifier-type="{{ $type['identifier_type'] ?? '' }}">{{ $type['label'] }}</option>';
            @endforeach
            typesHtml += '</optgroup>';
        @endforeach
        
        var newRow = '<div class="recipient-row border rounded p-3 mb-3" data-index="' + recipientIndex + '">' +
            '<div class="row align-items-end">' +
                '<div class="col-md-2">' +
                    '<div class="form-group mb-0">' +
                        '<label>Channel <span class="text-danger">*</span></label>' +
                        '<select class="form-control" name="recipients[' + recipientIndex + '][channel]" required>' +
                            channelsHtml +
                        '</select>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-4">' +
                    '<div class="form-group mb-0">' +
                        '<label>Recipient Type <span class="text-danger">*</span></label>' +
                        '<select class="form-control recipient-type-select" name="recipients[' + recipientIndex + '][recipient_type]" required>' +
                            typesHtml +
                        '</select>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-4">' +
                    '<div class="form-group mb-0 identifier-group" style="display:none;">' +
                        '<label class="identifier-label">Identifier</label>' +
                        '<input type="text" class="form-control identifier-input" name="recipients[' + recipientIndex + '][recipient_identifier]" placeholder="Enter identifier">' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<button type="button" class="btn btn-light-danger btn-icon remove-recipient" title="Remove Recipient">' +
                        '<i class="la la-trash-o"></i>' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        $('#noRecipientsMessage').hide();
        $('#recipientsContainer').append(newRow);
        recipientIndex++;
    });
    
    // Remove recipient row
    $(document).on('click', '.remove-recipient', function() {
        $(this).closest('.recipient-row').remove();
        
        if ($('.recipient-row').length === 0) {
            $('#noRecipientsMessage').show();
        }
    });
    
    // Check initial states
    if ($('.condition-row').length === 0) {
        $('#noConditionsMessage').show();
    }
    if ($('.recipient-row').length === 0) {
        $('#noRecipientsMessage').show();
    }
});
</script>
@endpush
