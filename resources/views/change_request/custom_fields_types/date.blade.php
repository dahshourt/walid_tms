@if($item->CustomField->type == "date")
<div class="col-md-6 change-request-form-field field_{{$item->CustomField->name}}">
    <label>{{ $item->CustomField->label }}</label>
    @if( isset($item->validation_type_id)&&($item->validation_type_id==1))
        <span style="color: red;">*</span>
    @endif
    @php
        $disabled = "";
        $required = "";
        if((isset($item->enable)&&($item->enable!=1))) $disabled = "disabled";
        if(isset($item->validation_type_id) && $item->validation_type_id == 1) $required = "required";
    @endphp
    @if(isset($cr))
        <input type="date" name="{{ $item->CustomField->name }}" value="{{ $custom_field_value }}" class="form-control form-control-lg" {{ $required }} {{ $disabled }} />
    @else
        <input type="date" name="{{ $item->CustomField->name }}" value="{{ old($item->CustomField->name) }}" class="form-control form-control-lg" {{ $required }} {{ $disabled }} />
    @endif
    {!! $errors->first('planned_start_iot_date', '<span class="form-control-feedback">:message</span>') !!}
</div>
@endif