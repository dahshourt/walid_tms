<div class="card-body">

    @if($errors->any())
        <div class="m-alert m-alert--icon alert alert-danger" role="alert" id="m_form_1_msg">
            <div class="m-alert__icon">
                <i class="la la-warning"></i>
            </div>
            <div class="m-alert__text">
                There are some errors
            </div>
            <div class="m-alert__close">
                <button type="button" class="close" data-close="alert" aria-label="Close">
                </button>
            </div>
        </div>
    @endif

    <div class="form-group form-group-last"></div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="workflow_type_id">Workflow Type</label>
            <select class="form-control form-control-lg" id="workflow_type_id" name="workflow_type_id">
                <option value=""> Select</option>
                @foreach($types as $item)
                    <option
                        value="{{ $item->id }}" {{ isset($row) && $row->workflow_type_id == $item->id ? "selected" : "" }}> {{ $item->name }} </option>
                @endforeach
            </select>
            {!! $errors->first('workflow_type_id', '<span class="form-control-feedback">:message</span>') !!}
        </div>

        <div class="form-group col-md-6">
            <label for="stage_id">Stage <span class="text-danger">*</span></label>
            <select class="form-control form-control-lg" id="stage_id" name="stage_id">
                <option value=""> Select</option>
                @foreach($stages as $item)
                    <option
                        value="{{ $item->id }}" {{ isset($row) && $row->stage_id == $item->id ? "selected" : "" }}> {{ $item->name }} </option>
                @endforeach
            </select>
            {!! $errors->first('stage_id', '<span class="form-control-feedback">:message</span>') !!}
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="set_group_id">Set by Groups <span class="text-danger">*</span></label>
            <select class="form-control form-control-lg" id="set_group_id" name="set_group_id[]" multiple="multiple">
                <option value=""> Select</option>
                @foreach($groups as $item)
                    <option value="{{ $item->id }}" {{ in_array($item->id, $set_group_ids ?? []) ? "selected" : "" }}>
                        {{ $item->title }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('set_group_id', '<span class="form-control-feedback">:message</span>') !!}
        </div>

        <div class="form-group col-md-6">
            <label for="view_group_id">View by groups:</label>
            <select class="form-control form-control-lg" id="view_group_id" name="view_group_id[]" multiple="multiple">
                <option value=""> Select</option>
                @foreach($groups as $item)
                    <option value="{{ $item->id }}" {{ in_array($item->id, $view_group_ids ?? []) ? "selected" : "" }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('view_group_id', '<span class="form-control-feedback">:message</span>') !!}
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Status Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-lg" placeholder="Status Name" name="status_name"
                   value="{{ isset($row) ? $row->status_name : old('status_name') }}"/>
            {!! $errors->first('status_name', '<span class="form-control-feedback">:message</span>') !!}
        </div>

        <div class="form-group col-md-6">
            <label>
                Status SLA <span class="text-danger">*</span>
                <span class="hint text-primary ml-2">Hint : number values in days</span>
            </label>
            <input type="text" class="form-control form-control-lg" placeholder="Status SLA" name="sla"
                   value="{{ isset($row) ? $row->sla : old('sla') }}"/>
            {!! $errors->first('sla', '<span class="form-control-feedback">:message</span>') !!}
        </div>
    </div>

    <div class="form-group">
        <label for="log_message">Log Message</label>
        <textarea class="form-control form-control-lg" id="log_message" name="log_message" rows="3"
                  placeholder="Optional log message">{{ isset($row) ? $row->log_message : old('log_message') }}</textarea>
        {!! $errors->first('log_message', '<span class="form-control-feedback">:message</span>') !!}
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Technical Team View Flag?</label>
            <div class="checkbox-inline mt-2">
                <label class="checkbox">
                    <input type="hidden" name="view_technical_team_flag" value="0">
                    <input type="checkbox" name="view_technical_team_flag" value="1"
                        {{ isset($row) && $row->view_technical_team_flag == 1 ? "checked" : "" }}>
                    <span></span>Yes
                </label>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label>Active</label>
            <div class="checkbox-inline mt-2">
                <label class="checkbox">
                    <input type="checkbox" name="active"
                           value="1" {{ isset($row) && $row->active == 1 ? "checked" : "" }}>
                    <span></span>Yes</label>
            </div>
        </div>
    </div>

</div>
