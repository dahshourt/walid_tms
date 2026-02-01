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
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card card-custom gutter-b">
                        <div class="card-header border-0 py-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label font-weight-bolder text-dark">Update Defect For CR#{{$defect_data->change_request->cr_no}}</span>
                                <span class="text-muted mt-3 font-weight-bold font-size-sm">Update the details below</span>
                            </h3>
                        </div>
                        
                        <form class="form" action='{{URL("defect_update/")}}/{{$defect_data->id}}' method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            @method('PATCH')
                           
                            <input type="hidden" name="cr_id" value="{{$defect_data->cr_id}}">
                            
                            <div class="card-body py-0">
                                <div class="row">
                                @foreach ($CustomFields as $field)
                                 
                                        @if (isset($field->CustomField))
                                            @php
                                                $customField = $field->CustomField;
                                                // Default to col-md-4
                                                $fieldClasses = isset($field->styleClasses) ? $field->styleClasses : 'col-md-4';
                                                if(strpos($fieldClasses, 'col-') === false) {
                                                    $fieldClasses .= ' col-md-4';
                                                }
                                            @endphp

                                            <div class="{{ $fieldClasses }} mb-6 modern-form-group">
                                                    
                                                <label for="{{ $customField->name }}" class="font-weight-bolder text-dark">{{ $customField->label }}
                                                    @if(isset($customField->required) && $customField->required)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                
                                                @if ($customField->type == 'select')
                                                
                                                    <select
                                                        class="form-control modern-form-control form-control-lg"
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
                                                        class="form-control modern-form-control form-control-lg"
                                                        id="{{ $customField->name }}"
                                                        name="{{ $customField->name }}"
                                                        placeholder="{{ $customField->label }}"
                                                        rows="4"
                                                    >{{ old($customField->name) }}</textarea>
                                                @elseif ($customField->type == 'text' || $customField->type == 'input')
                                                            
                                                                <input
                                                                    type="text"
                                                                    class="form-control modern-form-control form-control-lg"
                                                                    id="{{ $customField->name }}"
                                                                    name="{{ $customField->name }}"
                                                                    placeholder="{{ $customField->label }}"
                                                                    value="{{  $defect_data->subject}}"
                                                                >
                                                        
                                                    
                                                @elseif ($customField->type == 'number')
                                                    <input
                                                        type="number"
                                                        class="form-control modern-form-control form-control-lg"
                                                        id="{{ $customField->name }}"
                                                        name="{{ $customField->name }}"
                                                        placeholder="{{ $customField->label }}"
                                                        value="{{ old($customField->name) }}"
                                                    >
                                                @elseif ($customField->type == 'date')
                                                    <input
                                                        type="date"
                                                        class="form-control modern-form-control form-control-lg"
                                                        id="{{ $customField->name }}"
                                                        name="{{ $customField->name }}"
                                                        value="{{ old($customField->name) }}"
                                                    >
                                                @elseif ($customField->type == 'file')
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="{{ $customField->name }}" name="{{ $customField->name }}[]">
                                                        <label class="custom-file-label modern-form-control form-control-lg" style="height: auto; align-content: center;" for="{{ $customField->name }}">Choose file</label>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="col-12">
                                                <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
                                                    <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                                    <div class="alert-text">Custom field data is not available.</div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <div class="card-footer bg-white border-top-0 pt-0 pb-10 text-right">
                                <button type="submit" class="btn btn-success font-weight-bolder px-9 py-4 shadow-sm hover-elevate-up mr-2">Update Defect</button>
                                <button type="button" id="openModal" class="btn btn-primary font-weight-bolder px-9 py-4 shadow-sm hover-elevate-up">View History Logs</button>
                            </div>

                            @if(count($defect_comments) > 0)
                            <div class="separator separator-solid my-10"></div>
                            <div class="card-body pt-0">
                                <div class="">
                                    <h4 class="font-weight-bolder text-dark mb-4">Defect Comments</h4>

                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center table-head-bg table-borderless">
                                            <thead>
                                                <tr class="text-uppercase">
                                                    <th style="min-width: 150px" class="pl-7"><span class="text-dark-75">User Name</span></th>
                                                    <th style="min-width: 400px">Comment</th>
                                                    <th style="min-width: 150px">Created At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($defect_comments as $comment)
                                                    <tr>
                                                        <td class="pl-7 py-5">
                                                            <div class="d-flex align-items-center">
                                                                <div class="symbol symbol-40 flex-shrink-0 mr-3">
                                                                    <div class="symbol-label bg-light-primary text-primary font-weight-bold">
                                                                        {{ substr($comment->user->name, 0, 1) }}
                                                                    </div>
                                                                </div>
                                                                <div class="text-dark-75 font-weight-bolder font-size-lg mb-0">{{ $comment->user->name }}</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="text-dark-75 font-weight-500 d-block font-size-lg">{{ $comment->comment }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="text-dark-75 font-weight-500 d-block font-size-lg">{{ $comment->created_at->format('Y-m-d H:i:s') }}</span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted">No comments found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(count($defect_attachments) > 0)
                            <div class="separator separator-solid my-10"></div>
                            <div class="card-body pt-0">
                                <div class="">
                                    <h4 class="font-weight-bolder text-dark mb-4">Defect Attachments</h4>

                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center table-head-bg table-borderless">
                                            <thead>
                                                <tr class="text-uppercase">
                                                    <th style="min-width: 150px" class="pl-7"><span class="text-dark-75">User Name</span></th>
                                                    <th style="min-width: 250px">Files</th>
                                                    <th style="min-width: 150px">Created At</th>
                                                    <th style="min-width: 100px">Download</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($defect_attachments as $value)
                                                    <tr>
                                                        <td class="pl-7 py-5">
                                                            <div class="d-flex align-items-center">
                                                                <div class="symbol symbol-40 flex-shrink-0 mr-3">
                                                                    <div class="symbol-label bg-light-info text-info font-weight-bold">
                                                                        {{ substr($value->user->name, 0, 1) }}
                                                                    </div>
                                                                </div>
                                                                <div class="text-dark-75 font-weight-bolder font-size-lg mb-0">{{ $value->user->name }}</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="text-dark-75 font-weight-500 d-block font-size-lg">{{ $value->file }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="text-dark-75 font-weight-500 d-block font-size-lg">{{ $value->created_at->format('Y-m-d H:i:s') }}</span>
                                                        </td>
                                                        <td> <a href="{{url('defect/files/download/')}}/{{$value->id}}" class="btn btn-icon btn-light btn-hover-primary btn-sm"><i class="flaticon2-download"></i></a> </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">No Files found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
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

        if(btn) {
            btn.onclick = function () {
                modal.style.display = "block";
            };
        }

        if(closeBtn){
            closeBtn.onclick = function () {
                modal.style.display = "none";
            };
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };
    </script>
@endpush