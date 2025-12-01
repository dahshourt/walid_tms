@if(count($cr['attachments'])  > 0  )
        <div class="form-group col-md-12" style="float:left">
        @can('View Technichal Attachments')
        @if(count($cr['attachments']->where('flag', 1))  > 0  )
        <h5>Technichal Attachments</h5>
        <table class="table table-bordered">
            <thead>
                <tr class="text-center">
                    <th>#</th>
                    <th>File Name</th>
                    <th>User Name</th>
                    <th>Uploaded At</th>
                    <th>File Size (MB)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach ($cr['attachments'] as $key => $file)
                @if ($file->flag == 1)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $file->file }}</td>
                    <td>{{ $file->user->user_name }} ({{ $file->user->defualt_group->title }})</td>
                    <td>{{ $file->created_at }}</td>
                    <td>
                        @if (isset($file->size)) <!-- Ensure the file size is available -->
                        {{ round($file->size / 1024) }} KB
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('files.download', $file->id) }}" class="btn btn-light btn-sm">
                            Download
                        </a>
                        @if($file->user->id == \Auth::user()->id || \Auth::user()->hasRole('Super Admin'))
                        <a href="{{ route('files.delete', $file->id) }}" class="btn btn-danger btn-sm">
                            Delete
                        </a>
                        @endif
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        @endif
        @endcan
        @can('View Business Attachments')
        @if(count($cr['attachments']->where('flag', 2))  > 0  )
        <h5>Business Attachments</h5>
        <table class="table table-bordered">
            <thead>
                <tr class="text-center">
                    <th>#</th>
                    <th>File Name</th>
                    <th>User Name</th>
                    <th>Uploaded At</th>
                    <th>File Size (MB)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach ($cr['attachments'] as $key => $file)
                @if ($file->flag == 2)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $file->file }}</td>
                    <td>{{ $file->user->user_name }} ({{ $file->user->defualt_group->title }})</td>
                    <td>{{ $file->created_at }}</td>
                    <td>
                        @if (isset($file->size)) <!-- Ensure the file size is available -->
                        {{ round($file->size / 1024) }} KB
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('files.download', $file->id) }}" class="btn btn-light btn-sm">
                            Download
                        </a>
                        @if($file->user->id == \Auth::user()->id || \Auth::user()->hasRole('Super Admin'))
                        <a href="{{ route('files.delete', $file->id) }}" class="btn btn-danger btn-sm">
                            Delete
                        </a> 
                        @endif
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        @endif
        @endcan

        </div>
        @endif
