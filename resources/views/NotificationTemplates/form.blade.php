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
                                                    <div class="form-group">
														<label for="user_type">Available PlaceHolder:</label>
														<input class="form-control form-control-lg" id="available_placeholder" name="available_placeholder" value="{{ isset($row) ? $row->available_placeholder : old('available_placeholder') }}">
													</div>
                                                    <div class="form-group form-check">
                                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ (isset($row) && $row->is_active) || old('is_active') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_active">Is Active</label>
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



@endpush