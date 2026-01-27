<?php
$technical_feedback = $cr->change_request_custom_fields->where('custom_field_name', 'technical_feedback')->sortByDesc('updated_at');
$business_feedback = $cr->change_request_custom_fields->where('custom_field_name', 'business_feedback')->sortByDesc('updated_at');
 ?>
<div class="form-group col-md-12" style="float:left">
    @can('View Technical Feedback')
        @if($technical_feedback->count() > 0)
            <h5>Technichal Feedback</h5>
            <table class="table table-bordered">
                <thead>
                    <tr class="text-center">
                        <th>Feedback</th>
                        <th>Updated By</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($technical_feedback as $index => $feedback)
                        <tr>
                            <td>{{ $feedback->custom_field_value }}</td>
                            <td>{{ optional($feedback->user)->user_name ?? 'N/A' }}</td>
                            <td>{{ $feedback->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endcan
    @can('View Business Feedback')
        @if($business_feedback->count() > 0)
            <h5>Business Feedback</h5>
            <table class="table table-bordered">
                <thead>
                    <tr class="text-center">
                        <th>Feedback</th>
                        <th>Updated By</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($business_feedback as $index => $feedback)
                        <tr>
                            <td>{{ $feedback->custom_field_value }}</td>
                            <td>{{ optional($feedback->user)->user_name ?? 'N/A' }}</td>
                            <td>{{ $feedback->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endcan
    @can('mds_logs')
        @if(in_array($cr->current_status->status->status_name, config('change_request.man_days_status.name')))
            @if($man_days && count($man_days) > 0)
                <h5>Man Days Logs</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th>Group</th>
                            <th>User</th>
                            <th>Man Day</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Created At</th>
                            @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Update MDs'))
                                <th>Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($man_days as $index => $value)
                            <tr>
                                <td>{{ $value->group->title }}</td>
                                <td>{{ $value->user->user_name }}</td>
                                <td>{{ $value->man_day }}</td>
                                <td>{{ $value->start_date ? $value->start_date->toDateString() : null }}</td>
                                <td>{{ $value->end_date ? $value->end_date->toDateString() : null }}</td>
                                <td>{{ $value->created_at }}</td>
                                @can('Update MDs')
                                    <td>
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#edit-start-date-modal-{{ $value->id }}" data-bs-toggle="modal"
                                            data-bs-target="#edit-start-date-modal-{{ $value->id }}">Edit Start Date</button>

                                        @include('change_request.partials.edit-start-date-modal', ['value' => $value])


                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif
    @endcan
</div>

@push('script')
    <script>
        $(document).ready(function () {
            $('.update-start-date-form-ajax').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var id = form.data('id');
                var startDate = form.find('input[name="start_date"]').val();
                var modalId = '#edit-start-date-modal-' + id;
                var submitBtn = form.find('button[type="submit"]');
                var originalBtnText = submitBtn.html();

                // Show loading state
                submitBtn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

                $.ajax({
                    url: "{{ route('change-requests.man-days.update') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        start_date: startDate
                    },
                    success: function (response) {
                        if (response.success) {
                            $(modalId).modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function (xhr) {
                        submitBtn.attr('disabled', false).html(originalBtnText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON.message || 'An error occurred',
                        });
                    }
                });
            });
        });
    </script>
@endpush