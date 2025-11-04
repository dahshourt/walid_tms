<div class="card-body">
    {{-- Custom CSS for larger checkboxes --}}
    <style>
        .checkbox-large .form-check-input {
            width: 1.5rem;
            height: 1.5rem;
            margin-top: 0.125rem;
            cursor: pointer;
        }
        .checkbox-large .form-check-label {
            font-size: 1.1rem;
            margin-left: 0.5rem;
            cursor: pointer;
        }
    </style>

    {{-- Error Alert --}}
    @if($errors->any())
        <div class="m-alert m-alert--icon alert alert-danger" role="alert" id="m_form_1_msg">
            <div class="m-alert__icon">
                <i class="la la-warning"></i>
            </div>
            <div class="m-alert__text">
                There are some errors
            </div>
            <div class="m-alert__close">
                <button type="button" class="close" data-close="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    {{-- Status Dropdown --}}
    <div class="form-group">
        <label for="status_id">Status:</label>
        <select id="status_id" name="status_id" class="form-control">
            <option value="">-- Select Status  --</option>
            @foreach($statuses as $status)
                <option value="{{ $status->id }}" 
                    {{ (isset($row) && $row->status_id == $status->id) || old('status_id') == $status->id ? 'selected' : '' }}>
                    {{ $status->name }}
                </option>
            @endforeach
        </select>
        {!! $errors->first('status_id', '<span class="form-control-feedback">:message</span>') !!}
    </div>
    {{-- Groups Dropdown --}}
    <div class="form-group mt-3">
        <label for="group_id">Units:</label>
        <select name="unit_id" id="unit_id" class="form-control">
            <option value="">-- Select Units --</option>
            @if(isset($units) && count($units) > 0)
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" 
                        {{ (isset($row) && $row->unit_id == $unit->id) || old('unit_id') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            @endif
        </select>
        {!! $errors->first('unit_id', '<span class="form-control-feedback text-danger">:message</span>') !!}
    </div>

    {{-- SLA For Unit Section --}}
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label for="unit_sla_time">SLA For Unit:</label>
                <input 
                    type="number" 
                    id="unit_sla_time"
                    name="unit_sla_time" 
                    class="form-control" 
                    placeholder="SLA For Unit" 
                    value="{{ isset($row) ? $row->unit_sla_time : old('unit_sla_time') }}" 
                />
                {!! $errors->first('unit_sla_time', '<span class="form-control-feedback">:message</span>') !!}
            </div>
            <div class="form-group">
                <label for="sla_type_unit">SLA Type For Unit:</label>
                <select id="sla_type_unit" name="sla_type_unit" class="form-control">
                    <option value="">-- Select Type --</option>
                    <option value="day" {{ (isset($row) && $row->sla_type_unit == 'day') || old('sla_type_unit') == 'day' ? 'selected' : '' }}>Day</option>
                    <option value="hour" {{ (isset($row) && $row->sla_type_unit == 'hour') || old('sla_type_unit') == 'hour' ? 'selected' : '' }}>Hour</option>
                </select>
                {!! $errors->first('sla_type_unit', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <div class="form-check checkbox-large">
                <input 
                    type="checkbox" 
                    class="form-check-input" 
                    id="notification_unit" 
                    name="unit_notification" 
                    value="1"
                    {{ (isset($row) && $row->unit_notification) || old('unit_notification') ? 'checked' : '' }}
                >
                <label class="form-check-label" for="notification_unit">
                    Unit Notification
                </label>
            </div>
        </div>
    </div>

    {{-- SLA For Division Section --}}
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label for="division_sla_time">SLA For Division:</label>
                <input 
                    type="number" 
                    id="division_sla_time"
                    name="division_sla_time" 
                    class="form-control" 
                    placeholder="SLA For Division" 
                    value="{{ isset($row) ? $row->division_sla_time : old('division_sla_time') }}" 
                />
                {!! $errors->first('division_sla_time', '<span class="form-control-feedback">:message</span>') !!}
            </div>
            <div class="form-group">
                <label for="sla_type_division">SLA Type For Division:</label>
                <select id="sla_type_division" name="sla_type_division" class="form-control">
                    <option value="">-- Select Type --</option>
                    <option value="day" {{ (isset($row) && $row->sla_type_division == 'day') || old('sla_type_division') == 'day' ? 'selected' : '' }}>Day</option>
                    <option value="hour" {{ (isset($row) && $row->sla_type_division == 'hour') || old('sla_type_division') == 'hour' ? 'selected' : '' }}>Hour</option>
                </select>
                {!! $errors->first('sla_type_division', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <div class="form-check checkbox-large">
                <input 
                    type="checkbox" 
                    class="form-check-input" 
                    id="notification_division" 
                    name="division_notification" 
                    value="1"
                    {{ (isset($row) && $row->division_notification) || old('division_notification') ? 'checked' : '' }}
                >
                <label class="form-check-label" for="notification_division">
                    Division Notification
                </label>
            </div>
        </div>
    </div>

    {{-- SLA For Director Section --}}
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label for="director_sla_time">SLA For Director:</label>
                <input 
                    type="number" 
                    id="director_sla_time"
                    name="director_sla_time" 
                    class="form-control" 
                    placeholder="SLA For Director" 
                    value="{{ isset($row) ? $row->director_sla_time : old('director_sla_time') }}" 
                />
                {!! $errors->first('director_sla_time', '<span class="form-control-feedback">:message</span>') !!}
            </div>
            <div class="form-group">
                <label for="sla_type_director">SLA Type For Director:</label>
                <select id="sla_type_director" name="sla_type_director" class="form-control">
                    <option value="">-- Select Type --</option>
                    <option value="day" {{ (isset($row) && $row->sla_type_director == 'day') || old('sla_type_director') == 'day' ? 'selected' : '' }}>Day</option>
                    <option value="hour" {{ (isset($row) && $row->sla_type_director == 'hour') || old('sla_type_director') == 'hour' ? 'selected' : '' }}>Hour</option>
                </select>
                {!! $errors->first('sla_type_director', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <div class="form-check checkbox-large">
                <input 
                    type="checkbox" 
                    class="form-check-input" 
                    id="notification_director" 
                    name="director_notification" 
                    value="1"
                    {{ (isset($row) && $row->director_notification) || old('director_notification') ? 'checked' : '' }}
                >
                <label class="form-check-label" for="notification_director">
                   Director Notification
                </label>
            </div>
        </div>
    </div>
    
</div>


{{-- JavaScript for Checkbox Hierarchy Logic --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const unitCheckbox = document.getElementById('notification_unit');
    const divisionCheckbox = document.getElementById('notification_division');
    const directorCheckbox = document.getElementById('notification_director');

    // Handle Unit checkbox change
    unitCheckbox.addEventListener('change', function() {
        if (!this.checked) {
            // If Unit is unchecked, uncheck Division and Director
            divisionCheckbox.checked = false;
            directorCheckbox.checked = false;
        }
    });

    // Handle Division checkbox change
    divisionCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // If Division is checked, automatically check Unit
            unitCheckbox.checked = true;
        } else {
            // If Division is unchecked, uncheck Director
            directorCheckbox.checked = false;
        }
    });

    // Handle Director checkbox change
    directorCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // If Director is checked, automatically check Division and Unit
            divisionCheckbox.checked = true;
            unitCheckbox.checked = true;
        }
    });

    // Form submission validation
    const form = unitCheckbox.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Check if Director is checked but Division or Unit are not
            if (directorCheckbox.checked && (!divisionCheckbox.checked || !unitCheckbox.checked)) {
                e.preventDefault();
                alert('Error: If Director Notification is checked, both Division and Unit Notifications must be checked.');
                return false;
            }

            // Check if Division is checked but Unit is not
            if (divisionCheckbox.checked && !unitCheckbox.checked) {
                e.preventDefault();
                alert('Error: If Division Notification is checked, Unit Notification must be checked.');
                return false;
            }
        });
    }
});
</script>