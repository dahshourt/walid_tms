<div class="card-body">

<div class="form-group form-group-last">

</div>

<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="form-group">
            <label for="name">Unit Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control form-control-lg"
                   placeholder="Enter Unit Name..."
                   value="{{ isset($row) ? $row->name : old('name') }}" />
            {!! $errors->first('name', '<span class="form-control-feedback">:message</span>') !!}
        </div>
    </div>

    <div class="col-lg-6 col-md-12">
        <div class="form-group">
            <label>
                Manager <span class="text-danger">*</span>
                <span id="email-loader" class="ml-2" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="sr-only">Checking...</span>
                    </div>
                </span>
            </label>
            <input type="email" class="form-control form-control-lg"
                   placeholder="Manager"
                   name="manager_name"
                   id="manager_email"
                   value="{{ isset($row) ? $row->manager_name : old('manager_name') }}" />
            <div id="email_feedback" class="form-control-feedback mt-1"></div>
            {!! $errors->first('manager_name', '<span class="form-control-feedback">:message</span>') !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select name="status" class="form-control form-control-lg">
                <option value="">Select Status</option>
                <option value="1" {{ (isset($row) && $row->status == true) || old('status') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ (isset($row) && $row->status == false) || old('status') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
            {!! $errors->first('status', '<span class="form-control-feedback">:message</span>') !!}
        </div>
    </div>
</div>

</div>
@push('script')
<script>
$(document).ready(function() {
    const submitButton = $('button[type="submit"]');
    const emailFeedback = $('#email_feedback');
    const emailLoader = $('#email-loader');
    const managerEmailInput = $("#manager_email");
    let currentRequest = null; // To track ongoing AJAX request

    // Initial check on page load
    check_manager_email();

    // Check email on input change with debouncing
    let emailTimeout;

    managerEmailInput.on('paste input', function(){
        // Clear previous timeout
        clearTimeout(emailTimeout);

        // Cancel previous request if still pending
        if (currentRequest) {
            currentRequest.abort();
            currentRequest = null;
        }

        // Reset UI state immediately
        resetEmailState();

        // Debounce the validation (wait 500ms after user stops typing)
        emailTimeout = setTimeout(function() {
            check_manager_email();
        }, 500);
    });

    function resetEmailState() {
        // Hide loader and enable input
        emailLoader.hide();
        managerEmailInput.prop('disabled', false);

        // Clear feedback and validation classes
        emailFeedback.text("");
        emailFeedback.removeClass('text-success text-danger');
        managerEmailInput.removeClass('is-valid is-invalid');

        // Disable submit button by default
        submitButton.prop("disabled", true);
    }

    function check_manager_email() {
        const email = managerEmailInput.val().trim();

        if (!email) {
            // If email is empty, just disable submit button
            resetEmailState();
            return;
        }

        // Basic email format validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            resetEmailState();
            emailFeedback.text('Please enter a valid email format');
            emailFeedback.addClass('text-danger');
            managerEmailInput.addClass('is-invalid');
            return;
        }

        // Start validation process
        startValidation();

        // Make AJAX request
        currentRequest = $.ajax({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            url: '{{ url("/check-division-manager") }}',
            data: {email: email},
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                currentRequest = null;
                endValidation();

                if (data.valid) {
                    // Email is valid
                    submitButton.prop("disabled", false);
                    managerEmailInput.removeClass('is-invalid');
                    managerEmailInput.addClass('is-valid');
                    emailFeedback.text(data.message);
                    emailFeedback.removeClass('text-danger');
                    emailFeedback.addClass('text-success');
                } else {
                    // Email is invalid
                    submitButton.prop("disabled", true);
                    managerEmailInput.removeClass('is-valid');
                    managerEmailInput.addClass('is-invalid');
                    emailFeedback.text(data.message);
                    emailFeedback.removeClass('text-success');
                    emailFeedback.addClass('text-danger');
                }
            },
            error: function(xhr) {
                // Only handle error if request wasn't aborted
                if (xhr.statusText !== 'abort') {
                    currentRequest = null;
                    endValidation();

                    submitButton.prop("disabled", true);
                    managerEmailInput.removeClass('is-valid');
                    managerEmailInput.addClass('is-invalid');
                    emailFeedback.text('Error checking email. Please try again.');
                    emailFeedback.removeClass('text-success');
                    emailFeedback.addClass('text-danger');
                }
            }
        });
    }

    function startValidation() {
        // Show loader and disable input
        emailLoader.show();
        managerEmailInput.prop('disabled', true);

        // Clear previous feedback
        emailFeedback.text("");
        emailFeedback.removeClass('text-success text-danger');
        managerEmailInput.removeClass('is-valid is-invalid');

        // Disable submit button during validation
        submitButton.prop("disabled", true);
    }

    function endValidation() {
        // Hide loader and enable input
        emailLoader.hide();
        managerEmailInput.prop('disabled', false);
    }
});
</script>
@endpush
