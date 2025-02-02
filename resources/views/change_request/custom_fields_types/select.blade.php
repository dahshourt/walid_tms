@if($item->CustomField->type == "select")
    <div class="col-md-6 change-request-form-field field_{{$item->CustomField->name}}">
        
    @if(($item->CustomField->name=='tester_id')&&!empty($cr->test_duration))
        <label type="text" class="form-control form-control-lg"> {{$cr->tester->name}} </label>
    @elseif(($item->CustomField->name=='designer_id')&&!empty($cr->design_duration))
        <label type="text" class="form-control form-control-lg"> {{$cr->designer->name}} </label>
    @elseif(($item->CustomField->name=='developer_id')&&!empty($cr->develop_duration))
        <label type="text" class="form-control form-control-lg"> {{$cr->developer->name}} </label>
    @else
        <label for="user_type">{{ $item->CustomField->label }} </label>
    @endif
    @if( isset($item->validation_type_id)&&($item->validation_type_id==1))
        <span style="color: red;">*</span>
    @endif
    @if(!isset($cr) && $item->CustomField->name === 'application_id')
    <select name="{{ $item->CustomField->name }}" class="form-control form-control-lg"  >
        <option value="{{$target_system->id}}">{{$target_system->name}}</option>
    </select>
    @elseif($item->CustomField->name=="cr_member")
   
    <select name="{{ $item->CustomField->name }}" class="form-control form-control-lg">
        <option value="">Select</option>
        @foreach($item->CustomField->getCustomFieldValue() as $value)
            @if($value->defualt_group->title === 'CR Team Admin') {{-- Filter by group name --}}
                <option value="{{ $value->id }}" {{ isset($cr) && $cr->{$item->CustomField->name} == $value->id ? 'selected' : ''  }}>{{ $value->name }}</option> 

            @endif
        @endforeach
    </select>
    @else
    <select name="{{ $item->CustomField->name }}" class="form-control form-control-lg" @if(isset($item->validation_type_id) && $item->validation_type_id == 1) required @endif
        @cannot('Set Time For Another User')
            @if($item->CustomField->name == 'tester_id' || $item->CustomField->name == 'designer_id' || $item->CustomField->name == 'developer_id')
                disabled
            @endif
        @endcannot   {{(isset($item->enable)&&($item->enable==1))?'enabled':'disabled'}}>

            @cannot('Set Time For Another User')
                @if($item->CustomField->name == 'tester_id' || $item->CustomField->name == 'designer_id' || $item->CustomField->name == 'developer_id')
                    <option value="{{ auth()->user()->id }}" selected>{{ auth()->user()->name }}</option>
                @endif
            @endcannot
            
                                                                  
            @if($item->CustomField->name == "new_status_id")
                <option value="{{$cr->getCurrentStatus()?->status?->status_name}}" disabled selected>{{ $cr->getCurrentStatus()?->status?->status_name }}</option>
                    @foreach($cr->set_status as $status)
                        @if($status->same_time == 1)
                            <option value="{{ $status->id }}" {{ $cr->{$item->CustomField->name} == $status->id ? 'selected' : '' }}>{{ $status->to_status_label }} </option>
                        @else
                            <option value="{{ $status->id }}"  {{ $cr->{$item->CustomField->name} == $status->id ? 'selected' : '' }}>
                                {{ $status->workflowstatus[0]->to_status->high_level? $status->workflowstatus[0]->to_status->high_level->name : $status->workflowstatus[0]->to_status->status_name  }} 
                            </option>
                        @endif            
                    @endforeach
                @elseif($item->CustomField->name == "release_name")
                    <option value=""> select </option>
                    @foreach($cr->get_releases() as $release)
                        <option value="{{ $release->id }}" {{ $cr->{$item->CustomField->name} == $release->id ? 'selected' : '' }}>{{ $release->name }} </option>
                    @endforeach
                @else
                @if((isset($item->enable)&&($item->enable==1)))
                    <option value="">Select</option>
                    @if($item->CustomField->name == "developer_id" )
                        @foreach($developer_users as $developer)
                            <option value="{{ $developer->id }}" {{ old($developer->user_name, $cr->{$item->CustomField->name}) == $developer->id ? 'selected' : '' }}>{{ $developer->user_name }}</option>
                        @endforeach
                    @endif
                    @if($item->CustomField->name == "tester_id" )
                        @foreach($testing_users as $users)
                            <option value="{{ $users->id }}" {{ old($users->user_name, $cr->{$item->CustomField->name}) == $users->id ? 'selected' : '' }}>{{ $users->user_name }}</option>
                        @endforeach
                    @endif
                    @if($item->CustomField->name == "sa_users" )
                        @foreach($sa_users as $users)
                            <option value="{{ $users->id }}" {{ old($users->user_name, $cr->{$item->CustomField->name}) == $users->id ? 'selected' : '' }}>{{ $users->user_name }}</option>
                        @endforeach
                    @endif

                    @foreach($item->CustomField->getCustomFieldValue() as $value)
                        @if($item->CustomField->name == "developer_id" )
                        @elseif($item->CustomField->name == "tester_id" )
                        @elseif($item->CustomField->name == "designer_id" )
                        @else
                            @if(isset($cr))
                            <option value="{{ $value->id }}" {{ old($item->CustomField->name, $cr->{$item->CustomField->name}) == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>	
                            @else
                            <option value="{{ $value->id }}">{{ $value->name }}</option>	
                            @endif	
                        @endif
                    @endforeach
                @else
                @php
                    // Get the selected value from old input or the current record (cr)
                    $selectedValue = "";
                    if(isset($cr)) $selectedValue = old($item->CustomField->name, $cr->{$item->CustomField->name});
                @endphp

                @if($selectedValue)
                    @foreach($item->CustomField->getCustomFieldValue() as $value)
                        @if($value->id == $selectedValue)
                            <option value="{{ $value->id }}" selected>{{ $value->name }}</option>
                        @endif
                    @endforeach
                @else
                    <option value="">Select</option>
                @endif
                                                                    
            @endif
        @endif
    </select>
    @endif
                                                          
                                                              
</div>
@endif