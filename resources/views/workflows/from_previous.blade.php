@if($same_time_from)
    <div class="row">
        <div class="col-md-6">
            <div class="form-group modern-form-group">
                <label for="from_previous_status_id">Previous From Status:
                    <span style="color:blue; font-size: 0.85rem;" class="ml-2">(hint: the text shows previous status ->
                        current
                        status)</span>
                </label>
                <select class="form-control modern-form-control" id="from_previous_status_id" name="from_status_id[]"
                    multiple="multiple">
                    <option value="">Select</option>
                    @foreach($statuses as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->from_status ? $item->from_status->status_name : "" }} ->
                            {{ $item->workflowstatus[0]->to_status->status_name }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('from_previous_status_id', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
    </div>

@else
    <div class="row">
        <div class="col-md-6">
            <div class="form-group modern-form-group">
                <label for="previous_status_id">Previous Status</label>
                <select class="form-control modern-form-control select2" id="previous_status_id" name="previous_status_id">
                    <option value="">Select</option>
                    @foreach($statuses as $item)
                        <option value="{{ $item->id }}" {{ isset($row) && $row->previous_status_id == $item->id ? "selected" : "" }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('previous_status_id', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group modern-form-group">
                <label for="from_status_id">From Status</label>
                <select class="form-control modern-form-control select2" id="from_status_id" name="from_status_id">
                    <option value="">Select</option>
                    @foreach($statuses as $item)
                        <option value="{{ $item->id }}" {{ isset($row) && $row->from_status_id == $item->id ? "selected" : "" }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('from_status_id', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
    </div>
@endif