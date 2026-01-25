<!-- Custom Fields Loop -->

@foreach($custom_fields as $i => $field)
    <div class="crt-config-item">
        <!-- Header: Toggle & Label -->
        <div class="crt-config-header">
            <label class="crt-config-toggle">
                <input type="checkbox" id="custom_field_id_{{ $i }}" name="custom_field_id[{{ $i }}]"
                    value="{{ $field['id'] }}" @if(isset($field['custom_field_group'][0]['custom_field_id']) && $field['custom_field_group'][0]['custom_field_id'] == $field['id']) checked @endif>
                <span class="crt-slider"></span>
            </label>
            <label class="crt-config-label mb-0" for="custom_field_id_{{ $i }}">
                {{ $field['label'] }}
            </label>
        </div>

        <!-- Controls: Validation, Sort, Enable -->
        <div class="crt-config-controls">
            <!-- Validation -->
            <div class="crt-control-group">
                <label class="crt-control-label" for="validation_type_id_{{ $i }}">Validation Rule</label>
                <select id="validation_type_id_{{ $i }}" name="validation_type_id[]" class="form-control form-control-sm">
                    <option value="">Optional</option>
                    @foreach($validation_type_name as $validation)
                        <option value="{{ $validation->id }}" @if(
                            isset($field['custom_field_group'][0]['validation_type_id']) &&
                            $field['custom_field_group'][0]['validation_type_id'] == $validation->id
                        ) selected @endif>
                            {{ $validation->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sort Order -->
            <div class="crt-control-group">
                <label class="crt-control-label" for="sort_{{ $i }}">Order</label>
                <input type="number" id="sort_{{ $i }}" name="sort[]" class="form-control form-control-sm" placeholder="0"
                    value="{{ old('sort.' . $i, $field['custom_field_group'][0]['sort'] ?? '') }}">
            </div>

            <!-- Enable Status -->
            <div class="crt-control-group">
                <label class="crt-control-label" for="enable_{{ $i }}">Status</label>
                <select id="enable_{{ $i }}" name="enable[]" class="form-control form-control-sm">
                    <option value="1" @if(isset($field['custom_field_group'][0]['enable']) && $field['custom_field_group'][0]['enable'] == 1) selected @endif>Active</option>
                    <option value="0" @if(isset($field['custom_field_group'][0]['enable']) && $field['custom_field_group'][0]['enable'] == 0) selected @endif>Inactive</option>
                </select>
            </div>
        </div>
    </div>
@endforeach