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
        <label for="permission_module">Permission Module <span class="text-danger">*</span></label>
        <input class="form-control form-control-lg" id="permission_module" name="permission_module"
               value="{{ isset($row) ? $row->module : old('name') }}">
    </div>
    <div class="form-group">
        <label for="permission">Permission Name <span class="text-danger">*</span></label>
        <input class="form-control form-control-lg" id="permission" name="permission"
               value="{{ isset($row) ? $row->name : old('name') }}">
    </div>
    <div class="form-group">
        <div class="form-check">
            <input type="checkbox"
                   class="form-check-input"
                   id="parent"
                   name="parent"
                   value="1"
                {{ isset($row) && $row->parent_id == null ? 'checked' : '' }}>
            <label class="form-check-label" for="parent">Is Parent</label>
        </div>
    </div>
    <div class="form-group" id="permission_parent_group">
        <label for="user_type">Select Permission Parent:</label>
        <select class="form-control form-control-lg " id="permission_parent" name="permission_parent"
                title="Select Permissions">
            <option value=""> Select</option>
            @foreach($permissions_parents as $item)
                <option value="{{$item->id }}" {{ isset($row) && $row->parent_id == $item->id ? 'selected' : ''  }}>
                    {{ $item->name }}
                </option>
            @endforeach
        </select>
        {!! $errors->first('roles', '<span class="form-control-feedback">:message</span>') !!}
    </div>


</div>

@push('script')

    <script>
        $('#user_type').change(function () {
            if ($(this).val() != 1) {
                $(".local_password_div").show();
            } else {
                $(".local_password_div").hide();
            }
        });

    </script>

    <script>
        // JavaScript to hide/show the parent selection field
        document.getElementById('parent').addEventListener('change', function () {
            const parentGroup = document.getElementById('permission_parent_group');
            const permissionParentSelect = document.getElementById('permission_parent');

            if (this.checked) {
                parentGroup.style.display = 'none'; // Hide the parent select input if checkbox is checked
                permissionParentSelect.value = "";  // Deselect any selected option in the dropdown
            } else {
                parentGroup.style.display = 'block'; // Show the parent select input if checkbox is unchecked
            }
        });

        // Initial check when the page loads
        window.onload = function () {
            const parentGroup = document.getElementById('permission_parent_group');
            const isParentChecked = document.getElementById('parent').checked;
            const permissionParentSelect = document.getElementById('permission_parent');

            if (isParentChecked) {
                parentGroup.style.display = 'none'; // Hide on page load if checkbox is already checked
                permissionParentSelect.value = "";  // Deselect any selected option in the dropdown
            } else {
                parentGroup.style.display = 'block'; // Show on page load if checkbox is unchecked
            }
        };
    </script>

@endpush
