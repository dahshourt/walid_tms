<div class="card-body">

<div class="form-group form-group-last">

</div>

<div class="form-group">
    <label for="user_name">User Name <span class="text-danger">*</span></label>
    <input type="text" name="user_name" class="form-control form-control-lg"
           placeholder="Enter User Name..."
           value="{{ isset($row) ? $row->user_name : old('user_name') }}" />
    {!! $errors->first('user_name', '<span class="form-control-feedback">:message</span>') !!}
</div>

<div class="form-group">
    <label>
        Email <span class="text-danger">*</span>
        <span id="email-loader" class="ml-2" style="display: none;">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="sr-only">Checking...</span>
            </div>
        </span>
    </label>
    <input type="email" class="form-control form-control-lg"
           placeholder="Email"
           name="email"
           id="director_email"
           value="{{ isset($row) ? $row->email : old('email') }}" />
    <div id="email_feedback" class="form-control-feedback mt-1"></div>
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

@push('script')
<script>
$(document).ready(function() {
    const submitButton = $('button[type="submit"]');
    const emailFeedback = $('#email_feedback');
    const emailLoader = $('#email-loader');
    const directorEmailInput = $("#director_email");
    const directorForm = directorEmailInput.closest('form');
    let currentRequest = null; // To track ongoing AJAX request

    // Initial check on page load
    check_director_email();

    // Check email on input change with debouncing
    let emailTimeout;
    directorEmailInput.on('paste blur', function(){
        // Clear previous timeout
        clearTimeout(emailTimeout);

        // Cancel previous request if still pending
        abortCurrentRequest();

        // Reset UI state immediately
        resetEmailState();

        // Debounce the validation (wait 500ms after user stops typing)
        emailTimeout = setTimeout(function() {
            check_director_email();
        }, 500);
    });

    if (directorForm.length) {
        directorForm.on('submit', function(event) {
            clearTimeout(emailTimeout);
            abortCurrentRequest();
            resetEmailState();

            event.preventDefault();

            const request = check_director_email({ requireEmail: true });

            if (!request || typeof request.done !== 'function') {
                return;
            }

            request.done(function(data) {
                if (data && data.valid) {
                    event.currentTarget.submit();
                }
            });

            request.fail(function() {
                submitButton.prop("disabled", true);
            });
        });
    }

    function abortCurrentRequest() {
        if (currentRequest) {
            currentRequest.abort();
            currentRequest = null;
        }
    }

    function resetEmailState() {
        // Hide loader and enable input
        emailLoader.hide();
        directorEmailInput.prop('disabled', false);

        // Clear feedback and validation classes
        emailFeedback.text("");
        emailFeedback.removeClass('text-success text-danger');
        directorEmailInput.removeClass('is-valid is-invalid');

        // Disable submit button by default
        submitButton.prop("disabled", true);
    }

    function check_director_email(options = {}) {
        const { requireEmail = false } = options;
        const email = directorEmailInput.val().trim();

        if (!email) {
            // If email is empty, just disable submit button
            resetEmailState();
            if (requireEmail) {
                directorEmailInput.removeClass('is-valid').addClass('is-invalid');
                emailFeedback.text('Director email is required');
                emailFeedback.removeClass('text-success').addClass('text-danger');
            }
            return;
        }

        // Basic email format validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            resetEmailState();
            emailFeedback.text('Please enter a valid email format');
            emailFeedback.addClass('text-danger');
            directorEmailInput.addClass('is-invalid');
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
                    directorEmailInput.removeClass('is-invalid');
                    directorEmailInput.addClass('is-valid');
                    emailFeedback.text(data.message);
                    emailFeedback.removeClass('text-danger');
                    emailFeedback.addClass('text-success');
                } else {
                    // Email is invalid
                    submitButton.prop("disabled", true);
                    directorEmailInput.removeClass('is-valid');
                    directorEmailInput.addClass('is-invalid');
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
                    directorEmailInput.removeClass('is-valid');
                    directorEmailInput.addClass('is-invalid');
                    emailFeedback.text('Error checking email. Please try again.');
                    emailFeedback.removeClass('text-success');
                    emailFeedback.addClass('text-danger');
                }
            }
        });
        return currentRequest;
    }

    function startValidation() {
        // Show loader and disable input
        emailLoader.show();
        directorEmailInput.prop('disabled', true);

        // Clear previous feedback
        emailFeedback.text("");
        emailFeedback.removeClass('text-success text-danger');
        directorEmailInput.removeClass('is-valid is-invalid');

        // Disable submit button during validation
        submitButton.prop("disabled", true);
    }

    function endValidation() {
        // Hide loader and enable input
        emailLoader.hide();
        directorEmailInput.prop('disabled', false);
    }
});
</script>
@endpush
