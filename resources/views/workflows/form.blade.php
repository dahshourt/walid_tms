<div class="card-body">
    @if($errors->any())
        <div class="m-alert m-alert--icon alert alert-danger" role="alert" id="m_form_1_msg">
            <div class="m-alert__icon">
                <i class="la la-warning"></i>
            </div>
            <div class="m-alert__text">
                There are some errors
            </div>
            <div class="m-alert__close">
                <button type="button" class="close" data-close="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="form-group form-group-last"></div>
    
    <!-- Section: Workflow Configuration -->
    <div class="form-section-title">
        <i class="la la-cogs"></i> Workflow Configuration
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group modern-form-group">
                <label for="type_id">Type <span class="text-danger">*</span></label>
                <select class="form-control modern-form-control" id="type_id" name="type_id" {{ isset($row) ? "disabled" : "" }}>
                    <option value="">Select Type</option>
                    @foreach($types as $item)
                        <option value="{{ $item->id }}" @if(isset($row) && $row->type_id == $item->id) selected
                        @elseif(old('type_id') == $item->id) selected @endif>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('type_id', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group modern-form-group">
                <label>Options</label>
                <div class="mt-2">
                    <label class="modern-checkbox">
                        <input type="checkbox" name="workflow_type" value="1" {{ isset($row) && $row->workflow_type == 1 ? "checked" : "" }}>
                        <span class="checkmark"></span>
                        <span class="ml-2">Is Special?</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Status Transition -->
    <div class="form-section-title">
        <i class="la la-exchange"></i> Status Transition
    </div>

    <div class="form-group modern-form-group">
         <div class="mb-2">
            <label class="modern-checkbox">
                <input type="checkbox" id="same_time_from" name="same_time_from">
                <span class="checkmark"></span>
                <span class="ml-2">From At the same time</span>
            </label>
        </div>
    </div>

    <span id="load_from_status">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group modern-form-group">
                    <label for="previous_status_id">Previous Status</label>
                    <select class="form-control modern-form-control" id="previous_status_id" name="previous_status_id">
                        <option value="">Select Previous Status</option>
                        @foreach($statuses as $item)
                            <option value="{{ $item->id }}" {{ isset($row) && $row->previous_status_id == $item->id ? "selected" : "" }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    {!! $errors->first('previous_status_id', '<span class="form-control-feedback">:message</span>') !!}
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group modern-form-group">
                    <label for="from_status_id">From Status <span class="text-danger">*</span></label>
                    <select class="form-control modern-form-control" id="from_status_id" name="from_status_id">
                        <option value="">Select From Status</option>
                        @foreach($statuses as $item)
                            <option value="{{ $item->id }}" @if(isset($row) && $row->from_status_id == $item->id) selected
                            @elseif(old('from_status_id') == $item->id) selected @endif>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    {!! $errors->first('from_status_id', '<span class="form-control-feedback">:message</span>') !!}
                </div>
            </div>
        </div>
    </span>

    <div class="form-group modern-form-group">
        <div class="mb-2">
            <label class="modern-checkbox">
                <input type="checkbox" id="same_time" name="same_time" value="1" {{ isset($row) && $row->same_time == 1 ? "checked" : "" }}>
                <span class="checkmark"></span>
                <span class="ml-2">At the same time (target)</span>
            </label>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group modern-form-group">
                <label for="to_status_id">To Status <span class="text-danger">*</span></label>
                <!--<div class="same_class">-->
                <select class="form-control modern-form-control" id="to_status_id" name="to_status_id[]" multiple="multiple">
                    <option value="">Select To Status</option>
                    @foreach($statuses as $key => $item)
                        <option value="{{ $item->id }}" @if(isset($row)) @foreach($row->workflowstatus as $it)
                            @if($it->to_status_id == $item->id) {{'Selected'}} @else {{''}} @endif @endforeach
                        @elseif(collect(old('to_status_id', []))->contains($item->id)) selected @endif>
                            {{ $item->name}}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('to_status_id', '<span class="form-control-feedback">:message</span>') !!}
                <!--</div>-->
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group modern-form-group">
                <label>To Status Label</label>
                <input type="text" class="form-control modern-form-control" placeholder="To Status Label" name="to_status_lable"
                    value="{{ isset($row) ? $row->to_status_label : old('to_status_lable') }}" />
                {!! $errors->first('to_status_lable', '<span class="form-control-feedback">:message</span>') !!}
            </div>
        </div>
    </div>

    <!-- Section: Settings -->
    <div class="form-section-title">
        <i class="la la-toggle-on"></i> Settings
    </div>
    
    @if(isset($row))
        @foreach($row->workflowstatus as $itm)
            @php
                $res = $itm['default_to_status'];
            @endphp
        @endforeach
    @endif
    
    <div class="row">
        <div class="col-md-6">
             <div class="form-group modern-form-group">
                <label style="display:block; marginBottom: 5px;">Default Status</label>
                <div class="mb-2">
                    <label class="modern-checkbox">
                        <input type="checkbox" name="default_status" value="1" @if(isset($res) && $res == 1) checked @endif>
                        <span class="checkmark"></span>
                        <span class="ml-2">Yes</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group modern-form-group">
                <label style="display:block; margin-bottom: 15px;">Active <span class="text-danger">*</span></label>
                <div>
                    <label class="modern-toggle-switch">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" name="active" value="1" {{ (isset($row) && $row->active == 1) || old('active', 0) == 1 ? "checked" : "" }}>
                        <span class="modern-toggle-slider"></span>
                    </label>
                    <span class="toggle-label text-muted">Enable this workflow</span>
                </div>
            </div>
        </div>
    </div>


</div>

@push('script')
    <script>
        $(document).ready(function () {
            $("#same_time_from").change(function () {
                ChangeFromSameSelect();
            });
            $("#type_id").change(function () {
                ChangeFromSameSelect();
            });
            /*  $("#same_time").change(function() {
                 if (this.checked) {
                     $(".not_same_class").remove();
                     $(".same_class").show();
                     $(".same_time").hide();
                 } else {
                     $(".same_class").hide();

                     // Recreate the .not_same_class div
                     const notSameClassDiv = `
                         <div class="form-group not_same_class">
                             <select class="form-control form-control-lg" id="to_status_id" name="to_status_id">
                                 <option value="">Select</option>
    @foreach($statuses as $item)
        <option value="{{ $item->id }}" {{ isset($row) && $row->to_status_id == $item->id ? "selected" : "" }}>
                                    {{ $item->name }}
        </option>
    @endforeach
                    </select >
                {!! $errors->first('to_status_id', '<span class="form-control-feedback">:message</span>') !!}
                    </div >
                `;

                // Append the recreated div to the form
                $(".form-group.same_class").after(notSameClassDiv);
            }
        }); */

                });

                function ChangeFromSameSelect() {
                    var same_time_from = 0;
                    $("#load_from_status").empty();
                    if ($('#same_time_from').is(':checked')) var same_time_from = 1;
                    $.get("{{url('workflow/same/from/status')}}", {
                        same_time_from: same_time_from,
                        type_id: $("#type_id").val()
                    })
                        .done(function (data) {
                            $("#load_from_status").html(data);
                            $('#from_previous_status_id').select2({
                                placeholder: "Select status/statuses",
                            });
                            $('#from_status_id').select2({
                                placeholder: "Select status",
                            });
                            $('#previous_status_id').select2({
                                placeholder: "Select status",
                            });
                        });
                }


                document.addEventListener('DOMContentLoaded', function () {
                    const sameTimeCheckbox = document.getElementById('same_time');

                    // Function to handle the checkbox state
                    function handleCheckboxChange() {
                        if (sameTimeCheckbox.checked) {
                            // Remove elements with the class "not_same_class"
                            document.querySelectorAll('.not_same_class').forEach(function (element) {
                                element.remove();
                            });

                            // Show elements with the class "same_class"
                            document.querySelectorAll('.same_class').forEach(function (element) {
                                element.style.display = 'block';
                            });
                        } else {
                            // If the checkbox is unchecked, do nothing or add your own logic here if needed
                        }
                    }

                    // Check the state on page load
                    handleCheckboxChange();

                    // Attach the event listener to handle changes
                    sameTimeCheckbox.addEventListener('change', handleCheckboxChange);
                });

    </script>
@endpush