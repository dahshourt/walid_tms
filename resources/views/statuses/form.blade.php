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
														<label for="stage_id">Stage</label>
														<select class="form-control form-control-lg" id="stage_id" name="stage_id">
															<option value=""> Select </option>
															@foreach($stages as $item)
															<option value="{{ $item->id }}" {{ isset($row) && $row->stage_id == $item->id ? "selected" : "" }}> {{ $item->name }} </option>
															@endforeach
														</select>
														{!! $errors->first('stage_id', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<div class="form-group">
														<label for="view_group_id">View by groups:</label>
														<select class="form-control form-control-lg" id="view_group_id" name="view_group_id[]" multiple="multiple">
															<option value=""> Select </option>
															@foreach($groups as $item)
																<option value="{{ $item->id }}" {{ in_array($item->id, $view_group_ids ?? []) ? "selected" : "" }}> 
																	{{ $item->name }} 
																</option>
															@endforeach
														</select>
														{!! $errors->first('view_group_id', '<span class="form-control-feedback">:message</span>') !!}
													</div>
													
													<div class="form-group">
														<label for="set_group_id">Set by groups</label>
														<select class="form-control form-control-lg" id="set_group_id" name="set_group_id[]" multiple="multiple">
															<option value=""> Select </option>
															@foreach($groups as $item)
																<option value="{{ $item->id }}" {{ in_array($item->id, $set_group_ids ?? []) ? "selected" : "" }}>
																	{{ $item->title }}
																</option>
															@endforeach
														</select>
														{!! $errors->first('set_group_id', '<span class="form-control-feedback">:message</span>') !!}
													</div>
													<div class="form-group">
														<label>Status Name</label>
														<input type="text" class="form-control form-control-lg" placeholder="status_name" name="status_name" value="{{ isset($row) ? $row->name : old('name') }}" />
														{!! $errors->first('status_name', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<div class="form-group">
														<label>Status SLA 
														<br/> 
														<span class="hint"> Hint : number values in days </span>	
														</label>
														<input type="text" class="form-control form-control-lg" placeholder="Status SLA" name="sla" value="{{ isset($row) ? $row->sla : old('sla') }}" />
														{!! $errors->first('sla', '<span class="form-control-feedback">:message</span>') !!}
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

