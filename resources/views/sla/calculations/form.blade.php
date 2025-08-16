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
        <label for="sla_time">SLA Time:</label>
        <input 
            type="number" 
            id="sla_time"
            name="sla_time" 
            class="form-control" 
            placeholder="Enter SLA Time" 
            value="{{ isset($row) ? $row->sla_time : old('sla_time') }}" 
        />
        {!! $errors->first('sla_time', '<span class="form-control-feedback">:message</span>') !!}
    </div>

    {{-- SLA Type Dropdown --}}
    <div class="form-group">
        <label for="sla_type">SLA Type:</label>
        <select id="sla_type" name="type" class="form-control">
            <option value="">-- Select Type --</option>
            <option value="day" {{ (isset($row) && $row->type == 'day') || old('type') == 'day' ? 'selected' : '' }}>Day</option>
            <option value="hour" {{ (isset($row) && $row->type == 'hour') || old('type') == 'hour' ? 'selected' : '' }}>Hour</option>
        </select>
        {!! $errors->first('sla_type', '<span class="form-control-feedback">:message</span>') !!}
    </div>

</div>
