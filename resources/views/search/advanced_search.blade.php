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
                    <h2 class="text-white font-weight-bold my-2 mr-5">{{ $title }}</h2>
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
                            <h3 class="card-title">Add {{ $form_title }}</h3>
                        </div>
                        <!--begin::Form-->
                        <form  id="advanced_search" action="{{ route('advanced.search.result') }}">
                          

                            @if (count($fields) > 0)
                                <div class="form-group row p-3">
                                    @foreach ($fields as $field)
                                        @if (isset($field->custom_field))
                                            @php
                                                $customField = $field->custom_field;
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
                                                        @if($customField->name=="new_status_id")
                                                        
                                                        @foreach ($statuses as $value)
                                                            <option value="{{ $value->id }}">{{ $value->status_name }}</option>
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

                                <button type="submit" class="btn btn-primary">Search</button>
                            @else
                                <p>No fields available for search.</p>
                            @endif
                        </form>
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
    function checkFields(form) {
        var  inputs = $('.advanced_search_field'); 
        var filled = inputs.filter(function(){
            return $(this).val()  !== "";
        });
        if(filled.length === 0) {
            return false;
        }
        return true;
    }
    /*$(function(){
        $('#advanced_search').on('submit',function(e){
            var oneFilled = checkFields($(this));
            if(oneFilled) {
                $(this).submit();
            } else {
                e.preventDefault();
                toastr.error('NO FIELDS FILLED OUT!');
            }
        });
    }); */
/* var hasInput=false;
    $("#advanced_search").on("submit", function(){
        if(!hasInput){
    //Code: Action (like ajax...)return false;
            event.preventDefault();
            
            $('.advanced_search_field').each(function () {
                    if($(this).val()  !== ""){
                        hasInput=true;
                    }
                });
            if(!hasInput){
                alert("Please fill out or select at least one field before submitting.");
            }
            else{
            $("#advanced_search").submit();
            } 
        }
        else{
            $("#advanced_search").submit();
        }        
 });*/

</script>
@endpush
