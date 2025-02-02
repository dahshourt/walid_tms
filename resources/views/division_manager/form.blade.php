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
														<label for="user_type">Division Manager</label>
														<input type="text" name="name"  class="form-control form-control-lg" place_holder="Enter Division Manager Name..."  value="{{ isset($row) ? $row->name : old('name') }}"  />
														{!! $errors->first('name', '<span class="form-control-feedback">:message</span>') !!}

													</div>

													<div class="form-group">
														<label>Email</label>
														<input type="text" class="form-control form-control-lg" placeholder="Email" name="division_manager_email" value="{{ isset($row) ? $row->division_manager_email : old('division_manager_email') }}" />
														{!! $errors->first('division_manager_email', '<span class="form-control-feedback">:message</span>') !!}

 													</div>

												</div>
