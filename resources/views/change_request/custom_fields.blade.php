@foreach($CustomFields as $ky => $item)

@if($item->CustomField->name == 'on_behalf')
    @php
        $crTeamAdminGroup = config('constants.group_names.cr_team');
        $isCrAdmin = false;
        if(auth()->check()) {
             $isCrAdmin = auth()->user()->user_groups()->whereHas('group', function($q) use($crTeamAdminGroup){
                   $q->where('title', $crTeamAdminGroup);
             })->exists();
        }
    @endphp
    @if(!$isCrAdmin)
        @continue
    @endif
@endif

@php 
	$custom_field_value = null;
	if(isset($cr))
	{
		//$custom_field_value = $cr->change_request_custom_fields->where('custom_field_name',$item->CustomField->name)->last();
		$custom_field_value = $cr->change_request_custom_fields->where('custom_field_name', $item->CustomField->name)->sortByDesc('id')->first();
		$custom_field_value = $custom_field_value  ? $custom_field_value->custom_field_value  : $cr->{$item->CustomField->name};	
	}
	
@endphp
												  
@include("$view.custom_fields_types.file")	
@include("$view.custom_fields_types.input")	
@include("$view.custom_fields_types.checkbox")	
@include("$view.custom_fields_types.select")	
@include("$view.custom_fields_types.textarea")	
@include("$view.custom_fields_types.multiselect")                                               
@include("$view.custom_fields_types.button")     
@include("$view.custom_fields_types.radio")   
@include("$view.custom_fields_types.date")                                                 
@include("$view.custom_fields_types.datetime")                                                 
@endforeach
<!-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusSelect = document.querySelector('[name="new_status_id"]');
        const designEstimationInput = document.querySelector('[name="design_estimation"]');

        if (statusSelect && designEstimationInput) {
            statusSelect.addEventListener('change', function () {
                const selectedStatus = parseInt(this.value);
                const designEstimation = parseFloat(designEstimationInput.value) || 0;

                // Get the selected option's display text
                const selectedOptionText = this.options[this.selectedIndex]?.text;

                if (selectedStatus === 44 && designEstimation > 0) {
                    alert(
                        `Error: You selected status "${selectedOptionText}", but design estimation is greater than 0.`
                    );
                    this.value = 'Design estimation'; // Optional: reset selection
                }
            });
        }
    });
</script> -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form'); // Adjust selector if needed
        const statusSelect = document.querySelector('[name="new_status_id"]');
        const designEstimationInput = document.querySelector('[name="design_estimation"]');

        if (form && statusSelect && designEstimationInput) {
            form.addEventListener('submit', function (e) {
                const selectedStatus = parseInt(statusSelect.value) || 0;
                const designEstimation = parseFloat(designEstimationInput.value);
                
                const invalidCondition =
                    (selectedStatus === 44 && designEstimation > 0) ||
                    (selectedStatus === 43 && (!designEstimation || designEstimation === 0));

                if (invalidCondition) {
                    e.preventDefault();

                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Combination',
                        text: 'The provided estimation value is not compatible with the selected workflow',
                        confirmButtonText: 'OK'
                    });

                    // Optional: Reset status
                    statusSelect.value = 'Design estimation';
                }
            });
        }
    });
</script>




