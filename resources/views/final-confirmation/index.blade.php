@extends('layouts.app')

@section('content')

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Heading-->
                <div class="d-flex flex-column">
                    <!--begin::Title-->
                    <h2 class="text-white font-weight-bold my-2 mr-5">{{ $title ?? 'Final Confirmation' }}</h2>
                    <!--end::Title-->
                </div>
                <!--end::Heading-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b example example-compact">
                        <div class="card-header">
                            <h3 class="card-title">{{ $form_title ?? 'Final CR Confirmation' }}</h3>
                        </div>
                        <!--begin::Form-->
                        <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($errors->has('technical_feedback'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Technical Feedback Error:</strong> {{ $errors->first('technical_feedback') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                            <!-- Search Section -->
                            <form id="searchForm" class="form" method="GET" action="{{ route('final_confirmation.index') }}">
                                <div class="form-group row">
                                    <label class="col-3 col-form-label">CR Number</label>
                                    <div class="col-9">
                                        <div class="input-group">
                                            <input type="text"
                                                   class="form-control"
                                                   id="search_cr_number"
                                                   name="cr_no"
                                                   placeholder="Enter CR Number"
                                                   value="{{ $searchCrNumber ?? old('cr_no') }}"
                                                   required>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-search"></i>
                                                    Search
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @if(isset($searchError))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ $searchError }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if(isset($crDetails))
                                <!-- CR Details Table -->
                                <div class="separator separator-dashed my-7">
                                    <div class="separator-label">
                                        <span class="bg-white px-3">
                                            <i class="fas fa-info-circle text-primary"></i>
                                            Change Request Details
                                        </span>
                                    </div>
                                </div>

                                <div class="table-responsive mt-7">
                                    <table class="table table-bordered table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>CR NO.</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="font-weight-bold text-primary">{{ $crDetails->cr_no }}</td>
                                                <td>{{ $crDetails->title }}</td>
                                                <td>{{ Str::limit($crDetails->description) }}</td>
                                                <td>
                                                    <span class="label label-lg label-light-info label-inline">{{ $crDetails->CurrentRequestStatuses->status->status_name ?? 'Unknown' }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $is_disabled = $crDetails->isAlreadyCancelledOrRejected() || $crDetails->inFinalState();
                                                    @endphp
                                                    <form method="POST" action="{{ route('final_confirmation.submit') }}" class="d-inline" id="finalConfirmationForm">
                                                        @csrf
                                                        <input type="hidden" name="cr_number" value="{{ $crDetails->cr_no}}">
                                                        <input type="hidden" name="action" id="actionInput" value="">
                                                        <input type="hidden" name="technical_feedback" id="technicalFeedbackInput" value="">

                                                        <button @if($is_disabled) disabled @endif type="button" class="btn btn-danger btn-sm mr-2 final-confirmation-btn"
                                                                data-action="reject" data-status-id="{{ config('change_request.status_ids.Reject') }}" data-cr-no="{{ $crDetails->cr_no}}">
                                                            <i class="fas fa-ban"></i>
                                                            Reject
                                                        </button>

                                                        <button @if($is_disabled) disabled @endif type="button" class="btn btn-warning btn-sm final-confirmation-btn"
                                                                data-action="cancel" data-status-id="{{ config('change_request.status_ids.Cancel') }}" data-cr-no="{{ $crDetails->cr_no}}">
                                                            <i class="fas fa-times"></i>
                                                            Cancel
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                        <!--end::Form-->
                    </div>
                    <!--end::Card-->
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

@endsection

@push('script')
<script>
// Use jQuery since it's available in the global bundle
$(document).ready(function() {
    // Handle final confirmation button clicks
    $('.final-confirmation-btn').on('click', function() {
        console.log('Button clicked!'); // Debug log

        const action = $(this).data('action');
        const statusId = $(this).data('status-id');
        const crNo = $(this).data('cr-no');

        console.log('Action:', action, 'StatusId:', statusId, 'CrNo:', crNo); // Debug log

        // Check if button is disabled
        if ($(this).prop('disabled')) {
            console.log('Button is disabled, returning'); // Debug log
            return;
        }

        // Configure SweetAlert based on action
        const config = {
            reject: {
                title: 'Reject Change Request?',
                text: `Are you sure you want to reject CR #${crNo}?`,
                icon: 'error',
                confirmButtonText: 'Yes, Reject',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            },
            cancel: {
                title: 'Cancel Change Request?',
                text: `Are you sure you want to cancel CR #${crNo}?`,
                icon: 'warning',
                confirmButtonText: 'Yes, Cancel',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d'
            }
        };

        const alertConfig = config[action];

        Swal.fire({
            title: alertConfig.title,
            text: alertConfig.text,
            icon: alertConfig.icon,
            showCancelButton: true,
            confirmButtonColor: alertConfig.confirmButtonColor,
            cancelButtonColor: alertConfig.cancelButtonColor,
            confirmButtonText: alertConfig.confirmButtonText,
            cancelButtonText: 'No, Keep it',
            reverseButtons: true,
            focusCancel: true,
            html: `
                <div class="mb-3">
                    <label for="technical_feedback" class="form-label">Technical Feedback: <span class="text-danger">*</span></label>
                    <textarea 
                        id="technical_feedback" 
                        name="technical_feedback" 
                        class="form-control" 
                        rows="4" 
                        placeholder="Please provide technical feedback for this ${action} action..."
                        style="resize: vertical; min-height: 100px;"
                        required
                    ></textarea>
                </div>
            `,
            preConfirm: () => {
                const technicalFeedback = document.getElementById('technical_feedback').value;
                if (!technicalFeedback.trim()) {
                    Swal.showValidationMessage('Please provide technical feedback');
                    return false;
                }
                return technicalFeedback;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Set the action value and technical feedback, then submit the form
                $('#actionInput').val(statusId);
                $('#technicalFeedbackInput').val(result.value);
                $('#finalConfirmationForm').submit();

                // Show loading alert
                Swal.fire({
                    title: 'Processing...',
                    text: `Processing ${action} request for CR #${crNo}`,
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        });
    });
});
</script>
@endpush

@section('styles')
<style>
.separator-label {
    font-weight: 600;
    font-size: 1.1rem;
}

.separator-dashed {
    border-top: 1px dashed #E4E6EF;
}

.form-control-plaintext {
    padding-left: 0;
    padding-right: 0;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

#crDetailsSection, #confirmationFormSection {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert-warning {
    background-color: #FFF8DD;
    border-color: #FFEB3B;
    color: #8A6914;
}

.alert-icon {
    display: flex;
    align-items: center;
    margin-right: 1rem;
}

.alert-text {
    flex: 1;
}
</style>
@endsection
