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
                                    <span class="card-label font-weight-bolder text-dark">Create Defect For
                                        CR#{{$cr->cr_no}}</span>
                                    <span class="text-muted mt-3 font-weight-bold font-size-sm">Fill in the details below to
                                        create a new defect</span>
                                </h3>
                            </div>

                            <form class="form" action='{{URL("store_defect/")}}' method="post"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <input type="hidden" name="cr_id" value="{{$id}}">

                                <div class="card-body py-0">
                                    <div class="row">
                                        @foreach ($CustomFields as $field)
                                            @if (isset($field->CustomField))
                                                @php
                                                    $customField = $field->CustomField;
                                                    // Default to col-md-4 for better modern spacing unless specified
                                                    $fieldClasses = isset($field->styleClasses) ? $field->styleClasses : 'col-md-4';
                                                    // Ensure we don't have conflicting col classes if we want a fresh look, 
                                                    // but respect logic if it was dynamic. For now, assuming standarization:
                                                    if (strpos($fieldClasses, 'col-') === false) {
                                                        $fieldClasses .= ' col-md-4';
                                                    }
                                                @endphp

                                                <div class="{{ $fieldClasses }} mb-6 modern-form-group">
                                                    <label for="{{ $customField->name }}"
                                                        class="font-weight-bolder text-dark">{{ $customField->label }}
                                                        @if(isset($customField->required) && $customField->required)
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </label>

                                                    @if ($customField->type == 'select')
                                                        <select class="form-control modern-form-control form-control-lg"
                                                            id="{{ $customField->name }}" name="{{ $customField->name }}">
                                                            <option value="">Select {{ $customField->label }}</option>

                                                            @if($customField->name == "defect_status")
                                                                @foreach ($defect_status as $value)
                                                                    <option value="{{ $value->id }}">{{ $value->status_name }}</option>
                                                                @endforeach
                                                            @endif

                                                            @if($customField->name == "technical_team")
                                                                @foreach ($technical_team as $value)
                                                                @if(in_array($value->id, $selected_cr_technical_team))
                                                                    <option value="{{ $value->id }}" >
                                                                        {{ $value->name }}</option>
                                                                @endif
                                                                @endforeach
                                                            @endif

                                                                @if($customField->name == "priority_id")
                                                                    @foreach ($priorities as $value)
                                                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                                    @endforeach
                                                                @endif

                                                            @if($customField->name == "application_id")
                                                                @foreach ($applications as $value)
                                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                                @endforeach
                                                            @endif

                                                            @if($customField->name == "parent_id")
                                                                @foreach ($parents as $value)
                                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                                @endforeach
                                                            @endif

                                                            @if($customField->name == "category_id")
                                                                @foreach ($categories as $value)
                                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                                @endforeach
                                                            @endif

                                                            @if($customField->name == "unit_id")
                                                                @foreach ($units as $value)
                                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                                @endforeach
                                                            @endif

                                                            @if($customField->name == "workflow_type_id")
                                                                @foreach ($workflows as $value)
                                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                                @endforeach
                                                            @endif

                                                        </select>
                                                    @elseif ($customField->type == 'textArea')
                                                        <textarea class="form-control modern-form-control form-control-lg"
                                                            id="{{ $customField->name }}" name="{{ $customField->name }}"
                                                            placeholder="{{ $customField->label }}"
                                                            rows="4">{{ old($customField->name) }}</textarea>
                                                    @elseif ($customField->type == 'text' || $customField->type == 'input')
                                                        <input type="text" class="form-control modern-form-control form-control-lg"
                                                            id="{{ $customField->name }}" name="{{ $customField->name }}"
                                                            placeholder="{{ $customField->label }}"
                                                            value="{{ old($customField->name) }}">
                                                    @elseif ($customField->type == 'number')
                                                        <input type="number" class="form-control modern-form-control form-control-lg"
                                                            id="{{ $customField->name }}" name="{{ $customField->name }}"
                                                            placeholder="{{ $customField->label }}"
                                                            value="{{ old($customField->name) }}">
                                                    @elseif ($customField->type == 'date')
                                                        <input type="date" class="form-control modern-form-control form-control-lg"
                                                            id="{{ $customField->name }}" name="{{ $customField->name }}"
                                                            value="{{ old($customField->name) }}">
                                                    @elseif ($customField->type == 'file')
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" id="{{ $customField->name }}"
                                                                name="{{ $customField->name }}[]">
                                                            <label class="custom-file-label modern-form-control form-control-lg"
                                                                style="height: auto; align-content: center;"
                                                                for="{{ $customField->name }}">Choose file</label>
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
                                    <button type="submit"
                                        class="btn btn-primary font-weight-bolder px-9 py-4 shadow-sm hover-elevate-up">Add
                                        Defect</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Entry-->
    </div>
@endsection