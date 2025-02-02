@extends('layouts.app')

@section('content')

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid p-3" id="kt_content">
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
                            <h3 class="card-title">Manage Custom fields - Create CR</h3>
                        </div>
                        <!--begin::Form-->
                        <div class="card-body">
                        <form class="form" action="{{ route('custom.fields.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="form_type" value="1">
                            <!-- Workflow Type Selection -->
                            <div class="form-group">
                                <label for="wf_type_id">Work Flow Type:</label>
                                <select id="wf_type_id" name="wf_type_id" class="form-control" required>
                                <option value="" disabled selected hidden>Select an option</option>
   
                                @foreach($wf_type_name as $type)
                                        <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Custom Fields Container -->
                            <div id="custom-fields-container">
                                <!-- Custom fields will be loaded here dynamically -->
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-success mr-2">Submit</button>
                               
                            </div>
                        </form>
                        </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
       
        function loadCustomFields(wf_type_id) {
            $.ajax({
                url: '{{ url("customs/field/group/type/selected/1") }}',
                method: 'GET',
                data: {
                    by: "wf_type_id" ,
                    value:wf_type_id
                },
                success: function(response) {
                    $('#custom-fields-container').html(response);
                }
            });
        }

        $('#wf_type_id').change(function() {
            var wf_type_id = $(this).val();
            loadCustomFields(wf_type_id);
        });

        // Load custom fields for the initial selected workflow type
       // loadCustomFields($('#wf_type_id').val());
    });
</script>

@endsection
