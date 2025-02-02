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

													<div class="form-group">
														<label for="user_type">Template Name:</label>
														<input class="form-control form-control-lg" id="template_name" name="template_name" value="{{ isset($row) ? $row->name : old('name') }}">
													</div>
                                                    <div class="form-group">
														<label for="user_type">Template Subject:</label>
														<input class="form-control form-control-lg" id="template_subject" name="template_subject" value="{{ isset($row) ? $row->subject : old('subject') }}">
													</div>
                                                    <div class="form-group">
														<label for="user_type">Template Body:</label>
														<textarea class="form-control form-control-lg" id="summernote" name="template_body" rows="10" cols="50">{{ isset($row) ? $row->body : old('body') }}</textarea>
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
    $('#summernote').summernote({
      tabsize: 2,
      height: 150
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