<!-- Custom Fields Loop -->
@foreach($custom_fields as $i => $field)
    <div class="row mb-3 align-items-center">
        <div class="col-md-3">
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="custom_field_id_{{ $i }}" name="custom_field_id[{{ $i }}]" value="{{ $field['id'] }}" 
                @if(isset($field['custom_field_group'][0]['custom_field_id']) && $field['custom_field_group'][0]['custom_field_id'] == $field['id']) checked @endif>
                <label class="form-check-label" for="custom_field_id_{{ $i }}">{{ $field['label'] }} </label>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                <label for="validation_type_id_{{ $i }}">Validation:</label>
                <!-- <select id="validation_type_id_{{ $i }}" name="validation_type_id[]" class="form-control">
                    <option value="">Choose</option>
                    <option value="1" 
                        @if(isset($field['custom_field_group'][0]['validation_type_id']) && $field['custom_field_group'][0]['validation_type_id'] == '1') selected @endif>
                        Required
                    </option>
                </select> -->
                <select id="validation_type_id_{{ $i }}" name="validation_type_id[]" class="form-control">
    <option value="">Choose</option>
    @foreach($validation_type_name as $validation)
        <option value="{{ $validation->id }}"
            @if(isset($field['custom_field_group'][0]['validation_type_id']) && 
                $field['custom_field_group'][0]['validation_type_id'] == $validation->id) selected @endif>
            {{ $validation->name }}
        </option>
    @endforeach
</select>

            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                <label for="sort_{{ $i }}">Sort:</label>
                <input type="number" id="sort_{{ $i }}" name="sort[]" class="form-control" placeholder="Enter Sort" 
                value="{{ old('sort.' . $i, $field['custom_field_group'][0]['sort'] ?? '') }}">
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                <label for="enable_{{ $i }}">Enable:</label>
                <select id="enable_{{ $i }}" name="enable[]" class="form-control">
                    <option value="1" @if(isset($field['custom_field_group'][0]['enable']) && $field['custom_field_group'][0]['enable'] == 1) selected @endif>Enabled</option>
                    <option value="0" @if(isset($field['custom_field_group'][0]['enable']) && $field['custom_field_group'][0]['enable'] == 0) selected @endif>Disabled</option>
                </select>
            </div>
        </div>
    </div>
@endforeach
