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
                            <h3 class="card-title">Create Defect For CR#_{{$id}}</h3>
                        </div>
                        
                        <form class="form" action='{{URL("store_defect/")}}' method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            
                           
                            <input type="hidden" name="cr_id" value="{{$id}}">
                            
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
                                                                <option value="{{ $value->id }}">{{ $value->status_name }}</option>
                                                            @endforeach
                                                        @endif

                                                        @if($customField->name=="technical_team")
                                                        
                                                        @foreach ($technical_team as $value)
                                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                        @endforeach
                                                        @endif

                                                        @if($customField->name=="priority_id")
                                                        
                                                        @foreach ($priorities as $value)
                                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                        @endforeach
                                                        @endif


                                                        @if($customField->name=="application_id")
                                                        
                                                        @foreach ($applications as $value)
                                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                        @endforeach
                                                        @endif


                                                        @if($customField->name=="parent_id")
                                                        
                                                        @foreach ($parents as $value)
                                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                        @endforeach
                                                        @endif

                                                        @if($customField->name=="category_id")
                                                        
                                                        @foreach ($categories as $value)
                                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                        @endforeach
                                                        @endif
                                                        @if($customField->name=="unit_id")
                                                        
                                                        @foreach ($units as $value)
                                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                        @endforeach
                                                        @endif
                                                        @if($customField->name=="workflow_type_id")
                                                        
                                                        @foreach ($workflows as $value)
                                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
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
                                                                    value="{{ old($customField->name) }}"
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
                                <button type="submit" class="btn btn-success mr-2">Add Defect</button>
                                <!-- @can('Show CR Logs')
                                    <button type="button" id="openModal" class="btn btn-primary">View History Logs</button>
                                @endcan -->
                            </div>
                        </form>
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </div>

</div>
@endsection