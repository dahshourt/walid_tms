@php
    $role_permissions = $role_permissions ?? [];
    $isView = request()->routeIs('*.show');
    $inputClass = $isView ? 'form-control-plaintext' : 'form-control';
    $isDisabled = $isView ? 'disabled' : '';
    $currentYear = date('Y');
    $years = range($currentYear - 5, $currentYear + 5);

    // Status Badge Color Mapping
    $statusColors = [
        'Open' => 'primary',
        'In Progress' => 'warning',
        'Delivered' => 'success'
    ];
    $currentStatus = $row->status ?? 'Open';
    $statusColor = $statusColors[$currentStatus] ?? 'secondary';
@endphp

<div class="card-body">
    @if($errors->any())
        <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
            <div class="alert-icon"><i class="flaticon-warning"></i></div>
            <div class="alert-text">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><i class="ki ki-close"></i></span>
                </button>
            </div>
        </div>
    @endif

    <!-- Section: General Information -->
    <div class="card card-custom card-stretch gutter-b">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title font-weight-bolder">General Information</h3>
            @if(isset($row))
                <div class="card-toolbar">
                    <span class="label label-lg label-light-{{ $statusColor }} label-inline font-weight-bold py-4">
                        Status: {{ $currentStatus }}
                    </span>
                </div>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="font-weight-bold">KPI Name <span class="text-danger">*</span></label>
                        <input type="text" class="{{ $inputClass }}" name="name"
                               value="{{ $row->name ?? old('name') }}" {{ $isDisabled }} required
                               placeholder="Enter KPI Name">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Priority <span class="text-danger">*</span></label>
                        <select class="form-control kt-select2" name="priority" {{ $isDisabled }} required>
                            <option value="">Select Priority</option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority }}"
                                    {{ (isset($row) && $row->priority == $priority) || old('priority') == $priority ? 'selected' : '' }}>
                                    {{ $priority }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Strategic Pillar <span class="text-danger">*</span>
                            <span id="pillar-loader" class="ml-2" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </span>
                        </label>
                        <select class="form-control kt-select2" name="pillar_id" id="pillar_id"
                                {{ $isDisabled }} required>
                            <option value="">Select Strategic Pillar</option>
                            @foreach($pillars as $pillar)
                                <option value="{{ $pillar->id }}"
                                    {{ (isset($row) && $row->pillar_id == $pillar->id) || old('pillar_id') == $pillar->id ? 'selected' : '' }}>
                                    {{ $pillar->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Initiative <span class="text-danger">*</span>
                            <span id="initiative-loader" class="ml-2" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </span>
                        </label>
                        <select class="form-control kt-select2" name="initiative_id" id="initiative_id"
                                {{ $isDisabled }} required>
                            <option value="">Select Initiative</option>
                            @if(isset($row) && $row->initiative)
                                <option value="{{ $row->initiative->id }}"
                                        selected>{{ $row->initiative->name }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>

             <div class="row">
                 <div class="col-md-6">
                     <div class="form-group">
                         <label class="font-weight-bold">
                             Sub-Initiative <span class="text-muted font-weight-normal">(Optional)</span>
                             <span id="sub-initiative-loader" class="ml-2" style="display: none;">
                                 <div class="spinner-border spinner-border-sm text-primary" role="status">
                                     <span class="sr-only">Loading...</span>
                                 </div>
                             </span>
                         </label>
                         <select class="form-control kt-select2" name="sub_initiative_id"
                                 id="sub_initiative_id" {{ $isDisabled }}>
                             <option value="">Select Sub-Initiative</option>
                             @if(isset($row) && $row->subInitiative)
                                 <option value="{{ $row->subInitiative->id }}"
                                         selected>{{ $row->subInitiative->name }}</option>
                             @endif
                         </select>
                     </div>
                 </div>
                 <div class="col-md-6">
                     <div class="form-group">
                         <label class="font-weight-bold">
                             Requester Email <span class="text-danger">*</span>
                             <span id="requester-email-loader" class="ml-2" style="display: none;">
                                 <div class="spinner-border spinner-border-sm text-primary" role="status">
                                     <span class="sr-only">Checking...</span>
                                 </div>
                             </span>
                         </label>
                         <input type="email" class="{{ $isView ? 'form-control-plaintext' : 'form-control' }}" 
                                name="requester_email" id="requester_email"
                                value="{{ $row->requester_email ?? old('requester_email') }}" 
                                {{ $isView || isset($row) ? 'disabled' : '' }}
                                placeholder="Enter Requester Email"
                                style="{{ $isView || isset($row) ? 'background-color: #f3f6f9; opacity: 0.65;' : '' }}">
                         <div id="requester_email_feedback" class="form-control-feedback mt-1"></div>
                     </div>
                 </div>
             </div>

             <div class="row">
                 <div class="col-md-6">
                     <div class="form-group">
                         <label class="font-weight-bold">Creator</label>
                         <input type="text" class="form-control-plaintext"
                                value="{{ isset($row) ? ($row->creator->user_name ?? 'Unknown') : auth()->user()->user_name }}"
                                disabled style="background-color: #f3f6f9; opacity: 0.65;">
                         @if(!isset($row))
                             <input type="hidden" name="created_by" value="{{ auth()->id() }}">
                         @endif
                     </div>
                 </div>
             </div>
        </div>
    </div>

    <!-- Section: Business & Classification -->
    <div class="card card-custom card-stretch gutter-b">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title font-weight-bolder">Business & Classification</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Business Unit (BU) <span class="text-danger">*</span></label>
                        <input type="text" class="{{ $inputClass }}" name="bu"
                               value="{{ $row->bu ?? old('bu') }}" {{ $isDisabled }} required
                               placeholder="Enter Business Unit">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Sub-Business Unit <span class="text-muted font-weight-normal">(Optional)</span></label>
                        <input type="text" class="{{ $inputClass }}" name="sub_bu"
                               value="{{ $row->sub_bu ?? old('sub_bu') }}"
                               {{ $isDisabled }} placeholder="Enter Sub-Business Unit">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Type <span class="text-danger">*</span></label>
                        <select class="form-control kt-select2" name="type_id" {{ $isDisabled }} required>
                            <option value="">Select Type</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}"
                                    {{ (isset($row) && $row->type_id == $type->id) || old('type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Classification <span class="text-danger">*</span></label>
                        <select class="form-control kt-select2" name="classification" id="classification" {{ $isDisabled }} required>
                            <option value="">Select Classification</option>
                            @foreach($classifications as $class)
                                <option value="{{ $class }}"
                                    {{ (isset($row) && $row->classification == $class) || old('classification') == $class ? 'selected' : '' }}>
                                    {{ $class }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Timeline -->
    <div class="card card-custom card-stretch gutter-b">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title font-weight-bolder">Timeline</h3>
        </div>
        <div class="card-body">
            <div id="timeline-kpi-wrapper">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Target Launch Quarter <span class="text-danger">*</span></label>
                            <select class="form-control kt-select2" name="target_launch_quarter" {{ $isDisabled }} required>
                                <option value="">Select Quarter</option>
                                @foreach($quarters as $quarter)
                                    <option value="{{ $quarter }}"
                                        {{ (isset($row) && $row->target_launch_quarter == $quarter) || old('target_launch_quarter') == $quarter ? 'selected' : '' }}>
                                        {{ $quarter }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Target Launch Year <span class="text-danger">*</span></label>
                            <select class="form-control kt-select2" name="target_launch_year" {{ $isDisabled }} required>
                                <option value="">Select Year</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}"
                                        {{ (isset($row) && $row->target_launch_year == $year) || old('target_launch_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Target Numbers of CRs</label>
                            <input
                                type="number"
                                class="{{ $inputClass }}"
                                name="target_cr_count"
                                value="{{ $row->target_cr_count ?? old('target_cr_count') }}"
                                {{ $isDisabled }}
                                min="0"
                                placeholder="Enter Target Numbers of CRs">
                        </div>
                    </div>
                </div>
            </div>

            <div id="timeline-project-wrapper" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Project</label>
                            <select class="form-control kt-select2" name="project_id" id="project_id" {{ $isDisabled }}>
                                <option value="">Select Project</option>
                                @foreach(($projects ?? []) as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Details -->
    <div class="card card-custom card-stretch gutter-b">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title font-weight-bolder">Details</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="font-weight-bold">KPI Brief <span class="text-danger">*</span></label>
                <textarea class="{{ $inputClass }}" name="kpi_brief" rows="4" {{ $isDisabled }} required
                          placeholder="Enter KPI Brief">{{ $row->kpi_brief ?? old('kpi_brief') }}</textarea>
            </div>

            <div class="form-group">
                <label class="font-weight-bold">KPI Comment</label>
                <textarea class="{{ $inputClass }}" name="kpi_comment" rows="4"
                          {{ $isDisabled }} placeholder="Add a comment (optional)">{{ old('kpi_comment') }}</textarea>
                <span class="form-text text-muted">This comment will be added to the history logs.</span>
            </div>
        </div>
    </div>

    <!-- Section: Comments History (Moved to separate partial) -->


    <!-- Hidden Inputs for Read-Only Status -->
    @if(isset($row))
        <input type="hidden" name="status" value="{{ $row->status }}">
    @else
        <input type="hidden" name="status" value="Open">
    @endif
</div>

@push('css')
    <link href="{{ asset('assets/plugins/custom/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .card-title { font-size: 1.2rem; }
        .form-group label { font-size: 0.95rem; }
        .timeline.timeline-3 .timeline-item .timeline-content { padding-left: 10px; }

        /* Simple section borders */
        .card.card-custom.card-stretch.gutter-b {
            border: 1px solid #e4e6ef;
            margin-bottom: 20px;
        }
    </style>
@endpush

@push('script')
    <script>
        jQuery(document).ready(function () {
            $('.kt-select2').select2({
                placeholder: "Select an option",
                allowClear: true,
                width: '100%'
            });

            const classificationSelect = $('#classification');
            const kpiTimelineWrapper = $('#timeline-kpi-wrapper');
            const projectTimelineWrapper = $('#timeline-project-wrapper');
            const projectSelect = $('#project_id');

            const toggleTimelineByClassification = () => {
                const value = classificationSelect.val();
                if (value === 'PM') {
                    kpiTimelineWrapper.hide();
                    projectTimelineWrapper.show();
                    projectSelect.prop('disabled', false).trigger('change.select2');
                } else {
                    projectTimelineWrapper.hide();
                    projectSelect.val('').trigger('change');
                    projectSelect.prop('disabled', true).trigger('change.select2');
                    kpiTimelineWrapper.show();
                }
            };

            classificationSelect.on('change', toggleTimelineByClassification);
            toggleTimelineByClassification();

            // Cascading Select Logic for Pillar -> Initiative -> Sub-Initiative
            const pillarSelect = $('#pillar_id');
            const initiativeSelect = $('#initiative_id');
            const subInitiativeSelect = $('#sub_initiative_id');
            const pillarLoader = $('#pillar-loader');
            const initiativeLoader = $('#initiative-loader');
            const subInitiativeLoader = $('#sub-initiative-loader');

            // Store initial values for edit mode
            const initialInitiativeId = '{{ isset($row) ? $row->initiative_id : old("initiative_id") }}';
            const initialSubInitiativeId = '{{ isset($row) ? $row->sub_initiative_id : old("sub_initiative_id") }}';

            // When pillar changes, load initiatives
            pillarSelect.on('change', function () {
                const pillarId = $(this).val();

                // Reset initiative and sub-initiative
                initiativeSelect.html('<option value="">Select Initiative</option>').prop('disabled', true).trigger('change');
                subInitiativeSelect.html('<option value="">Select Sub-Initiative</option>').prop('disabled', true).trigger('change');

                if (!pillarId) {
                    return;
                }

                // Show loader
                initiativeLoader.show();
                initiativeSelect.prop('disabled', true);

                // Fetch initiatives
                $.ajax({
                    url: '{{ route("kpis.get-initiatives") }}',
                    type: 'GET',
                    data: {pillar_id: pillarId},
                    dataType: 'json',
                    success: function (response) {
                        initiativeLoader.hide();

                        if (response.success && response.data.length > 0) {
                            let options = '<option value="">Select Initiative</option>';
                            response.data.forEach(function (initiative) {
                                const selected = initiative.id == initialInitiativeId ? 'selected' : '';
                                options += `<option value="${initiative.id}" ${selected}>${initiative.name}</option>`;
                            });
                            initiativeSelect.html(options).prop('disabled', false);

                            // If there was an initial initiative selected, trigger change to load sub-initiatives
                            if (initialInitiativeId) {
                                initiativeSelect.trigger('change');
                            }
                        } else {
                            initiativeSelect.html('<option value="">No initiatives available</option>').prop('disabled', true);
                        }
                    },
                    error: function (xhr) {
                        initiativeLoader.hide();
                        initiativeSelect.html('<option value="">Error loading initiatives</option>').prop('disabled', true);
                        console.error('Error fetching initiatives:', xhr);
                    }
                });
            });

            // When initiative changes, load sub-initiatives
            initiativeSelect.on('change', function () {
                const initiativeId = $(this).val();

                // Reset sub-initiative
                subInitiativeSelect.html('<option value="">Select Sub-Initiative</option>').prop('disabled', true).trigger('change');

                if (!initiativeId) {
                    return;
                }

                // Show loader
                subInitiativeLoader.show();
                subInitiativeSelect.prop('disabled', true);

                // Fetch sub-initiatives
                $.ajax({
                    url: '{{ route("kpis.get-sub-initiatives") }}',
                    type: 'GET',
                    data: {initiative_id: initiativeId},
                    dataType: 'json',
                    success: function (response) {
                        subInitiativeLoader.hide();

                        if (response.success && response.data.length > 0) {
                            let options = '<option value="">Select Sub-Initiative</option>';
                            response.data.forEach(function (subInitiative) {
                                const selected = subInitiative.id == initialSubInitiativeId ? 'selected' : '';
                                options += `<option value="${subInitiative.id}" ${selected}>${subInitiative.name}</option>`;
                            });
                            subInitiativeSelect.html(options).prop('disabled', false);
                        } else {
                            subInitiativeSelect.html('<option value="">No sub-initiatives available</option>').prop('disabled', false);
                        }
                    },
                    error: function (xhr) {
                        subInitiativeLoader.hide();
                        subInitiativeSelect.html('<option value="">Error loading sub-initiatives</option>').prop('disabled', true);
                        console.error('Error fetching sub-initiatives:', xhr);
                    }
                });
            });

            // Trigger initial load if editing existing KPI
             @if(isset($row) && $row->pillar_id)
             pillarSelect.trigger('change');
             @endif

             // Email validation for Requester Email (only on create page)
             @if(!isset($row))
             const submitButton = $('button[type="submit"]');
             const emailFeedback = $('#requester_email_feedback');
             const emailLoader = $('#requester-email-loader');
             const requesterEmailInput = $("#requester_email");
             const kpiForm = requesterEmailInput.closest('form');
             let currentRequest = null;

             // Initial check on page load
             check_requester_email();

             // Check email on input change with debouncing
             let emailTimeout;
             requesterEmailInput.on('paste blur', function () {
                 clearTimeout(emailTimeout);
                 abortCurrentRequest();
                 resetEmailState();

                 emailTimeout = setTimeout(function () {
                     check_requester_email();
                 }, 500);
             });

             if (kpiForm.length) {
                 kpiForm.on('submit', function (event) {
                     clearTimeout(emailTimeout);
                     abortCurrentRequest();
                     resetEmailState();

                     event.preventDefault();

                     const request = check_requester_email({requireEmail: true});

                     if (!request || typeof request.done !== 'function') {
                         return;
                     }

                     request.done(function (data) {
                         if (data && data.valid) {
                             event.currentTarget.submit();
                         }
                     });

                     request.fail(function () {
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
                 emailLoader.hide();
                 requesterEmailInput.prop('disabled', false);
                 emailFeedback.text("");
                 emailFeedback.removeClass('text-success text-danger');
                 requesterEmailInput.removeClass('is-valid is-invalid');
                 submitButton.prop("disabled", true);
             }

             function check_requester_email(options = {}) {
                 const {requireEmail = false} = options;
                 const email = requesterEmailInput.val().trim();

                 if (!email) {
                     resetEmailState();
                     if (requireEmail) {
                         requesterEmailInput.removeClass('is-valid').addClass('is-invalid');
                         emailFeedback.text('Requester email is required');
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
                     requesterEmailInput.addClass('is-invalid');
                     return;
                 }

                 // Start validation process
                 startValidation();

                 // Make AJAX request
                 currentRequest = $.ajax({
                     headers: {
                         'X-CSRF-TOKEN': "{{ csrf_token() }}"
                     },
                     url: '{{ route("kpis.check-requester-email") }}',
                     data: {email: email},
                     dataType: 'JSON',
                     type: 'POST',
                     success: function (data) {
                         currentRequest = null;
                         endValidation();

                         if (data.valid) {
                             submitButton.prop("disabled", false);
                             requesterEmailInput.removeClass('is-invalid');
                             requesterEmailInput.addClass('is-valid');
                             emailFeedback.text(data.message);
                             emailFeedback.removeClass('text-danger');
                             emailFeedback.addClass('text-success');
                         } else {
                             submitButton.prop("disabled", true);
                             requesterEmailInput.removeClass('is-valid');
                             requesterEmailInput.addClass('is-invalid');
                             emailFeedback.text(data.message);
                             emailFeedback.removeClass('text-success');
                             emailFeedback.addClass('text-danger');
                         }
                     },
                     error: function (xhr) {
                         if (xhr.statusText !== 'abort') {
                             currentRequest = null;
                             endValidation();

                             submitButton.prop("disabled", true);
                             requesterEmailInput.removeClass('is-valid');
                             requesterEmailInput.addClass('is-invalid');
                             emailFeedback.text('Error checking email. Please try again.');
                             emailFeedback.removeClass('text-success');
                             emailFeedback.addClass('text-danger');
                         }
                     }
                 });
                 return currentRequest;
             }

             function startValidation() {
                 emailLoader.show();
                 requesterEmailInput.prop('disabled', true);
                 emailFeedback.text("");
                 emailFeedback.removeClass('text-success text-danger');
                 requesterEmailInput.removeClass('is-valid is-invalid');
                 submitButton.prop("disabled", true);
             }

             function endValidation() {
                 emailLoader.hide();
                 requesterEmailInput.prop('disabled', false);
             }
             @endif

             @if(isset($row) && !$isView)
            var kpiId = {{ (int) $row->id }};
            var searchUrl = "{{ route('kpis.search-cr', ['kpi' => $row->id]) }}";
            var attachUrl = "{{ route('kpis.attach-cr', ['kpi' => $row->id]) }}";
            var detachBaseUrl = "{{ url('kpis/'.$row->id.'/detach-cr') }}";
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $('#kpi_cr_search_btn').on('click', function () {
                var crNo = $('#kpi_cr_no').val().trim();
                $('#kpi_cr_search_message').text('');
                $('#kpi_cr_search_result').hide();

                if (!crNo) {
                    $('#kpi_cr_search_message').text('Please enter a CR number.');
                    return;
                }

                // Show loading state
                var btn = $(this);
                btn.addClass('spinner spinner-white spinner-right').prop('disabled', true);

                $.get(searchUrl, {cr_no: crNo})
                    .done(function (response) {
                        btn.removeClass('spinner spinner-white spinner-right').prop('disabled', false);

                        if (!response.success) {
                            $('#kpi_cr_search_message').text(response.message || 'Unable to find Change Request.');
                            return;
                        }

                        $('#kpi_cr_result_no').text(response.data.cr_no);
                        $('#kpi_cr_result_title').text(response.data.title);
                        $('#kpi_cr_result_status').text(response.data.status || '-');
                        $('#kpi_cr_result_link').attr('href', response.data.show_url || '#');
                        $('#kpi_cr_attach_btn').data('cr-no', response.data.cr_no);

                        if (response.already_linked) {
                            $('#kpi_cr_search_message').text('This Change Request is already linked to this KPI.');
                            $('#kpi_cr_attach_btn').hide();
                        } else {
                            $('#kpi_cr_attach_btn').show();
                        }

                        $('#kpi_cr_search_result').slideDown();
                    })
                    .fail(function (xhr) {
                        btn.removeClass('spinner spinner-white spinner-right').prop('disabled', false);
                        var msg = 'Error searching for Change Request.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        $('#kpi_cr_search_message').text(msg);
                    });
            });

            $('#kpi_cr_attach_btn').on('click', function () {
                var crNo = $(this).data('cr-no');
                if (!crNo) return;

                var btn = $(this);
                btn.addClass('spinner spinner-white spinner-right').prop('disabled', true);

                $.ajax({
                    url: attachUrl,
                    type: 'POST',
                    data: {
                        cr_no: crNo,
                        _csrf: csrfToken,
                    },
                    headers: {'X-CSRF-TOKEN': csrfToken}
                })
                    .done(function (response) {
                        btn.removeClass('spinner spinner-white spinner-right').prop('disabled', false);
                        $('#kpi_cr_search_result').hide();
                        $('#kpi_cr_no').val('');

                        if (!response.success) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Unable to link Change Request.'
                            });
                            return;
                        }

                        // Add row to table
                        var cr = response.cr;
                        if (cr) {
                            $('.no-records').remove();
                            var existingRow = $('#kpi_cr_table_body').find('tr[data-cr-id="' + cr.id + '"]');
                            if (existingRow.length === 0) {
                                var statusText = (cr.CurrentRequestStatuses && cr.CurrentRequestStatuses.status) ? cr.CurrentRequestStatuses.status.status_name : (cr.status_name || '-');
                                var workflowText = (cr.workflow_type && cr.workflow_type.name) ? cr.workflow_type.name : (cr.workflowType && cr.workflowType.name ? cr.workflowType.name : '');

                                var newRow = `
                                <tr data-cr-id="${cr.id}">
                                    <td class="pl-0 font-weight-bolder">${cr.cr_no}</td>
                                    <td><a href="${response.show_url || '#'}" target="_blank" class="text-dark-75 text-hover-primary font-weight-bold">${cr.title || ''}</a></td>
                                    <td><span class="label label-lg label-light-info label-inline font-weight-bold">${statusText}</span></td>
                                    <td>${workflowText}</td>
                                    <td class="text-right pr-0">
                                        <button type="button" class="btn btn-icon btn-light-danger btn-sm js-detach-cr" data-cr-id="${cr.id}" title="Remove">
                                            <i class="flaticon2-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                                $('#kpi_cr_table_body').append(newRow);
                            }
                        }

                        if (response.kpi_status) {
                            // Update status badge if needed (requires page reload or complex DOM manipulation, simple reload for now or just alert)
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload(); // Reload to update status badge
                            });
                        }
                    })
                    .fail(function (xhr) {
                        btn.removeClass('spinner spinner-white spinner-right').prop('disabled', false);
                        var msg = 'Error linking Change Request.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    });
            });

            $('#kpi_cr_table_body').on('click', '.js-detach-cr', function () {
                var crId = $(this).data('cr-id');
                var row = $(this).closest('tr');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to unlink this Change Request.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, unlink it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: detachBaseUrl + '/' + crId,
                            type: 'DELETE',
                            headers: {'X-CSRF-TOKEN': csrfToken}
                        })
                            .done(function (response) {
                                if (!response.success) {
                                    Swal.fire('Error', response.message || 'Unable to remove Change Request.', 'error');
                                    return;
                                }
                                row.remove();
                                if ($('#kpi_cr_table_body tr').length === 0) {
                                    $('#kpi_cr_table_body').append('<tr class="no-records"><td colspan="5" class="text-center text-muted font-weight-bold py-5">No Change Requests linked to this KPI.</td></tr>');
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Unlinked!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload(); // Reload to update status badge
                                });
                            })
                            .fail(function (xhr) {
                                Swal.fire('Error', 'Error removing Change Request.', 'error');
                            });
                    }
                });
            });
            @endif
        });
    </script>
@endpush
