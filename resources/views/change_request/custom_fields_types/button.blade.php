@if($item->CustomField->type == "button")
                                                  
 
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
                <a href="{{ url('create_defect/cr_id/') }}/{{$cr->id}}" type="button" name="{{ $item->CustomField->name }}"  class="form-control form-control-lg btn-primary"  value="{{ old($item->CustomField->name, $custom_field_value) }}"  style="color:white; font-weight: bold;font-size:15px;text-align:center"  >{{ $item->CustomField->label }}</a>          
        @else
            @if(isset($cr))
                <label class="form-control form-control-lg">{{ ($cr->{$item->CustomField->name}) }} </label>
            @endif
        @endif
</div>                                                      
@endif