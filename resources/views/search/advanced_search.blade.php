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
                                    @php $createdField = null; $updatedField = null; @endphp
                                    @foreach ($fields as $field)
                                        @if (isset($field->custom_field))
                                            @php
                                                $customField = $field->custom_field;
                                                $fieldClasses = isset($field->styleClasses) ? $field->styleClasses : 'col-sm-3 field-select';
                                                $labelLower = isset($customField->label) ? strtolower(trim($customField->label)) : '';
                                                // Skip deprecated standalone date filters
                                                if (in_array($labelLower, ['less than date','greater than date'])) {
                                                    continue;
                                                }
                                                // Widen date range groups to allow two inputs inline
                                                if (in_array($customField->name, ['created_at', 'updated_at'])) {
                                                    // Capture these to render later with desired order and labels
                                                    if ($customField->name === 'created_at') { $createdField = $field; }
                                                    if ($customField->name === 'updated_at') { $updatedField = $field; }
                                                    continue;
                                                }
                                                // Defaults for rendering
                                                $renderName = $customField->name;
                                                $renderLabel = $customField->label;
                                                // Remap CR ID -> CR No. with input name cr_no
                                                if ($labelLower === 'cr id' || strtolower($customField->name) === 'cr_id') {
                                                    $renderName = 'cr_no';
                                                    $renderLabel = 'CR No.';
                                                }
                                            @endphp

                                            <div class="{{ 'form-group ' . $fieldClasses . (in_array($customField->name, ['created_at','updated_at']) ? ' p-3 border rounded shadow-sm' : '') }}">
                                                <label class="{{ in_array($customField->name, ['created_at','updated_at']) ? 'w-100 text-center' : '' }}" for="{{ $renderName }}">{{ $renderLabel }}</label>

                                                @if (in_array($customField->name, ['created_at', 'updated_at']))
                                                    <div class="d-flex flex-nowrap align-items-center">
                                                        <input
                                                            type="date"
                                                            class="form-control form-control-solid advanced_search_field w-50"
                                                            id="{{ $customField->name }}_start"
                                                            name="{{ $customField->name }}_start"
                                                            placeholder="Start date"
                                                            value="{{ old($customField->name . '_start') }}"
                                                        >
                                                        <span class="mx-2 text-muted">to</span>
                                                        <input
                                                            type="date"
                                                            class="form-control form-control-solid advanced_search_field w-50"
                                                            id="{{ $customField->name }}_end"
                                                            name="{{ $customField->name }}_end"
                                                            placeholder="End date"
                                                            value="{{ old($customField->name . '_end') }}"
                                                        >
                                                    </div>
                                                @elseif ($customField->type == 'select')
                                                    <select
                                                        class="form-control form-control-solid advanced_search_field {{ in_array($customField->name, ['new_status_id','application_id']) ? 'select2' : '' }}"
                                                        id="{{ $renderName }}"
                                                        name="{{ $renderName }}{{ in_array($customField->name, ['new_status_id','application_id']) ? '[]' : '' }}"
                                                        {{ in_array($customField->name, ['new_status_id','application_id']) ? 'multiple' : '' }}
                                                        {{ $customField->name === 'new_status_id' ? 'data-placeholder=\'Select CR status\'' : '' }}
                                                        {{ $customField->name === 'application_id' ? 'data-placeholder=\'Select Target System\'' : '' }}
                                                        style="width:100%;"
                                                    >
                                                        @if(!in_array($customField->name, ['new_status_id','application_id']))
                                                            <option value="">Select {{ $customField->label }}</option>
                                                        @endif
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
                                                        class="form-control form-control-solid advanced_search_field"
                                                        id="{{ $renderName }}"
                                                        name="{{ $renderName }}"
                                                        placeholder="{{ $renderLabel }}"
                                                        rows="4"
                                                    >{{ old($renderName) }}</textarea>
                                                @elseif ($customField->type == 'text' || $customField->type == 'input')
                                                           
                                                                @php $isCrIdField = in_array(strtolower($customField->name), ['cr_id','id']) || ($labelLower === 'cr id'); @endphp
                                                                <input
                                                                    type="{{ $isCrIdField ? 'number' : 'text' }}"
                                                                    class="form-control form-control-solid advanced_search_field"
                                                                    id="{{ $renderName }}"
                                                                    name="{{ $renderName }}"
                                                                    placeholder="{{ $renderLabel }}"
                                                                    value="{{ old($renderName) }}"
                                                                >
                                                         
                                                    
                                                @elseif ($customField->type == 'number')
                                                    <input
                                                        type="number"
                                                        class="form-control form-control-solid advanced_search_field"
                                                        id="{{ $renderName }}"
                                                        name="{{ $renderName }}"
                                                        placeholder="{{ $renderLabel }}"
                                                        value="{{ old($renderName) }}"
                                                    >
                                                @elseif ($customField->type == 'date')
                                                    <input
                                                        type="date"
                                                        class="form-control form-control-solid advanced_search_field"
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
                                    @if ($createdField)
                                        @php $customField = $createdField->custom_field; @endphp
                                        <div class="form-group col-sm-6 field-select p-3 border rounded shadow-sm">
                                            <label class="w-100 text-center" for="{{ $customField->name }}">Creation Date</label>
                                            <div class="d-flex flex-nowrap align-items-center">
                                                <input
                                                    type="date"
                                                    class="form-control form-control-solid advanced_search_field w-50"
                                                    id="{{ $customField->name }}_start"
                                                    name="{{ $customField->name }}_start"
                                                    placeholder="Start date"
                                                    value="{{ old($customField->name . '_start') }}"
                                                >
                                                <span class="mx-2 text-muted">to</span>
                                                <input
                                                    type="date"
                                                    class="form-control form-control-solid advanced_search_field w-50"
                                                    id="{{ $customField->name }}_end"
                                                    name="{{ $customField->name }}_end"
                                                    placeholder="End date"
                                                    value="{{ old($customField->name . '_end') }}"
                                                >
                                            </div>
                                        </div>
                                    @endif
                                    @if ($updatedField)
                                        @php $customField = $updatedField->custom_field; @endphp
                                        <div class="form-group col-sm-6 field-select p-3 border rounded shadow-sm">
                                            <label class="w-100 text-center" for="{{ $customField->name }}">Updated Date</label>
                                            <div class="d-flex flex-nowrap align-items-center">
                                                <input
                                                    type="date"
                                                    class="form-control form-control-solid advanced_search_field w-50"
                                                    id="{{ $customField->name }}_start"
                                                    name="{{ $customField->name }}_start"
                                                    placeholder="Start date"
                                                    value="{{ old($customField->name . '_start') }}"
                                                >
                                                <span class="mx-2 text-muted">to</span>
                                                <input
                                                    type="date"
                                                    class="form-control form-control-solid advanced_search_field w-50"
                                                    id="{{ $customField->name }}_end"
                                                    name="{{ $customField->name }}_end"
                                                    placeholder="End date"
                                                    value="{{ old($customField->name . '_end') }}"
                                                >
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="px-3 pb-3 w-100 d-flex justify-content-end">
                                    <button type="button" id="reset_advanced_search" class="btn btn-secondary mr-3">Clear</button>
                                    <button type="submit" class="btn btn-primary px-6">Search</button>
                                </div>
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
@push('script')
<script>
    // Avoid horizontal scrollbars when Select2 opens
    (function(){
        var css = '.select2-container{max-width:100%} .select2-dropdown{max-width:100vw}';
        var style = document.createElement('style');
        style.type = 'text/css';
        style.appendChild(document.createTextNode(css));
        document.head.appendChild(style);
    })();
    $(function(){
        if ($.fn.select2) {
            var $status = $('#new_status_id');
            if ($status.length) {
                $status.select2({
                    placeholder: 'Select CR status',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#advanced_search'),
                    multiple: true
                });
                // Set old values if any
                @if(old('new_status_id'))
                    $status.val(@json(old('new_status_id'))).trigger('change');
                @endif
            }
            var $application = $('#application_id');
            if ($application.length) {
                $application.select2({
                    placeholder: 'Select Target System',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#advanced_search'),
                    multiple: true
                });
                // Set old values if any
                @if(old('application_id'))
                    $application.val(@json(old('application_id'))).trigger('change');
                @endif
            }
        }
        $('#reset_advanced_search').on('click', function(){
            var $form = $('#advanced_search');
            if ($form.length && $form[0]) {
                $form[0].reset();
            }
            // Reset Select2 fields explicitly
            $form.find('select.select2').val(null).trigger('change');
        });
    });
</script>
@endpush
