@if($same_time_from)


        <div class="form-group">
            <label for="from_previous_status_id">Previous From Status:

                <span style="color:blue">(hint: the text show previuos status -> currnet status)</span>
            </label>
            <select class="form-control form-control-lg" id="from_previous_status_id" name="from_status_id[]" multiple="multiple">
                <option value="">Select</option>
                @foreach($statuses as $item)
                    <option value="{{ $item->id }}" >
                    {{ $item->from_status? $item->from_status->status_name : "" }} -> {{ $item->workflowstatus[0]->to_status->status_name }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('from_previous_status_id', '<span class="form-control-feedback">:message</span>') !!}
        </div>

@else

        <div class="form-group">
            <label for="previous_status_id">Pevious Status:</label>
            <select class="form-control form-control-lg" id="previous_status_id" name="previous_status_id">
                <option value="">Select</option>
                @foreach($statuses as $item)
                    <option value="{{ $item->id }}" {{ isset($row) && $row->previous_status_id == $item->id ? "selected" : "" }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('previous_status_id', '<span class="form-control-feedback">:message</span>') !!}
        </div>


        <div class="form-group">
            <label for="from_status_id">From Status:</label>
            <select class="form-control form-control-lg" id="from_status_id" name="from_status_id">
                <option value="">Select</option>
                @foreach($statuses as $item)
                    <option value="{{ $item->id }}" {{ isset($row) && $row->from_status_id == $item->id ? "selected" : "" }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('from_status_id', '<span class="form-control-feedback">:message</span>') !!}
        </div>
@endif        

