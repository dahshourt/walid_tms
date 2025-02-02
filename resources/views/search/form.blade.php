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
														<label>search</label>
														<input type="text" class="form-control form-control-lg" placeholder="CR ID" name="search" value="{{ isset($row) ? $row->search : old('search') }}" />
														{!! $errors->first('search', '<span class="form-control-feedback">:message</span>') !!}
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