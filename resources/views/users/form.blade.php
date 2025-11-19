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
<input type="hidden" class="form-control form-control-lg" placeholder="Name" name="user_id" value="{{ isset($row) ? $row->id : '' }}" />

													<div class="form-group form-group-last">
														
													</div>

													<div class="form-group">
														<label for="user_type">User Type <span class="text-danger">*</span></label>
														<select class="form-control form-control-lg" id="user_type" name="user_type">
															<option value=""> Select </option>
															<option value="0" {{ (isset($row) && $row->user_type != 1) ? "selected" : "" }}> Local User </option>
															<option value="1" {{ (isset($row) && $row->user_type == 1) ? "selected" : "" }}> AD User </option>
														</select>
														{!! $errors->first('user_type', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<div class="form-group">
														<label>Name <span class="text-danger">*</span></label>
														<input type="text" class="form-control form-control-lg" placeholder="Name" name="name" value="{{ isset($row) ? $row->name : old('name') }}" />
														{!! $errors->first('name', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<div class="form-group">
														<label>UserName <span class="text-danger">*</span></label>
														<input type="text" class="form-control form-control-lg" placeholder="UserName" name="user_name" value="{{ isset($row) ? $row->user_name : old('user_name') }}" autocomplete="off" />
														{!! $errors->first('user_name', '<span class="form-control-feedback">:message</span>') !!}
													</div>
													<div class="form-group">
														<label>Email <span class="text-danger">*</span> <small class="text-muted">(required for Local Users)</small></label>
														<input type="text" class="form-control" placeholder="Email" name="email" value="{{ isset($row) ? $row->email : old('email') }}" autocomplete="off" />
														{!! $errors->first('email', '<span class="form-control-feedback">:message</span>') !!}
													</div>
													
													@php
													$style="";
													if(isset($row) && $row->user_type !=1)
													{
														$style="display:block;";
													}
													@endphp
													<div class="form-group local_password_div" style="{{ $style }}">
														<label>Password</label>


														<input type="password" class="form-control" placeholder="password" name="password" autocomplete="off" />
														{!! $errors->first('password', '<span class="form-control-feedback">:message</span>') !!}


													</div>

													<div class="form-group local_password_div" style="{{ $style }}">
														<label>Password Confirmation</label>


														<input type="password" class="form-control" placeholder="confirm password" name="password_confirmation"  autocomplete="off" />
														{!! $errors->first('password_confirmation', '<span class="form-control-feedback">:message</span>') !!}


													</div>

												
													<div class="form-group">
														<label for="role_id">User Roles</label>
														<select class="selectpicker form-control form-control-lg " id="role_id" name="roles[]" multiple title="Select Roles">
															
															@foreach($roles as $item)
																@if(auth()->user()->hasRole('Super Admin') || $item->name != "Super Admin")
																	<option value="{{ $item->name }}" 
																		{{ isset($row) && in_array($item->id, $row->roles->pluck('id')->toArray()) ? 'selected' : '' }}>
																		{{ $item->name }}
																	</option>
																@endif
															@endforeach
														</select>
														{!! $errors->first('roles', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<!--

													<div class="form-group">
														<label for="perission_id">User Permissions</label>
														<select class="selectpicker form-control form-control-lg " id="perission_id" name="permissions[]" multiple title="Select Permissions">
															@foreach($permissions as $item)
															<option value="{{$item->name }}" {{ isset($row) && in_array($item->id, $row->permissions->pluck('id')->toArray()) ? 'selected' : '' }}>
																{{ $item->name }}
															</option>
															@endforeach
														</select>
														{!! $errors->first('roles', '<span class="form-control-feedback">:message</span>') !!}
													</div>

												-->

													<div class="form-group">
														<label for="default_group">Default Group <span class="text-danger">*</span></label>
														<select class="form-control form-control-lg" id="default_group" name="default_group">
															<option value=""> Select </option>
															@foreach($groups as $item)
															<option value="{{ $item->id }}" {{ isset($row) && $row->default_group == $item->id ? "selected" : "" }}> {{ $item->title }} </option>
															@endforeach
														</select>
														{!! $errors->first('default_group', '<span class="form-control-feedback">:message</span>') !!}
													</div>
													@php
														$group_arr = [];
														if (isset($row)) {
														$group_arr = array_values($row->user_groups->pluck('group_id')->toArray());
													}			
													@endphp

													<div class="form-group">
														<label for="group_id">Groups <span class="text-danger">*</span></label>
														<select class="form-control form-control-lg select2" id="group_id" name="group_id[]" multiple="multiple">
															@foreach($groups as $item)
																<option value="{{ $item->id }}" {{ in_array($item->id, $group_arr) ? 'selected' : '' }}>
																	{{ $item->title }}
																</option>
															@endforeach
														</select>
														{!! $errors->first('group_id', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<div class="form-group">
														<label for="unit_id">Unit</label>
														<select class="form-control form-control-lg" id="unit_id" name="unit_id">
															<option value=""> Select </option>
															@foreach($units as $item)
															<option value="{{ $item->id }}" {{ isset($row) && $row->unit_id == $item->id ? "selected" : "" }}> {{ $item->name }} </option>
															@endforeach
														</select>
														{!! $errors->first('unit_id', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<div class="form-group">
														<label for="department_id">Department</label>
														<select class="form-control form-control-lg" id="department_id" name="department_id">
															<option value=""> Select </option>
															@foreach($departments as $item)
															<option value="{{ $item->id }}" {{ isset($row) && $row->department_id == $item->id ? "selected" : "" }}> {{ $item->name }} </option>
															@endforeach
														</select>
														{!! $errors->first('department_id', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<div class="form-group">
														<label>Man Power</label>
														<input type="text" class="form-control" placeholder="Man Power" name="man_power" value="{{ isset($row) ? $row->man_power : old('man_power') }}" autocomplete="off" />
														{!! $errors->first('man_power', '<span class="form-control-feedback">:message</span>') !!}
													</div>


													<div class="form-group">
														<label>Active</label>
														<div class="checkbox-inline">
															<label class="checkbox">
															<input type="hidden" name="active" value="0" />
															<input type="checkbox" name="active" {{ isset($row) && $row->active == "1" ? "checked" : "" }} value="1" />

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