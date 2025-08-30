<div class="card-body">

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
            <option value="">-- Select Status --</option>
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
    <div class="form-group">
        <label for="group_id">Group:</label>
        <select id="group_id" name="group_id" class="form-control">
            <option value="">-- Select Group --</option>
            @foreach($groups as $group)
                <option value="{{ $group->id }}" 
                    {{ (isset($row) && $row->group_id == $group->id) || old('group_id') == $group->id ? 'selected' : '' }}>
                    {{ $group->name }}
                </option>
            @endforeach
        </select>
        {!! $errors->first('group_id', '<span class="form-control-feedback">:message</span>') !!}
    </div>

    {{-- SLA Time Input --}}
    <div class="form-group">
        <label for="sla_time">SLA For Unit:</label>
        <input 
            type="number" 
            id="unit_sla_time"
            name="unit_sla_time" 
            class="form-control" 
            placeholder="SLA For Unit" 
            value="{{ isset($row) ? $row->unit_sla_time : old('sla_time') }}" 
        />
        {!! $errors->first('sla_time', '<span class="form-control-feedback">:message</span>') !!}
    </div>

    {{-- SLA Type Dropdown --}}
    <div class="form-group">
        <label for="sla_type">SLA Type For Unit:</label>
        <select id="sla_type" name="sla_type_unit" class="form-control">
            <option value="">-- Select Type --</option>
            <option value="day" {{ (isset($row) && $row->sla_type_unit == 'day') || old('type') == 'day' ? 'selected' : '' }}>Day</option>
            <option value="hour" {{ (isset($row) && $row->sla_type_unit == 'hour') || old('type') == 'hour' ? 'selected' : '' }}>Hour</option>
        </select>
        {!! $errors->first('sla_type', '<span class="form-control-feedback">:message</span>') !!}
    </div>

        <div class="form-group">
        <label for="sla_time">SLA For Division:</label>
        <input 
            type="number" 
            id="division_sla_time"
            name="division_sla_time" 
            class="form-control" 
            placeholder="SLA For Division" 
            value="{{ isset($row) ? $row->division_sla_time : old('sla_time') }}" 
        />
        {!! $errors->first('sla_time', '<span class="form-control-feedback">:message</span>') !!}
    </div>

     {{-- SLA Type Dropdown --}}
    <div class="form-group">
        <label for="sla_type">SLA Type For Division:</label>
        <select id="sla_type" name="sla_type_division" class="form-control">
            <option value="">-- Select Type --</option>
            <option value="day" {{ (isset($row) && $row->sla_type_division == 'day') || old('type') == 'day' ? 'selected' : '' }}>Day</option>
            <option value="hour" {{ (isset($row) && $row->sla_type_division == 'hour') || old('type') == 'hour' ? 'selected' : '' }}>Hour</option>
        </select>
        {!! $errors->first('sla_type', '<span class="form-control-feedback">:message</span>') !!}
    </div>

        <div class="form-group">
        <label for="sla_time">SLA For Director:</label>
        <input 
            type="number" 
            id="director_sla_time"
            name="director_sla_time" 
            class="form-control" 
            placeholder="SLA For Director" 
            value="{{ isset($row) ? $row->director_sla_time : old('sla_time') }}" 
        />
        {!! $errors->first('sla_time', '<span class="form-control-feedback">:message</span>') !!}
    </div>


    {{-- SLA Type Dropdown --}}
    <div class="form-group">
        <label for="sla_type">SLA Type For Director:</label>
        <select id="sla_type" name="sla_type_director" class="form-control">
            <option value="">-- Select Type --</option>
            <option value="day" {{ (isset($row) && $row->sla_type_director == 'day') || old('type') == 'day' ? 'selected' : '' }}>Day</option>
            <option value="hour" {{ (isset($row) && $row->sla_type_director == 'hour') || old('type') == 'hour' ? 'selected' : '' }}>Hour</option>
        </select>
        {!! $errors->first('sla_type', '<span class="form-control-feedback">:message</span>') !!}
    </div>

    

</div>
