@if($item->CustomField->type == "datetime-local")
    @php
        $disabled = "";
        $required = "";
        $min = "";
        if ((isset($item->enable) && ($item->enable != 1)))
            $disabled = "disabled";
        if (isset($item->validation_type_id) && $item->validation_type_id == 1)
            $required = "required";
        if ($item->CustomField->name == 'deployment_date' || $item->CustomField->name == 'start_date_mds')
            $min = date('Y-m-d\TH:i');
    @endphp

    <div class="col-md-6 change-request-form-field field_{{$item->CustomField->name}}">

        @if(isset($cr))
            <label for="user_type">{{ $item->CustomField->label }} </label>
        @else
            <label for="user_type">{{ $item->CustomField->label }} </label>
        @endif
        @if(isset($item->validation_type_id) && ($item->validation_type_id == 1))
            <span style="color: red;">*</span>
        @endif
        @if(isset($cr))
            <input type="datetime-local" id="{{ $item->CustomField->name }}" name="{{ $item->CustomField->name }}"
                value="{{ $custom_field_value }}" class="form-control form-control-lg {{ $item->CustomField->class }}"
                {{ $required }} {{ $disabled }} @if($min) min="{{$min}}" @endif />
        @else
            @if(isset($cr))
                <label class="form-control form-control-lg">{{ ($cr->{$item->CustomField->name}) }} </label>
            @endif
        @endif
    </div>
    {{-- <div class="col-md-6 change-request-form-field field_{{$item->CustomField->name}}">
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
        <input type="date" name="{{ $item->CustomField->name }}" value="{{ $custom_field_value }}"
            class="form-control form-control-lg" {{ $required }} {{ $disabled }} />
        @else
        <input type="date" name="{{ $item->CustomField->name }}" value="{{ old($item->CustomField->name) }}"
            class="form-control form-control-lg" {{ $required }} {{ $disabled }} />
        @endif
        {!! $errors->first('planned_start_iot_date', '<span class="form-control-feedback">:message</span>') !!}
    </div> --}}
@endif