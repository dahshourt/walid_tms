<div class="card-body">
    <!-- Error Message Section -->
    @if($errors->any())
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="la la-warning la-2x mr-2"></i>
            <div>There are some errors in your submission. Please correct them and try again.</div>
            <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- CR Number Field -->
    <div class="form-group mb-4">
        <label for="change_request_id" class="font-weight-bold text-dark">
            CR Number for Promo: <span class="text-danger">*</span>
        </label>
        <div class="input-group input-group-lg">
            <div class="input-group-prepend">
                <span class="input-group-text bg-light">
                    <i class="la la-file-text"></i>
                </span>
            </div>
            <input 
                type="number"
                class="form-control form-control-lg @error('change_request_id') is-invalid @enderror"
                id="change_request_id"
                name="change_request_id"
                placeholder="Enter CR Number (≥ 40000)"
                min="40000"
                value="{{ old('change_request_id', isset($row) ? $row->change_request_id : '') }}"
                required
            />
        </div>
        <small id="cr-error" class="text-danger mt-1 d-none">
            CR Number must be equal to or greater than 40000.
        </small>
        @error('change_request_id')
            <span class="text-danger small d-block mt-1">{{ $message }}</span>
        @enderror
    </div>

  
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const $input = $('#change_request_id');
    const $error = $('#cr-error');

    // Realtime validation while typing
    $input.on('input', function() {
        const value = parseInt($(this).val());
        if (isNaN(value) || value < 40000) {
            $error.removeClass('d-none');           // show message
            $(this).addClass('is-invalid');         // red border
        } else {
            $error.addClass('d-none');              // hide message
            $(this).removeClass('is-invalid');      // reset border
        }
    });

    // Final check before submit
    $('form').on('submit', function(e) {
        const value = parseInt($input.val());
        if (isNaN(value) || value < 40000) {
            e.preventDefault();
            $error.removeClass('d-none');
            $input.addClass('is-invalid').focus();
        }
    });
});
</script>
@endpush
