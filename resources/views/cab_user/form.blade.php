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
														<label for="system_id">Application</label>
														<select name="system_id" class="form-control form-control-lg">
															<option value="">select</option>
															@foreach($applications as $item)
																<option value="{{$item->id}}"
																{{ isset($row) && $row->system_id == $item->id ? "selected" : "" }}
																>{{$item->name}}</option>
															@endforeach
														</select>
														{!! $errors->first('system_id', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<div class="form-group">
														<label for="user_id">User</label>
														<select name="user_id" class="form-control form-control-lg">
															<option value="">select</option>
															@foreach($users as $item)
																<option value="{{$item->id}}"
																{{ isset($row) && $row->user_id == $item->id ? "selected" : "" }}
																>{{$item->name}}</option>
															@endforeach
														</select>
														{!! $errors->first('user_id', '<span class="form-control-feedback">:message</span>') !!}
													</div>


													<div class="form-group">
														<label>Active</label>
														<div class="checkbox-inline">
															<label class="checkbox">
															<input type="checkbox" name="active" value="1" {{ isset($row) && $row->active == 1 ? "checked" : "" }}  {{ !isset($row) ? "checked" : "" }}>
															<span></span>Yes</label>
															
														</div>
														
													</div>

													

												</div>
