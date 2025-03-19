@extends('layouts.app')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex flex-column">
                    <h2 class="text-white font-weight-bold my-2 mr-5">{{ $title }}</h2>
                </div>
            </div>
        </div>
    </div>
    <!--end::Subheader-->

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom gutter-b example example-compact">
                        <div class="card-header">
                            <h3 class="card-title">Update Defect For CR#_{{$defect_data->cr_id}}</h3>
                        </div>
                        
                        <form class="form" action='{{URL("defect_update/")}}/{{$defect_data->id}}' method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            @method('PATCH')
                           
                            <input type="hidden" name="cr_id" value="{{$defect_data->cr_id}}">
                            
                            <div class="card-body">
                                <div class="form-group row">
                                @foreach ($CustomFields as $field)
                                 
                                        @if (isset($field->CustomField))
                                            @php
                                                $customField = $field->CustomField;
                                                $fieldClasses = isset($field->styleClasses) ? $field->styleClasses : 'col-sm-3 field-select';
                                            @endphp

                                            <div class="form-group {{ $fieldClasses }}">
                                                    
                                                <label for="{{ $customField->name }}">{{ $customField->label }}</label>
                                                
                                                @if ($customField->type == 'select')
                                                
                                                    <select
                                                        class="form-control advanced_search_field"
                                                        id="{{ $customField->name }}"
                                                        name="{{ $customField->name }}"
                                                    >
                                                        <option value="">Select {{ $customField->label }}</option>
                                                        <!-- Fetch options from related table or predefined options -->
                                                        @if($customField->name=="defect_status")
                                                            @foreach ($defect_status as $value)
                                                                <option value="{{ $value->id }}" @if($value->id == $defect_data->status_id) selected @endif>{{ $value->status_name }}</option>
                                                            @endforeach
                                                        @endif

                                                        @if($customField->name=="technical_team")
                                                        
                                                        @foreach ($technical_team as $value)
                                                            <option value="{{ $value->id }}"  @if($value->id == $defect_data->group_id) selected @endif>{{ $value->name }}</option>
                                                        @endforeach
                                                        @endif


                                                    </select>
                                                @elseif ($customField->type == 'textArea')
                                                    <textarea
                                                        class="form-control advanced_search_field"
                                                        id="{{ $customField->name }}"
                                                        name="{{ $customField->name }}"
                                                        placeholder="{{ $customField->label }}"
                                                        rows="4"
                                                    >{{ old($customField->name) }}</textarea>
                                                @elseif ($customField->type == 'text' || $customField->type == 'input')
                                                            
                                                                <input
                                                                    type="text"
                                                                    class="form-control advanced_search_field"
                                                                    id="{{ $customField->name }}"
                                                                    name="{{ $customField->name }}"
                                                                    placeholder="{{ $customField->label }}"
                                                                    value="{{  $defect_data->subject}}"
                                                                >
                                                        
                                                    
                                                @elseif ($customField->type == 'number')
                                                    <input
                                                        type="number"
                                                        class="form-control advanced_search_field"
                                                        id="{{ $customField->name }}"
                                                        name="{{ $customField->name }}"
                                                        placeholder="{{ $customField->label }}"
                                                        value="{{ old($customField->name) }}"
                                                    >
                                                @elseif ($customField->type == 'date')
                                                    <input
                                                        type="date"
                                                        class="form-control advanced_search_field"
                                                        id="{{ $customField->name }}"
                                                        name="{{ $customField->name }}"
                                                        value="{{ old($customField->name) }}"
                                                    >
                                                @elseif ($customField->type == 'file')
                                                    <input
                                                        type="file"
                                                        class="form-control advanced_search_field"
                                                        id="{{ $customField->name }}"
                                                        name="{{ $customField->name }}[]"
                                                        value="{{ old($customField->name) }}"
                                                    >
                                                @endif
                                                


                                                @if(isset($customField->required) && $customField->required)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </div>
                                        @else
                                            <p>Custom field data is not available.</p>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="form-group col-md-12">
                                    
                                </div>
                            </div>
                            <div class="card-footer text-left">
                                <button type="submit" class="btn btn-success mr-2">Update Defect</button>
                                <button type="button" id="openModal" class="btn btn-primary">View History Logs</button>
                            </div>
                            @if(count($defect_comments) > 0)
                            <div class="card-footer">
                                <div class="container mt-4">
                                    <h2 class="mb-3">Defect Comments</h2>

                                    <table class="table table-striped table-hover table-bordered">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">User Name</th>
                                                <th scope="col">Comment</th>
                                                <th scope="col">Created At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($defect_comments as $comment)
                                                <tr>
                                                    <td>{{ $comment->user->name }}</td>
                                                    <td>{{ $comment->comment }}</td>
                                                    <td>{{ $comment->created_at->format('Y-m-d H:i:s') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">No comments found.</td>
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
                            @if(count($defect_attachments) > 0)
                            <div class="card-footer">
                                <div class="container mt-4">
                                    <h2 class="mb-3">Defect Attachments</h2>

                                    <table class="table table-striped table-hover table-bordered">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">User Name</th>
                                                <th scope="col">Files</th>
                                                <th scope="col">Created At</th>
                                                <th scope="col">Download</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($defect_attachments as $value)
                                                <tr>
                                                    <td>{{ $value->user->name }}</td>
                                                    <td>{{ $value->file }}</td>
                                                    <td>{{ $value->created_at->format('Y-m-d H:i:s') }}</td>
                                                    <td> <a href="{{url('defect/files/download/')}}/{{$value->id}}">Download </a> </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">No Files found.</td>
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
                            @if(count($all_defects) > 0)
                            <div class="card-footer">
                                <div class="container mt-4">
                                    <h2 class="mb-3">Defects Related CR#_{{$defect_data->cr_id}}</h2>

                                    <table class="table table-striped table-hover table-bordered">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">Status</th>
                                                <th scope="col">Subject</th>
                                                <th scope="col">Created At</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($all_defects as $value)
                                                <tr>
                                                    <td>{{ $value->current_status->status_name }}</td>
                                                    <td>{{ $value->subject }}</td>
                                                    <td>{{ $value->created_at->format('Y-m-d H:i:s') }}</td>
                                                    <td> <a href="{{url('edit_defect')}}/{{$value->id}}">Edit </a> </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">No Defects found.</td>
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
                            @include("defect.cr_logs")
                            
                        </form>
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('script')
    <script>
        var modal = document.getElementById("modal");
        var btn = document.getElementById("openModal");
        var closeBtn = document.getElementById("close_logs");

        btn.onclick = function () {
            modal.style.display = "block";
        };

        closeBtn.onclick = function () {
            modal.style.display = "none";
        };

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };
    </script>
@endpush