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

    <input type="hidden" class="form-control" name="user_id" value="{{ isset($row) ? $row->id : '' }}" />

    <!-- Section: Personal Information -->
    <div class="form-section-title">
        <i class="la la-user"></i> Personal Information
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group modern-form-group">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control modern-form-control" placeholder="Enter full name" name="name"
                    value="{{ isset($row) ? $row->name : old('name') }}" />
                {!! $errors->first('name', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group modern-form-group">
                <label>Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control modern-form-control" placeholder="Enter username"
                    name="user_name" value="{{ isset($row) ? $row->user_name : old('user_name') }}"
                    autocomplete="off" />
                {!! $errors->first('user_name', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group modern-form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input type="text" class="form-control modern-form-control" placeholder="Enter email" name="email"
                    value="{{ isset($row) ? $row->email : old('email') }}" autocomplete="off" />
                {!! $errors->first('email', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
    </div>

    <!-- Section: Access Control -->
    <div class="form-section-title">
        <i class="la la-key"></i> Access Control
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group modern-form-group">
                <label for="role_id">User Roles</label>
                <select class="selectpicker form-control modern-form-control" id="role_id" name="roles[]" multiple
                    title="Select Roles">
                    @foreach($roles as $item)
                        @if(auth()->user()->hasRole('Super Admin') || $item->name != "Super Admin")
                            <option value="{{ $item->name }}" {{ isset($row) && in_array($item->id, $row->roles->pluck('id')->toArray()) ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
                {!! $errors->first('roles', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group modern-form-group">
                <label for="default_group">Default Group <span class="text-danger">*</span></label>
                <select class="form-control modern-form-control" id="default_group" name="default_group">
                    <option value=""> Select Group</option>
                    @foreach($groups as $item)
                        <option value="{{ $item->id }}" {{ isset($row) && $row->default_group == $item->id ? "selected" : "" }}> {{ $item->title }} </option>
                    @endforeach
                </select>
                {!! $errors->first('default_group', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
    </div>

    @php
        $group_arr = [];
        if (isset($row)) {
            $group_arr = array_values($row->user_groups->pluck('group_id')->toArray());
        }
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="form-group modern-form-group">
                <label for="group_id">Additional Groups <span class="text-danger">*</span></label>
                <select class="form-control modern-form-control select2" id="group_id" name="group_id[]"
                    multiple="multiple">
                    @foreach($groups as $item)
                        <option value="{{ $item->id }}" {{ in_array($item->id, $group_arr) ? 'selected' : '' }}>
                            {{ $item->title }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('group_id', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
    </div>

    <!-- Section: Organization Details -->
    <div class="form-section-title">
        <i class="la la-building"></i> Organization Details
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group modern-form-group">
                <label for="unit_id">Unit</label>
                <select class="form-control modern-form-control" id="unit_id" name="unit_id">
                    <option value=""> Select Unit</option>
                    @foreach($units as $item)
                        <option value="{{ $item->id }}" {{ isset($row) && $row->unit_id == $item->id ? "selected" : "" }}>
                            {{ $item->name }} </option>
                    @endforeach
                </select>
                {!! $errors->first('unit_id', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group modern-form-group">
                <label for="department_id">Department</label>
                <select class="form-control modern-form-control" id="department_id" name="department_id">
                    <option value=""> Select Department</option>
                    @foreach($departments as $item)
                        <option value="{{ $item->id }}" {{ isset($row) && $row->department_id == $item->id ? "selected" : "" }}> {{ $item->name }} </option>
                    @endforeach
                </select>
                {!! $errors->first('department_id', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group modern-form-group">
                <label>Man Power</label>
                <input type="text" class="form-control modern-form-control" placeholder="Man Power" name="man_power"
                    value="{{ isset($row) ? $row->man_power : old('man_power') }}" autocomplete="off" />
                {!! $errors->first('man_power', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
    </div>

    <!-- Section: Settings -->
    <div class="form-section-title">
        <i class="la la-toggle-on"></i> Account Status
    </div>

    <div class="form-group modern-form-group">
        <label style="display:block; margin-bottom: 15px;">Active</label>
        <div>
            <label class="modern-toggle-switch">
                <input type="hidden" name="active" value="0" />
                <input type="checkbox" name="active" {{ isset($row) && $row->active == "1" ? "checked" : "" }}
                    value="1" />
                <span class="modern-toggle-slider"></span>
            </label>
            <span class="toggle-label text-muted">Enable this user account</span>
        </div>
    </div>

    <!-- Original Commented Section Preserved -->
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

</div>