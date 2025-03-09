@foreach($CustomFields as $ky => $item)

@php 
	$custom_field_value = null;
	if(isset($cr))
	{
		$custom_field_value = $cr->change_request_custom_fields->where('custom_field_name',$item->CustomField->name)->first();
		$custom_field_value = $custom_field_value  ? $custom_field_value->custom_field_value  : $cr->{$item->CustomField->name};	
	}
	
@endphp
												  
@include("$view.custom_fields_types.file")	
@include("$view.custom_fields_types.input")	
@include("$view.custom_fields_types.checkbox")	
@include("$view.custom_fields_types.select")	
@include("$view.custom_fields_types.textarea")	
@include("$view.custom_fields_types.multiselect")                                               
                                                  
@endforeach