@if($item->CustomField->type == "datetime-local")
    @php
        $disabled = "";
        $required = "";
        if((isset($item->enable)&&($item->enable!=1))) $disabled = "disabled";
        if(isset($item->validation_type_id) && $item->validation_type_id == 1) $required = "required";
    @endphp

<div class="col-md-6 change-request-form-field field_{{$item->CustomField->name}}">
 
    @if(isset($cr))
        <label for="user_type">{{ $item->CustomField->label }} </label>
    @else
        <label for="user_type">{{ $item->CustomField->label }} </label>
    @endif
    @if( isset($item->validation_type_id)&&($item->validation_type_id==1))
        <span style="color: red;">*</span>
    @endif
        @if((isset($item->enable)&&($item->enable==1)))
        <input type="datetime-local" name="{{ $item->CustomField->name }}" value="{{ $custom_field_value }}" class="form-control form-control-lg" {{ $required }} {{ $disabled }} />
        @else
            @if(isset($cr))
                <label class="form-control form-control-lg">{{ ($cr->{$item->CustomField->name}) }} </label>
            @endif
        @endif
</div>                                                      
@endif