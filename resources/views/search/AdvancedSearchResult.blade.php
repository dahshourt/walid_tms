@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Advanced Search</h3>
                </div>
                <div class="card-body">
                    <h2>Search Result</h2>
                    <div class="card-toolbar">
                        <form action="{{ route('advanced.search.export', request()->query()) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-light-primary font-weight-bolder">
                                <span class="svg-icon svg-icon-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path d="M7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 L5,4 C5,2.8954305 5.8954305,2 7,2 Z" fill="#000000" />
                                            <polygon fill="#000000" opacity="0.3" points="6 8 18 8 18 10 6 10" />
                                        </g>
                                    </svg>
                                </span>
                                Export Table
                            </button>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>CR ID</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Release</th>
                                    <th>Current Status</th>
                                    <th>Requester</th>
                                    <th>Requester Email</th>
                                    <th>Design Duration</th>
                                    <th>Dev Duration</th>
                                    <th>Test Duration</th>
                                    <th>Creation Date</th>
                                    <th>Requesting Department</th>
                                    <th>Targeted System</th>
                                    <th>Last Action Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td>
                                        @can('Edit ChangeRequest')
                                            <a href='{{ url("$route") }}/{{ $item["id"] }}/edit'>{{ $item['cr_no'] }}</a>
                                        @endcan    
                                        </td>
                                        <td>{{ $item['title'] ?? "" }}</td>
                                        <td>{{ $item['category']['name'] ?? "" }}</td>
                                        <td>{{ $item['release']['name'] ?? "" }}</td>
                                        <td>{{ $item->getCurrentStatus()->status->status_name ?? "" }}</td>
                                        <td>{{ $item['requester_name'] ?? "" }}</td>
                                        <td>{{ $item['requester_email'] ?? "" }}</td>
                                        <td>{{ $item['design_duration'] ?? "" }}</td>
                                        <td>{{ $item['develop_duration'] ?? "" }}</td>
                                        <td>{{ $item['test_duration'] ?? "" }}</td>
                                        <td>{{ $item['created_at'] ?? "" }}</td>
                                        <td>{{ $item['department'] ?? "" }}</td>
                                        <td>{{ $item['application']['name'] ?? "" }}</td>
                                        <td>{{ $item['updated_at'] ?? "" }}</td>
                                        <td>
                                            <div class="d-inline-flex">
                                                <a href='{{ url("$route") }}/{{ $item["id"] }}' class="btn btn-sm btn-clean btn-icon mr-2" title="Show details">
                                                    <span class="svg-icon svg-icon-md">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                                <path d="M12,2 C6.477,2 2,6.477 2,12 C2,17.523 6.477,22 12,22 C17.523,22 22,17.523 22,12 C22,6.477 17.523,2 12,2 Z M12,19.5 C7.805,19.5 4.5,16.195 4.5,12 C4.5,7.805 7.805,4.5 12,4.5 C16.195,4.5 19.5,7.805 19.5,12 C19.5,16.195 16.195,19.5 12,19.5 Z M11,16 L13,16 L13,13 L11,13 L11,16 Z M11,11 L13,11 L13,8 L11,8 L11,11 Z" fill="#000000"></path>
                                                            </g>
                                                        </svg>
                                                    </span>
                                                </a>
                                                @if(in_array($item["id"], $crs_in_queues->toArray()) && !(($item["workflow_type_id"] == 5) && in_array($item["new_status_id"], [66, 67, 68, 69])))
                                                    <a href='{{ url("$route") }}/{{ $item["id"] }}/edit' class="btn btn-sm btn-clean btn-icon mr-2" title="Edit details">
                                                        <span class="svg-icon svg-icon-md">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24"></rect>
                                                                    <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero"></path>
                                                                </g>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded shadow-sm">
                        <p class="mb-0 text-primary fw-bold">Total Results: {{ $totalCount }}</p>
                        <div>{{ $items->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
