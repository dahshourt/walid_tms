@if($item->CustomField->type == "textArea")	
    <div class="col-md-6 change-request-form-field">
        <label for="user_type">{{ $item->CustomField->label }}</label>
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
            <textarea name="{{ $item->CustomField->name }}" class="form-control form-control-lg" {{ $required }} {{ $disabled }}>{{ old($item->CustomField->name, $cr->{$item->CustomField->name}) }}</textarea>
        @else
            <textarea name="{{ $item->CustomField->name }}" class="form-control form-control-lg" {{ $required }} {{ $disabled }}>{{ old($item->CustomField->name) }}</textarea>
        @endif 
        @if($errors->has($item->CustomField->name))
                @foreach($errors->get($item->CustomField->name) as $error)
                    <small class="text-danger d-block">{{ $error }}</small>
                @endforeach
        @endif   
           
       
    </div>
@endif