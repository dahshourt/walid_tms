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
                    <h2 class="text-white font-weight-bold my-2 mr-5">Advanced Search</h2>
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
                        <form  id="advanced_search">
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
                                                // Defaults for rendering
                                                $renderName = $customField->name;
                                                $renderLabel = $customField->label;
                                                // Remap CR ID -> CR No. with input name cr_no
                                                if ($labelLower === 'cr id' || strtolower($customField->name) === 'cr_id') {
                                                    $renderName = 'cr_no';
                                                    $renderLabel = 'CR No.';
                                                }

                                                if (in_array($customField->name, ['created_at', 'updated_at'])) {
                                                    $renderLabel = $customField->name === 'created_at' ? 'Creation Date' :'Updated Date';
                                                }

                                            @endphp

                                            <div @class([
                                                    'form-group',
                                                    $fieldClasses,
                                                    'col-sm-6 date-range-group' => in_array($customField->name, ['created_at', 'updated_at']),
                                                ])>
                                                <label class="{{ in_array($customField->name, ['created_at','updated_at']) ? 'w-100 text-center mb-2' : '' }}" for="{{ $renderName }}">{{ $renderLabel }}</label>

                                                @if (in_array($customField->name, ['created_at', 'updated_at']))
                                                    <div class="p-3 border rounded bg-white shadow-none">
                                                        <div class="d-flex flex-nowrap align-items-center">
                                                            <input
                                                                type="date"
                                                                class="form-control form-control-solid advanced_search_field w-50"
                                                                id="{{ $customField->name }}_start"
                                                                name="{{ $customField->name }}_start"
                                                                placeholder="Start date"
                                                                value="{{ request()->query($customField->name . '_start') }}"
                                                            >
                                                            <span class="mx-2 text-muted">to</span>
                                                            <input
                                                                type="date"
                                                                class="form-control form-control-solid advanced_search_field w-50"
                                                                id="{{ $customField->name }}_end"
                                                                name="{{ $customField->name }}_end"
                                                                placeholder="End date"
                                                                value="{{ request()->query($customField->name . '_end') }}"
                                                            >
                                                        </div>
                                                    </div>
                                                    <div id="updated_at_error" class="invalid-feedback d-none"></div>
                                                @elseif ($customField->type == 'select')
                                                    <select
                                                        class="form-control form-control-solid advanced_search_field select2"
                                                        id="{{ $renderName }}"
                                                        name="{{ $renderName }}[]"
                                                        multiple
                                                        data-placeholder="Select {{ $renderLabel }}"
                                                        style="width:100%;"
                                                    >
                                                    @php
                                                        $selectedValuesRaw = request()->query($renderName, []);
                                                        if (! is_array($selectedValuesRaw)) {
                                                            $selectedValuesRaw = strlen((string) $selectedValuesRaw) ? explode(',', (string) $selectedValuesRaw) : [];
                                                        }
                                                        $selectedValues = array_map('strval', $selectedValuesRaw);
                                                    @endphp
                                                    <!-- options generated below -->
                                                    @if($customField->name=="new_status_id")
                                                        @foreach ($statuses as $value)
                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->status_name }}</option>
                                                        @endforeach
                                                    @endif

                                                    @if($customField->name=="priority_id")
                                                        @foreach ($priorities as $value)
                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                        @endforeach
                                                    @endif


                                                    @if($customField->name=="application_id")
                                                        @foreach ($applications as $value)
                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                        @endforeach
                                                    @endif


                                                    @if($customField->name=="parent_id")
                                                        @foreach ($parents as $value)
                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                        @endforeach
                                                    @endif

                                                    @if($customField->name=="cr_type")
                                                        @foreach ($cr_types as $value)
                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                        @endforeach
                                                    @endif

                                                    @if($customField->name=="category_id")
                                                        @foreach ($categories as $value)
                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                        @endforeach
                                                    @endif
                                                    @if($customField->name=="unit_id")
                                                        @foreach ($units as $value)
                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                        @endforeach
                                                    @endif
                                                    @if($customField->name=="workflow_type_id")
                                                        @foreach ($workflows as $value)
                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                        @endforeach
                                                    @endif

                                                    @if($customField->name === 'tester_id')
                                                        @foreach ($testing_users as $testing_user)
                                                            @php $isSelected = in_array((string) $testing_user->id, $selectedValues, true); @endphp
                                                            <option value="{{ $testing_user->id }}" @if($isSelected) selected @endif>{{ $testing_user->user_name }}</option>
                                                        @endforeach
                                                    @endif

                                                    @if($customField->name === 'designer_id')
                                                        @foreach ($sa_users as $sa_user)
                                                            @php $isSelected = in_array((string) $sa_user->id, $selectedValues, true); @endphp
                                                            <option value="{{ $sa_user->id }}" @if($isSelected) selected @endif>{{ $sa_user->user_name }}</option>
                                                        @endforeach
                                                    @endif

                                                    @if($customField->name === 'developer_id')
                                                        @foreach ($developer_users as $developer_user)
                                                            @php $isSelected = in_array((string) $developer_user->id, $selectedValues, true); @endphp
                                                            <option value="{{ $developer_user->id }}" @if($isSelected) selected @endif>{{ $developer_user->user_name }}</option>
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
                                                    >{{ request()->query($renderName) }}</textarea>
                                                @elseif ($customField->type == 'text' || $customField->type == 'input')

                                                                @php $isCrIdField = in_array(strtolower($customField->name), ['cr_id','id']) || ($labelLower === 'cr id'); @endphp
                                                                <input
                                                                    type="{{ $isCrIdField ? 'number' : 'text' }}"
                                                                    class="form-control form-control-solid advanced_search_field"
                                                                    id="{{ $renderName }}"
                                                                    name="{{ $renderName }}"
                                                                    placeholder="{{ $renderLabel }}"
                                                                    value="{{ request()->query($renderName) }}"
                                                                >


                                                @elseif ($customField->type == 'number')
                                                    <input
                                                        type="number"
                                                        class="form-control form-control-solid advanced_search_field"
                                                        id="{{ $renderName }}"
                                                        name="{{ $renderName }}"
                                                        placeholder="{{ $renderLabel }}"
                                                        value="{{ request()->query($renderName) }}"
                                                    >
                                                @elseif ($customField->type == 'date')
                                                    <input
                                                        type="date"
                                                        class="form-control form-control-solid advanced_search_field"
                                                        id="{{ $customField->name }}"
                                                        name="{{ $customField->name }}"
                                                        value="{{ request()->query($customField->name) }}"
                                                    >
                                                @elseif ($customField->type == 'checkbox')
                                                    <div class="checkbox-inline">
                                                        <label class="checkbox checkbox-outline checkbox-success">
                                                            <input
                                                                type="checkbox"
                                                                name="{{ $renderName }}"
                                                                value="1"
                                                                {{ request()->query($renderName) == '1' ? 'checked' : '' }}
                                                            >
                                                            <span></span>
                                                        </label>
                                                    </div>
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

<div class="container" id="results">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Advanced Search Results</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <h2 class="mb-0 mr-4">Search Result</h2>
                        <form action="{{ route('advanced.search.export', request()->query()) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success font-weight-bolder">
                                <span class="svg-icon svg-icon-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M7,18 L17,18 C18.1045695,18 19,18.8954305 19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 C5,18.8954305 5.8954305,18 7,18 Z M7,20 L17,20 C17.5522847,20 18,20.4477153 18,21 C18,21.5522847 17.5522847,22 17,22 L7,22 C6.44771525,22 6,21.5522847 6,21 C6,20.4477153 6.44771525,20 7,20 Z" fill="#000000" fill-rule="nonzero"/>
                                            <path d="M12,2 C12.5522847,2 13,2.44771525 13,3 L13,13.5857864 L15.2928932,11.2928932 C15.6834175,10.9023689 16.3165825,10.9023689 16.7071068,11.2928932 C17.0976311,11.6834175 17.0976311,12.3165825 16.7071068,12.7071068 L12.7071068,16.7071068 C12.3165825,17.0976311 11.6834175,17.0976311 11.2928932,16.7071068 L7.29289322,12.7071068 C6.90236893,12.3165825 6.90236893,11.6834175 7.29289322,11.2928932 C7.68341751,10.9023689 8.31658249,10.9023689 8.70710678,11.2928932 L11,13.5857864 L11,3 C11,2.44771525 11.4477153,2 12,2 Z" fill="#000000"/>
                                        </g>
                                    </svg>
                                </span>
                                Export Table
                            </button>
                        </form>
                    </div>
                    <p class="mb-4 text-primary fw-bold">Total Results: {{ $totalCount }}</p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>CR ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Release</th>
                                <th>Current Status</th>
                                <th>On Behalf</th>
                                <th>Cr Type</th>
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
                            @foreach ($collection as $item)
                                <tr>
                                    <td>
                                        @can('Edit ChangeRequest')
                                            <a href='{{ route('change_request.edit', $item->id) }}'>{{ $item['cr_no'] }}</a>
                                        @endcan
                                    </td>
                                    <td>{{ $item['title'] ?? "" }}</td>
                                    <td>{{ $item['category']['name'] ?? "" }}</td>
                                    <td>{{ $item['release']['name'] ?? "" }}</td>
                                    <td>
                                        @php
                                            if ($item->isOnHold()) {
                                                $statuses_names = ['On Hold'];
                                            } elseif ($item->isDependencyHold()) {
                                                $blockingCrs = $item->getBlockingCrNumbers();
                                                $crList = !empty($blockingCrs) ? ' (CR#' . implode(', CR#', $blockingCrs) . ')' : '';
                                                $statuses_names = ['Design Estimation - Pending Dependency' . $crList];
                                            } else {
                                                $statuses_names = $item->RequestStatuses->pluck('status.name');
                                            }
                                        @endphp
                                        <div class="d-flex flex-wrap align-items-center" style="gap: 0.4rem;">
                                            @forelse ($statuses_names as $statusName)
                                                <span class="label label-lg label-light-primary label-inline text-dark px-4 py-2"
                                                      style="white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; font-weight: 400;">
                                                    {{ $statusName }}
                                                </span>
                                            @empty
                                                <span class="text-muted">—</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td>{{ $item['on_behalf'] ?? "" }}</td>
                                    <td>{{ $item['cr_type_name'] ?? "" }}</td>
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
                                            <a href='{{ route('change_request.show', $item->id) }}' class="btn btn-sm btn-clean btn-icon mr-2" title="Show details">
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
                                                <a href='{{ route('change_request.show', $item->id) }}/edit' class="btn btn-sm btn-clean btn-icon mr-2" title="Edit details">
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
                        <p class="mb-0 text-primary fw-bold"></p>
                        <div>{{ $collection->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
    @if(count(request()->query()))
        $('html, body').animate({
            scrollTop: $('#results').offset().top - 200
        }, 800);
    @endif
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
            $form.find('input[type="text"], input[type="number"], input[type="date"], input[type="email"], input[type="search"], textarea').val('').trigger('change');
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
