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
														<label for="status_id2"> from status:</label>
														<select class="form-control form-control-lg" id="status_id3" name="from_status_id" >
															@foreach($statuses as $item)
															<option value="{{ $item->id }}" 

															{{ ($item->id == (isset($row) && isset($row->status[0]['previous_status_high_level']) ? $row->status[0]['previous_status_high_level'] : null)) ? "selected" : "" }}

															> {{ $item->status_name}} </option>
													<!-- <option value="{{ $item->id }}" > {{ $item->status_name }} </option> -->

															@endforeach
														</select>
														{!! $errors->first('from_status_id', '<span class="form-control-feedback">:message</span>') !!}
													</div>






													<div class="form-group">
														<label for="status_id"> to status:</label>
														<select class="form-control form-control-lg select2" id="status_id" name="to_status_id">
															@foreach($statuses as $item)
															<option value="{{ $item->id }}" 
															
															{{ ($item->id == (isset($row) && isset($row->status[0]['id']) ? $row->status[0]['id'] : null)) ? "selected" : "" }}


															>{{ $item->status_name }}  </option>
													<!-- <option value="{{ $item->id }}" > {{ $item->status_name }} </option> -->

															@endforeach
														</select>
														{!! $errors->first('to_status_id', '<span class="form-control-feedback">:message</span>') !!}
													</div>
													<div class="form-group">
														<label>Name:</label>
														<input type="text" class="form-control form-control-lg" placeholder="Name" name="name" value="{{ isset($row) ? $row->name : old('name') }}" />
														{!! $errors->first('name', '<span class="form-control-feedback">:message</span>') !!}
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