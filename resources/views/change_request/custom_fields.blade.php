@foreach($CustomFields as $ky => $item)
												  
@include("$view.custom_fields_types.file")	
@include("$view.custom_fields_types.input")	
@include("$view.custom_fields_types.checkbox")	
@include("$view.custom_fields_types.select")	
@include("$view.custom_fields_types.textarea")	
@include("$view.custom_fields_types.multiselect")                                               
                                                  
@endforeach