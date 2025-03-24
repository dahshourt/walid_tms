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
    <div class="form-group form-group-last"></div>

    <div class="form-group">
        <label>Is Especial?</label>
        <div class="checkbox-inline">
            <label class="checkbox">
                <input type="checkbox" name="workflow_type" value="1" {{ isset($row) && $row->workflow_type == 1 ? "checked" : "" }}>
                <span></span>Yes
            </label>
        </div>
    </div>

    <div class="form-group">
        <label for="type_id">Type:</label>
        <select class="form-control form-control-lg" id="type_id" name="type_id" {{ isset($row) ? "disabled" : "" }}>
            <option value="">Select</option>
            @foreach($types as $item)
                <option value="{{ $item->id }}" {{ isset($row) && $row->type_id == $item->id ? "selected" : "" }}>
                    {{ $item->name }}
                </option>
            @endforeach
        </select>
        {!! $errors->first('type_id', '<span class="form-control-feedback">:message</span>') !!}
    </div>

    <div class="form-group">
		<div class="checkbox-inline mb-10">
			<label class="checkbox">
				<input type="checkbox" id="same_time_from" name="same_time_from">
				<span></span>From At the same time
			</label>
		</div>
	</div>

    <span id="load_from_status">
        <div class="form-group">
            <label for="previous_status_id">Previous Status:</label>
            <select class="form-control form-control-lg" id="previous_status_id" name="previous_status_id">
                <option value="">Select</option>
                @foreach($statuses as $item)
                    <option value="{{ $item->id }}" {{ isset($row) && $row->previous_status_id == $item->id ? "selected" : "" }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('previous_status_id', '<span class="form-control-feedback">:message</span>') !!}
        </div>


        <div class="form-group">
            <label for="from_status_id">From Status:</label>
            <select class="form-control form-control-lg" id="from_status_id" name="from_status_id">
                <option value="">Select</option>
                @foreach($statuses as $item)
                    <option value="{{ $item->id }}" {{ isset($row) && $row->from_status_id == $item->id ? "selected" : "" }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('from_status_id', '<span class="form-control-feedback">:message</span>') !!}
        </div>
    </span>
<div class="form-group">
		<div class="checkbox-inline mb-10">
			<label class="checkbox">
				<input type="checkbox" id="same_time" name="same_time"  value="1" {{ isset($row) && $row->same_time == 1 ? "checked" : "" }}>
				<span></span>At the same time
			</label>
		</div>
		</div>

    <div class="form-group">
        <label for="to_status_id">To Status:</label>
        

        
        <!--<div class="same_class">-->
            <select class="form-control form-control-lg" id="to_status_id" name="to_status_id[]" multiple="multiple">
                <option value="">Select</option>
                @foreach($statuses as $key => $item)
                            

                    <option value="{{ $item->id }}" 
                        @if(isset($row))
                            @foreach($row->workflowstatus as $it)
                                @if($it->to_status_id == $item->id)
                                    {{'Selected'}}
                                @else
                                    {{''}}
                                @endif
                            @endforeach
                        @endif
                        >
                        {{ $item->name}}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('to_status_id', '<span class="form-control-feedback">:message</span>') !!}
        <!--</div>-->
    </div>
    <div class="form-group">
        <label>To Status Label:</label>
        <input type="text" class="form-control form-control-lg" placeholder="To Status Label" name="to_status_lable" 
        value="@if(isset($row)){{$row->to_status_label}}
        @else
            {{old('to_status_lable')}}
        @endif" 
        />
        {!! $errors->first('to_status_lable', '<span class="form-control-feedback">:message</span>') !!}
    </div>
 
    <!-- <div class="form-group not_same_class">
        <select class="form-control form-control-lg " id="to_status_id" name="to_status_id">
            <option value="">Select</option>
            {{ isset($row) && $row->to_status_id == $item->id ? "selected" : "" }}
            @foreach($statuses as $item)

                <option value="{{ $item->id }}"  
                    @if(isset($row))
                         @foreach($row->workflowstatus as $it)
                            @if($it->to_status_id == $item->id)
                                    {{'Selected'}}
                                @else
                                    {{''}}
                                @endif
                         @endforeach
                    @endif
                    >
                    {{ $item->name }}
                </option>
            @endforeach
        </select>
        {!! $errors->first('to_status_id', '<span class="form-control-feedback">:message</span>') !!}
    </div> -->
 @if(isset($row))
 @foreach($row->workflowstatus as $itm)
@php
    $res =  $itm['default_to_status'] ;

@endphp
@endforeach
@endif
    <div class="form-group">
        <label>Default Status</label>
        <div class="checkbox-inline">
            <label class="checkbox">
                <input type="checkbox" name="default_status" value="1" @if(isset($res) && $res == 1 ) checked    @endif >
                <span></span>Yes
            </label>
        </div>
    </div>

    <div class="form-group">
        <label>Active</label>
        <div class="checkbox-inline">
            <label class="checkbox">
                <input type="checkbox" name="active" value="1" {{ isset($row) && $row->active == 1 ? "checked" : "" }}>
                <span></span>Yes
            </label>
        </div>
    </div>
</div>

@push('script')
<script>
    $(document).ready(function() {
        $("#same_time_from").change(function() {
            ChangeFromSameSelect();
        });
        $("#type_id").change(function() {
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
                        </select>
                        {!! $errors->first('to_status_id', '<span class="form-control-feedback">:message</span>') !!}
                    </div>
                `;
                
                // Append the recreated div to the form
                $(".form-group.same_class").after(notSameClassDiv);
            }
        }); */

    });

    function ChangeFromSameSelect(){
        var same_time_from = 0;
        $("#load_from_status").empty();
        if($('#same_time_from').is(':checked')) var same_time_from = 1;
        $.get( "{{url('workflow/same/from/status')}}", { same_time_from: same_time_from, type_id: $("#type_id").val() } )
            .done(function( data ) {
                $("#load_from_status").html(data);
                $('#from_previous_status_id').select2({
        	        placeholder: "Select status/statuses",
                });
        });
    }


document.addEventListener('DOMContentLoaded', function() {
    const sameTimeCheckbox = document.getElementById('same_time');

    // Function to handle the checkbox state
    function handleCheckboxChange() {
        if (sameTimeCheckbox.checked) {
            // Remove elements with the class "not_same_class"
            document.querySelectorAll('.not_same_class').forEach(function(element) {
                element.remove();
            });

            // Show elements with the class "same_class"
            document.querySelectorAll('.same_class').forEach(function(element) {
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