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
                                                        class="form-control form-control-solid advanced_search_field select2"
                                                        id="{{ $renderName }}"
                                                        name="{{ $renderName }}[]"
                                                        multiple
                                                        data-placeholder="Select {{ $renderLabel }}"
                                                        style="width:100%;"
                                                    >
                                                        <!-- options generated below -->
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

                                                        @if($customField->name === 'tester_id')
                                                            @foreach ($testing_users as $testing_user)
                                                                <option value="{{ $testing_user->id }}">{{ $testing_user->user_name }}</option>
                                                            @endforeach
                                                        @endif

                                                        @if($customField->name === 'designer_id')
                                                            @foreach ($sa_users as $sa_user)
                                                                <option value="{{ $sa_user->id }}">{{ $sa_user->user_name }}</option>
                                                            @endforeach
                                                        @endif

                                                        @if($customField->name === 'developer_id')
                                                            @foreach ($developer_users as $developer_user)
                                                                <option value="{{ $developer_user->id }}">{{ $developer_user->user_name }}</option>
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
                                            <div id="created_at_error" class="invalid-feedback d-none"></div>
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
                                            <div id="updated_at_error" class="invalid-feedback d-none"></div>
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
@push('css')
    {{--Avoid horizontal scrollbars when Select2 opens--}}
    <style>
        html,body{overflow-x:hidden}
        .select2-container{max-width:100%}
        .select2-dropdown{max-width:100vw;overflow-x:hidden}
    </style>
@endpush
@push('script')
<script>
    function checkFields(form) {
        var  inputs = $('.advanced_search_field');
        var filled = inputs.filter(function(){
            return $(this).val()  !== "";
        });
        return filled.length !== 0;
    }
</script>
<script>
    // Avoid horizontal scrollbars when Select2 opens
    $(function(){
        if ($.fn.select2) {
            $('.select2').each(function(){
                var $el = $(this);
                $el.select2({
                    placeholder: $el.data('placeholder') || 'Select',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#advanced_search')
                });
            });
        }

        function clearDateValidation(ids){
            ids.forEach(function(id){
                var $i = $('#'+id);
                $i.removeClass('is-invalid');
            });
        }

        function setError(elId, message){
            var $el = $('#'+elId);
            if(!$el.length) return;
            if(message){
                $el.text(message).removeClass('d-none');
            } else {
                $el.text('').addClass('d-none');
            }
        }

        function markInvalid(ids){
            ids.forEach(function(id){
                var $i = $('#'+id);
                $i.addClass('is-invalid');
            });
        }

        function validateRange(startId, endId, label, errorElId){
            var s = $('#'+startId).val();
            var e = $('#'+endId).val();
            clearDateValidation([startId, endId]);
            setError(errorElId, '');
            if (!s || !e) return true; // only validate when both filled
            var sd = new Date(s);
            var ed = new Date(e);
            if (isNaN(sd.getTime()) || isNaN(ed.getTime())) return true;
            if (sd.getTime() > ed.getTime()){
                var msg = label + ' range is invalid: start must be before or equal to end.';
                if (window.toastr && toastr.error){ toastr.error(msg); }
                else { alert(msg); }
                markInvalid([startId, endId]);
                setError(errorElId, msg);
                return false;
            }
            return true;
        }

        function syncBounds(startId, endId){
            var s = $('#'+startId).val();
            var e = $('#'+endId).val();
            // Set native constraints
            if (s){ $('#'+endId).attr('min', s); } else { $('#'+endId).removeAttr('min'); }
            if (e){ $('#'+startId).attr('max', e); } else { $('#'+startId).removeAttr('max'); }
        }

        $('#advanced_search').on('submit', function(e){
            var ok1 = validateRange('created_at_start','created_at_end','Creation Date','created_at_error');
            var ok2 = validateRange('updated_at_start','updated_at_end','Updated Date','updated_at_error');
            if (!(ok1 && ok2)){
                e.preventDefault();
            }
        });

        // Real-time validation on input
        $('#created_at_start, #created_at_end').on('change input', function(){
            syncBounds('created_at_start','created_at_end');
            validateRange('created_at_start','created_at_end','Creation Date','created_at_error');
        });
        $('#updated_at_start, #updated_at_end').on('change input', function(){
            syncBounds('updated_at_start','updated_at_end');
            validateRange('updated_at_start','updated_at_end','Updated Date','updated_at_error');
        });

        // Hydrate constraints on load
        syncBounds('created_at_start','created_at_end');
        syncBounds('updated_at_start','updated_at_end');

        $('#reset_advanced_search').on('click', function(){
            var $form = $('#advanced_search');
            if ($form.length && $form[0]) {
                $form[0].reset();
            }
            // Reset Select2 fields explicitly
            $form.find('select.select2').val(null).trigger('change');
            // Clear date errors
            setError('created_at_error','');
            setError('updated_at_error','');
            clearDateValidation(['created_at_start','created_at_end','updated_at_start','updated_at_end']);
            // Clear constraints
            $('#created_at_start, #created_at_end, #updated_at_start, #updated_at_end').removeAttr('min').removeAttr('max');
        });
    });
</script>
@endpush
