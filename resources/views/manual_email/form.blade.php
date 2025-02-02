<div class="card-body">


@if($errors->any())
    <div class="m-alert m-alert--icon alert alert-danger" role="alert" id="m_form_1_msg">
        <div class="m-alert__icon">
            <i class="la la-warning"></i>
        </div>
        <div class="m-alert__text">
			There are some errors
        </div>
        <div class="m-alert__close">
            <button type="button" class="close" data-close="alert" aria-label="Close">
            </button>
        </div>
    </div>
@endif

													<div class="form-group form-group-last">
														
													</div>

                                                    <div class="form-group" id="mail_template_group">
														<label for="user_type">Select Mail Template: (hint: you can create new template if you didn't see your template)</label>
														<select class="form-control form-control-lg " id="mail_template" name="mail_template">
                                                            <option value=""> Select </option>
															@foreach($templates as $item)
															<option value="{{$item->id }}" >
																{{ $item->name }}
															</option>
															@endforeach
														</select>
													</div>
													
                                                    <h4>Select TO:</h4>
                                                    <div class="form-group" id="to_users_group">
														<label for="user_type">Users:</label>
														<select class="selectpicker form-control form-control-lg " id="to_users" name="to_users" multiple>
                                                            
															@foreach($users as $item)
															<option value="{{$item->id }}" >
																{{ $item->name }}
															</option>
															@endforeach
														</select>
													</div>
                                                    <div class="form-group" id="to_users_group">
														<label for="user_type">Groups:</label>
														<select class="selectpicker form-control form-control-lg " id="to_users" name="to_users" multiple>
                                                            
															@foreach($groups as $item)
															<option value="{{$item->id }}" >
																{{ $item->name }}
															</option>
															@endforeach
														</select>
													</div>

                                                    <h4>Select CC:</h4>
                                                    <div class="form-group" id="to_users_group">
														<label for="user_type">Users:</label>
														<select class="selectpicker form-control form-control-lg " id="to_users" name="to_users" multiple>
                                                            
															@foreach($users as $item)
															<option value="{{$item->id }}" >
																{{ $item->name }}
															</option>
															@endforeach
														</select>
													</div>
                                                    <div class="form-group" id="to_users_group">
														<label for="user_type">Groups:</label>
														<select class="selectpicker form-control form-control-lg " id="to_users" name="to_users" multiple>
                                                            
															@foreach($groups as $item)
															<option value="{{$item->id }}" >
																{{ $item->name }}
															</option>
															@endforeach
														</select>
													</div>
                                                    
													
												</div>

@push('script')

<script>
$('#user_type').change(function() {
    if ($(this).val() != 1) {
        $(".local_password_div").show();
    }
	else
	{
		$(".local_password_div").hide();
	}
});

</script>

<script>
    // JavaScript to hide/show the parent selection field
    document.getElementById('parent').addEventListener('change', function() {
        const parentGroup = document.getElementById('permission_parent_group');
        const permissionParentSelect = document.getElementById('permission_parent');
        
        if (this.checked) {
            parentGroup.style.display = 'none'; // Hide the parent select input if checkbox is checked
            permissionParentSelect.value = "";  // Deselect any selected option in the dropdown
        } else {
            parentGroup.style.display = 'block'; // Show the parent select input if checkbox is unchecked
        }
    });

    // Initial check when the page loads
    window.onload = function() {
        const parentGroup = document.getElementById('permission_parent_group');
        const isParentChecked = document.getElementById('parent').checked;
        const permissionParentSelect = document.getElementById('permission_parent');

        if (isParentChecked) {
            parentGroup.style.display = 'none'; // Hide on page load if checkbox is already checked
            permissionParentSelect.value = "";  // Deselect any selected option in the dropdown
        } else {
            parentGroup.style.display = 'block'; // Show on page load if checkbox is unchecked
        }
    };
</script>

@endpush