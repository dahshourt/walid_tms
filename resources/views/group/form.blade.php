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
		<div class="form-group">
			<label for="user_type">Group Name</label>
			<input type="text" name="title"  class="form-control form-control-lg" place_holder="Enter Group Name..."  value="{{ isset($row) ? $row->title : old('title') }}"  />
			{!! $errors->first('title', '<span class="form-control-feedback">:message</span>') !!}

		</div>

		<div class="form-group">
			<label for="#">Unit Manager:</label>
			<select class="form-control form-control-lg select4" id="#" name="unit_id"  >
				<option value="">Select...</option>
				@foreach($unit_manager as $item)
					<option value="{{ $item->id }}" 
						@if(isset($row) && $row->unit_id == $item->id)
							selected 
						@elseif(old('unit_id') == $item->id)
							selected 
						@endif>
						{{ $item->name }} - {{ $item->manager_name }}
					</option>
				@endforeach
			</select>
    		{!! $errors->first('unit_id', '<span class="form-control-feedback">:message</span>') !!}
		</div>

		<div class="form-group">
			<label for="#">Devision Manager:</label>
			<select class="form-control form-control-lg select3" id="#" name="division_manager_id"  >
				<option value="">Select...</option>
				@foreach($devision_manager as $item)
				 
					<option value="{{ $item->id }}" 
						@if(isset($row) && $row->division_manager_id == $item->id)
							selected 
							@elseif(old('division_manager_id') == $item->id)
							selected 
						@endif>
						{{ $item->name }}
					</option>
				@endforeach
			</select>
    		{!! $errors->first('division_manager_id', '<span class="form-control-feedback">:message</span>') !!}
		</div>

		<div class="form-group">
			<label for="#">Director:</label>
			<select class="form-control form-control-lg select5" id="#" name="director_id"  >
				<option value="">Select...</option>
				@foreach($directors as $item)
					<option value="{{ $item->id }}" 
						@if(isset($row) && $row->director_id == $item->id)
							selected 
						@elseif(old('director_id') == $item->id)
							selected 
						@endif>
						{{ $item->user_name }}
					</option>
				@endforeach
			</select>
    		{!! $errors->first('director_id', '<span class="form-control-feedback">:message</span>') !!}
		</div>
		

		<div class="form-group">
			<label for="user_type">Description</label>
			<input type="text" name="description"  class="form-control form-control-lg" place_holder="Enter Group Description..."  value="{{ isset($row) ? $row->description : old('description') }}"  />
			{!! $errors->first('description', '<span class="form-control-feedback">:message</span>') !!}

		</div>

		<div class="form-group">
			<label for="user_type">Group Power (hint : this mean time in hours for CR time per day)</label>
			<input type="text" name="man_power"  class="form-control form-control-lg" place_holder="Enter Group Power..."  value="{{ isset($row) ? $row->man_power : old('man_power') }}"  />
			{!! $errors->first('man_power', '<span class="form-control-feedback">:message</span>') !!}

		</div>
		<div class="form-group">
			<label for="status_id">Applications:</label>
			<select class="form-control form-control-lg select2" id="status_id" name="application_id[]" multiple="multiple">
				@foreach($applications as $item)
					<option value="{{ $item->id }}" 
						@if(isset($row) && $row->group_applications->contains('application_id', $item->id)) 
							selected 
						@elseif(old('application_id') && in_array($item->id, old('application_id')))
							selected 
						@endif>
						{{ $item->name }}
					</option>
				@endforeach
			</select>
    		{!! $errors->first('application_id', '<span class="form-control-feedback">:message</span>') !!}
		</div>
		<div class="form-group">
			<label for="user_type">Parent</label>
			<select name="parent_id" class="form-control form-control-lg">
				<option value="">....</option>
				@foreach($parent_groups as $item)
					<option value="{{$item->id}}"
					{{ isset($row) && $row->parent_id == $item->id ? "selected" : "" }}
					>{{$item->title}}</option>
				@endforeach
			</select>
		</div>

		<div class="form-group">
			<label for="user_type">Head Group Name</label>
			<input type="text" name="head_group_name"  class="form-control form-control-lg" place_holder="Enter Head Group Name..."  value="{{ isset($row) ? $row->head_group_name : old('head_group_name') }}"  />
			{!! $errors->first('head_group_name', '<span class="form-control-feedback">:message</span>') !!}

		</div>

		<div class="form-group">
			<label for="user_type">Head Group Email</label>
			<input type="text" name="head_group_email"  class="form-control form-control-lg" place_holder="Enter Head Group Email..."  value="{{ isset($row) ? $row->head_group_email : old('head_group_email') }}"  />
			{!! $errors->first('head_group_email', '<span class="form-control-feedback">:message</span>') !!}

		</div>

		<div class="form-group">
			<label>Technical Team?</label>
			<div class="checkbox-inline">
				<label class="checkbox">						
					<input type="hidden" name="technical_team" value="0">
					<input type="checkbox" name="technical_team" value="1" 
						{{ isset($row) && $row->technical_team == 1 ? "checked" : "" }}>
					<span></span>Yes
				</label>
			</div>
		</div>

		<div class="form-group">
			<label>Recive Notification?</label>
			<div class="checkbox-inline">
				<label class="checkbox">
				<input type="hidden" name="recieve_notification" value="0">
				<input type="checkbox" name="recieve_notification" value="1" {{ isset($row) && $row->recieve_notification == '1' ? "checked" : "" }}>
				<span></span>Yes</label>
			</div>
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
</div>
