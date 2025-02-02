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
														<label for="user_type">Groups:</label>
														<select class="form-control form-control-lg" id="groups" name="group_id">
															<option value=""> Select </option>
															@foreach($groups as $item)
															<option value="{{ $item->id }}" {{ isset($row) && $row->group_id == $item->id ? "selected" : "" }}> {{ $item->title }} </option>
															@endforeach
														</select>
															
														
													</div>

													<div class="form-group">
														<label>Rule Name:</label>
														<select class="form-control form-control-lg" id="roles" name="rule_id[]" multiple>
															<option value="">Select</option>
															@foreach($rules as $item)
																
																	<option value="{{ $item->id }}" {{ isset($row) && $row->rule_id == $item->id ? "selected" : "" }}> {{ $item->rule_name }} </option>

																
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

@endpush