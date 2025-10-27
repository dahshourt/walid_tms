@forelse($collection as $key => $row)
<tr>
    <td>{{ $row->id }}</td>
    <td>
        <span class="badge badge-light-primary">{{ ucfirst($row->type) }}</span>
    </td>
    <td>{{ $row->label }}</td>
    <td>
        <span class="font-weight-bold">{{ $row->name }}</span>
        @if($row->class)
            <br><small class="text-muted">Class: {{ $row->class }}</small>
        @endif
    </td>
    @can('Edit Custom Fields')
    <td>
        <span class="switch switch-outline switch-icon switch-success">
            <label>
                <input type="checkbox" {{ $row->active ? 'checked' : '' }} 
                       onchange="toggleStatus({{ $row->id }}, this)" />
                <span></span>
            </label>
        </span>
    </td>
    @endcan
    <td nowrap="nowrap">
        @can('Edit Custom Fields')
        <a href="{{ route('custom-fields.edit', $row->id) }}" 
           class="btn btn-sm btn-clean btn-icon mr-2" 
           title="Edit Custom Field">
            <span class="svg-icon svg-icon-md">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"></rect>
                        <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                    </g>
                </svg>
            </span>
        </a>
        @endcan
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center">
        <div class="d-flex flex-column align-items-center py-10">
            <div class="symbol symbol-100 mb-5">
                <span class="symbol-label bg-light-primary">
                    <i class="flaticon2-search-1 text-primary" style="font-size: 3rem;"></i>
                </span>
            </div>
            <div class="font-weight-bold text-dark-50 font-size-lg">No custom fields found</div>
            <div class="text-muted font-size-sm mt-2">
                @if(request('search'))
                    Try adjusting your search criteria
                @else
                    Create your first custom field to get started
                @endif
            </div>
        </div>
    </td>
</tr>
@endforelse

@push('script')
<script>
function toggleStatus(id, element) {
    var isChecked = $(element).is(':checked');
    var statusText = isChecked ? 'activate' : 'deactivate';
    var confirmText = isChecked ? 'Yes, activate it!' : 'Yes, deactivate it!';

    Swal.fire({
        title: 'Are you sure?',
        text: "You want to " + statusText + " this custom field?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading alert
            Swal.fire({
                title: 'Processing...',
                text: 'Updating custom field status...',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Proceed with status update
            $.ajax({
                url: "{{ route('custom-fields.updateStatus') }}",
                type: 'POST',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Updated!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error'
                        });
                        // Revert the switch
                        $(element).prop('checked', !isChecked);
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to update status',
                        icon: 'error'
                    });
                    // Revert the switch
                    $(element).prop('checked', !isChecked);
                }
            });
        } else {
            // User cancelled, revert the switch
            $(element).prop('checked', !isChecked);
        }
    });
}

// Initialize tooltips
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush
