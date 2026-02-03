@if(count($all_defects) > 0 && $workflow_type_id == 9)
    <div class="card-footer">
        <div class="container mt-4">
            <h2 class="mb-3">CR Defects</h2>

            <table class="table table-striped table-hover table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">User Name</th>
                        <th scope="col">Defect Name</th>
                        <th scope="col">Group</th>
                        <th scope="col">Status</th>
                        <th scope="col">Created At </th>
                        <th scope="col">Action </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($all_defects as $value)

                        <tr>
                            <td>{{ $value->User_created->user_name }}</td>
                            <td>{{ $value->subject }}</td>
                            <td>{{ $value?->assigned_team?->title }}</td>
                            <td>{{ $value->current_status?->status_name }}</td>
                            <td>{{ $value->created_at->format('Y-m-d H:i:s') }}</td>
                            <td> <a href="{{url('edit_defect')}}/{{$value->id}}">Edit </a> </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No Defects Found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination (If Needed) -->
            <div class="d-flex justify-content-center">

            </div>
        </div>
    </div>
@endif