@if($item->CustomField->type == "file")
<div class="col-md-6 change-request-form-field">
    <label for="user_type"> {{ $item->CustomField->label }}</label>
@if( isset($item->validation_type_id)&&($item->validation_type_id==1))
    <span style="color: red;">*</span>
@endif
    <input type="file"  multiple name="{{$item->CustomField->name}}[]" class="form-control form-control-lg" @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif />
</div>	
@endif