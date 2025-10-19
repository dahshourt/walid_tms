<div class="card-body">

<div class="form-group form-group-last">

</div>

<div class="form-group">
    <label for="user_name">User Name</label>
    <input type="text" name="user_name" class="form-control form-control-lg"
           placeholder="Enter User Name..."
           value="{{ isset($row) ? $row->user_name : old('user_name') }}" />
    {!! $errors->first('user_name', '<span class="form-control-feedback">:message</span>') !!}
</div>

<div class="form-group">
    <label>Email</label>
    <input type="text" class="form-control form-control-lg"
           placeholder="Email"
           name="email"
           value="{{ isset($row) ? $row->email : old('email') }}" />
    {!! $errors->first('email', '<span class="form-control-feedback">:message</span>') !!}
</div>
<div class="form-group">
    <label for="status">Status</label>
    <select name="status" class="form-control form-control-lg">
        <option value="">Select Status</option>
        <option value="1" {{ (isset($row) && $row->status == true) || old('status') == '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ (isset($row) && $row->status == false) || old('status') == '0' ? 'selected' : '' }}>Inactive</option>
    </select>
    {!! $errors->first('status', '<span class="form-control-feedback">:message</span>') !!}
</div>

</div>
