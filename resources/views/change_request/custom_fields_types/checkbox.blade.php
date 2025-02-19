@if($item->CustomField->type == "checkbox")

    <div class="col-md-6 change-request-form-field">
        <label for="user_type">{{ $item->CustomField->label }} </label>
                                                                    
       
            @if( isset($item->validation_type_id)&&($item->validation_type_id==1))
                <span style="color: red;">*</span>
            @endif
        <div class="form-group">
            <div class="checkbox-inline">
                <label class="checkbox">
                <input type="checkbox" value="1" {{ (isset($item->enable)&&($item->enable!=1)) ? "disabled" : "" }} name="{{ $item->CustomField->name }}"  @if(isset($cr->{$item->CustomField->name}) && $cr->{$item->CustomField->name} == 1) checked @endif
                class="form-control form-control-lg form-group col-md-3" @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif/>

                <span></span>Yes</label>
                                                        
            </div>
                                                    
        </div>
    </div>
                                                              
@endif
                                                          