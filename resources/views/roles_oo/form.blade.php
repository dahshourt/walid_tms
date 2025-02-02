@php
	$role_permissions = $role_permissions ?? []; // to check if it's create or edit form
@endphp


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
														<h6>Role Name:</h6>
														<input type="text" class="form-control form-control-lg" placeholder="Name" name="role" value="{{ isset($row) ? $row->name : old('name') }}" />
														{!! $errors->first('role', '<span class="form-control-feedback">:message</span>') !!}
													</div>

													<div class="form-group">
														<h6>Choose the Role Permissions:</h6>
														<br>
														@foreach($permissions as $key => $module_permissions)
															<div class="module-section mb-4">
																{{-- <strong>{{ ucfirst($module_permissions->module) . ':' }}</strong> --}}
																<div class="row">
																<div class="col-md-4 mb-2">
																	<div class="form-check">
																		<input 
																			type="checkbox" 
																			name="permissions[]" 
																			value="{{ $module_permissions->name }}" 
																			class="form-check-input permission-checkbox {{ $module_permissions->parent_id ? 'child-of-' . $module_permissions->parent_id : '' }}" 
																			id="permission_{{ $module_permissions->id }}"
																			{{ in_array($module_permissions->name, $role_permissions) ? 'checked' : '' }}
																			data-parent-id="{{ $module_permissions->id }}" >
																		
																		<label class="form-check-label" for="permission_{{ $module_permissions->id }}">
																			<strong>{{ ucfirst($module_permissions->module) . ':' }}</strong> (hint : {{ $module_permissions->name }} )
																		</label>
																	</div>
																</div>
															</div>
															<div class="row ml-3"> 
																	@foreach($module_permissions->children as $permission)
																		<div class="col-md-4 mb-2">
																			<div class="form-check">
																				<input 
																					type="checkbox" 
																					name="permissions[]" 
																					value="{{ $permission->name }}" 
																					class="form-check-input permission-checkbox {{ $permission->parent_id ? 'child-of-' . $permission->parent_id : '' }}" 
																					id="permission_{{ $permission->id }}"
																					{{ in_array($permission->name, $role_permissions) ? 'checked' : '' }}
																					data-parent-id="{{ $permission->parent_id ? 'permission_' . $permission->parent_id : '' }}" >
																				
																				<label class="form-check-label" for="permission_{{ $permission->id }}">
																					{{ $permission->name }}
																				</label>
																			</div>
																		</div>
																	@endforeach
															</div>
															</div>
														@endforeach
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // check parent permission if any child is checked
        document.querySelectorAll('.permission-checkbox').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                let parentId = this.dataset.parentId;
                let childCheckboxes = document.querySelectorAll('.child-of-' + parentId);

                // If a child is checked, check its parent
                if (this.checked && parentId) {
                    let parentCheckbox = document.getElementById(parentId);
                    if (parentCheckbox) {
                        parentCheckbox.checked = true;
                    }
                }

                // If a parent is unchecked, uncheck all its children
                if (!this.checked && childCheckboxes.length > 0) {
                    childCheckboxes.forEach(function (childCheckbox) {
                        childCheckbox.checked = false;
                    });
                }
            });
        });
    });
</script>


@endpush