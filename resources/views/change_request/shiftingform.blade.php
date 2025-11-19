<div class="card-body">
    <!-- Error Message Section -->
    @if($errors->any())
        <div class="m-alert m-alert--icon alert alert-danger" role="alert" id="m_form_1_msg">
            <div class="m-alert__icon">
                <i class="la la-warning"></i>
            </div>
            <div class="m-alert__text">
                There are some errors in your submission. Please correct them and try again.
            </div>
            <div class="m-alert__close">
                <button type="button" class="close" data-close="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif

    <!-- Form Fields Section -->
    <div class="form-group form-group-last mb-5">
        <!-- This can be used for additional spacing or content -->
    </div>

    <!-- CR Number Field -->
    <div class="form-group mb-4">
        <label class="font-weight-bold text-dark">
            CR Number <span class="text-danger">*</span>
            <small class="text-muted">(must match an existing CR)</small>
        </label>
        <div class="input-group input-group-lg">
            <div class="input-group-prepend">
                <span class="input-group-text bg-light">
                    <i class="la la-file-text"></i>
                </span>
            </div>
            <input type="text" 
                   class="form-control form-control-lg @error('change_request_id') is-invalid @enderror" 
                   placeholder="Enter CR Number" 
                   name="change_request_id" 
                   value="{{ old('change_request_id') }}" />
        </div>
        @error('change_request_id')
            <span class="text-danger small d-block mt-1">{{ $message }}</span>
        @enderror
    </div>

    <!-- Priority Checkbox -->
    <div class="form-group mb-4">
        <div class="checkbox-inline">
            <label class="checkbox checkbox-lg">
                <input type="checkbox" name="priority" />
                <span></span>
                Mark as Priority
            </label>
        </div>
    </div>

    <!-- Additional fields can be added here with similar structure -->
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Add any necessary JavaScript here
        // For example, form validation or dynamic behavior
    });
</script>
@endpush