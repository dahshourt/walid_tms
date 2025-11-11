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
														<label for="user_type">Division Manager <span class="text-danger">*</span></label>
														<input type="text" name="name"  class="form-control form-control-lg" place_holder="Enter Division Manager Name..."  value="{{ isset($row) ? $row->name : old('name') }}"  />
														{!! $errors->first('name', '<span class="form-control-feedback">:message</span>') !!}

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
														<input type="email" class="form-control form-control-lg" placeholder="Email" name="division_manager_email" id="division_manager_email" value="{{ isset($row) ? $row->division_manager_email : old('division_manager_email') }}" />
														<div id="email_feedback" class="form-control-feedback mt-1"></div>
														{!! $errors->first('division_manager_email', '<span class="form-control-feedback">:message</span>') !!}

 													</div>

												</div>

@push('script')
<script>
$(document).ready(function() {
    const submitButton = $('button[type="submit"]');
    const emailFeedback = $('#email_feedback');
    const emailLoader = $('#email-loader');
    const divisionManagerEmailInput = $("#division_manager_email");
    const divisionManagerForm = divisionManagerEmailInput.closest('form');
    let currentRequest = null; // To track ongoing AJAX request

    // Initial check on page load
    check_division_manager_email();

    // Check email on input change with debouncing
    let emailTimeout;
    divisionManagerEmailInput.on('paste blur', function(){
        // Clear previous timeout
        clearTimeout(emailTimeout);

        // Cancel previous request if still pending
        abortCurrentRequest();

        // Reset UI state immediately
        resetEmailState();

        // Debounce the validation (wait 500ms after user stops typing)
        emailTimeout = setTimeout(function() {
            check_division_manager_email();
        }, 500);
    });

    if (divisionManagerForm.length) {
        divisionManagerForm.on('submit', function(event) {
            clearTimeout(emailTimeout);
            abortCurrentRequest();
            resetEmailState();

            event.preventDefault();

            const request = check_division_manager_email({ requireEmail: true });

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
        divisionManagerEmailInput.prop('disabled', false);

        // Clear feedback and validation classes
        emailFeedback.text("");
        emailFeedback.removeClass('text-success text-danger');
        divisionManagerEmailInput.removeClass('is-valid is-invalid');

        // Disable submit button by default
        submitButton.prop("disabled", true);
    }

    function check_division_manager_email(options = {}) {
        const { requireEmail = false } = options;
        const email = divisionManagerEmailInput.val().trim();

        if (!email) {
            // If email is empty, just disable submit button
            resetEmailState();
            if (requireEmail) {
                divisionManagerEmailInput.removeClass('is-valid').addClass('is-invalid');
                emailFeedback.text('Division manager email is required');
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
            divisionManagerEmailInput.addClass('is-invalid');
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
                    divisionManagerEmailInput.removeClass('is-invalid');
                    divisionManagerEmailInput.addClass('is-valid');
                    emailFeedback.text(data.message);
                    emailFeedback.removeClass('text-danger');
                    emailFeedback.addClass('text-success');
                } else {
                    // Email is invalid
                    submitButton.prop("disabled", true);
                    divisionManagerEmailInput.removeClass('is-valid');
                    divisionManagerEmailInput.addClass('is-invalid');
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
                    divisionManagerEmailInput.removeClass('is-valid');
                    divisionManagerEmailInput.addClass('is-invalid');
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
        divisionManagerEmailInput.prop('disabled', true);

        // Clear previous feedback
        emailFeedback.text("");
        emailFeedback.removeClass('text-success text-danger');
        divisionManagerEmailInput.removeClass('is-valid is-invalid');

        // Disable submit button during validation
        submitButton.prop("disabled", true);
    }

    function endValidation() {
        // Hide loader and enable input
        emailLoader.hide();
        divisionManagerEmailInput.prop('disabled', false);
    }
});
</script>
@endpush
