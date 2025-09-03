@if($item->CustomField->type == "input")
    <div class="col-md-6 change-request-form-field field_{{$item->CustomField->name}}">
        <label for="user_type">{{ $item->CustomField->label }} </label>
        @if(isset($item->validation_type_id) && $item->validation_type_id == 1)
            <span style="color: red;">*</span>
        @endif

        @if((isset($item->enable) && $item->enable == 1))
            @if(isset($cr))
                <input 
                    type="{{ in_array($item->CustomField->name, ['dev_estimation','design_estimation','testing_estimation']) ? 'number' : 'text' }}" 
                    name="{{ $item->CustomField->name }}" 
                    class="form-control form-control-lg" 
                    value="{{ old($item->CustomField->name, $custom_field_value) }}" 
                    @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif 
                />
            @else
                @if($item->CustomField->name === 'division_manager')
                    <input 
                        type="email" 
                        id="division_manager" 
                        name="{{ $item->CustomField->name }}" 
                        class="form-control form-control-lg @error($item->CustomField->name) is-invalid @enderror" 
                        value="{{ old($item->CustomField->name) }}" 
                        @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif 
                    />
                    @error($item->CustomField->name)
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <small id="email_feedback" class="form-text text-danger"></small>
                @elseif(in_array($item->CustomField->name, ['requester_name', 'requester_email']))
                    <input 
                        type="{{ $item->CustomField->name === 'requester_email' ? 'email' : 'text' }}" 
                        name="{{ $item->CustomField->name }}" 
                        class="form-control form-control-lg @error($item->CustomField->name) is-invalid @enderror" 
                        value="{{ $item->CustomField->name === 'requester_name' ? auth()->user()->name : auth()->user()->email }}" 
                        readonly 
                        @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif 
                    />
                    @error($item->CustomField->name)
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                @elseif(in_array($item->CustomField->name, ['dev_estimation','design_estimation','testing_estimation']))
                    <input 
                        type="number" 
                        name="{{ $item->CustomField->name }}" 
                        class="form-control form-control-lg @error($item->CustomField->name) is-invalid @enderror" 
                        value="{{ old($item->CustomField->name) }}" 
                        @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif 
                    />
                    @error($item->CustomField->name)
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                @else
                    <input 
                        type="text" 
                        name="{{ $item->CustomField->name }}" 
                        class="form-control form-control-lg @error($item->CustomField->name) is-invalid @enderror" 
                        value="{{ old($item->CustomField->name) }}" 
                        @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif 
                    />
                    @error($item->CustomField->name)
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                @endif
            @endif
        @else
            @if(isset($cr))
                <label class="form-control form-control-lg">{{ ($cr->{$item->CustomField->name}) }} </label>
            @endif
        @endif
    </div>
@endif
