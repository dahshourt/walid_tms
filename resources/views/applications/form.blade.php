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
														<label>application Name:</label>
														<input type="text" class="form-control form-control-lg" placeholder="Name" name="name" value="{{ isset($row) ? $row->name : old('name') }}" />
														{!! $errors->first('name', '<span class="form-control-feedback">:message</span>') !!}
													</div>
													<div class="form-group">
														<label for="wf_type_id">Work Flow Type:</label>
														<select class="form-control" id="wf_type_id" name="wf_type_id" >
															<option value="">Choose...</option>
															@foreach ($row as $type)
															<option value="{{ $type->id }}">{{ $type->name }}</option>
															@endforeach
														</select>
													</div>
													<div class="form-group">
														<label>Active</label>
														<div class="checkbox-inline">
															<label class="checkbox">
															<input type="checkbox" name="active" value="1" {{ isset($row) && $row->active == 1 ? "checked" : "" }}>
															<span></span>Yes</label>
															
														</div>
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