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
                            <th>Man Day</th>
                            <th>Group</th>
                            <th>User</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($man_days as $index => $value)
                            <tr>
                                <td>{{ $value->man_day }}</td>
                                <td>{{ $value->group->title }}</td>
                                <td>{{ $value->user->user_name }}</td>
                                <td>{{ $value->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif
    @endcan
</div>