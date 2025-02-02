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
                    <h2 class="text-white font-weight-bold my-2 mr-5">Welcome, {{ auth()->user()->name }}</h2>
                    <div class="text-white font-weight-bold my-2 mr-5" style="opacity: 0.9;">
                        <i class="fas fa-clock mr-2"></i> Last login: {{ auth()->user()->last_login }}
                    </div>
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
            <!--begin::Card-->
            <div class="card card-custom card-stretch example example-compact row" id="kt_page_stretched_card">
                <div class="card-body">
                    <!-- <h2>Welcome back IN Dashboard! You are logged in with group:</h2> -->
                    <div class="card card-custom card-stretch example example-compact" id="kt_page_stretched_card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center gap-2">
                                <!-- <h2 class="mb-2">
                                    You are logged in as
                                    <span>
                                        @if(session()->has('default_group'))
                                            {{ session('default_group_name') }}
                                        @else
                                            @if(auth()->user()->default_group)
                                                {{ auth()->user()?->default_group?->name ?? 'No group assigned' }}
                                            @endif
                                        @endif
                                    </span>
                                </h2> -->

                                <!-- Cards Section -->
                                <div class="row">
                                    

                                         <div class="col-xl-12">
                                            <!--begin::Card-->
                                            <div class="card shadow-sm">
                                                <!--begin::Card Body-->
                                                <div class="card-body p-4">
                                                    <form class="form" action='{{url("$route")}}' method="post" enctype="multipart/form-data">
                                                        {{ csrf_field() }}
                                                        <div class="row">
                                                            <!-- Workflow Type Dropdown -->
                                                            <div class="col-xl-6 form-group mb-4">
                                                                <label for="workflow_id" class="form-label font-weight-bold">WorkFlow Type</label>
                                                                <select class="form-control form-control-lg" id="workflow_id" name="workflow">
                                                                    <option value="" disabled selected>Select Workflow Type...</option>
                                                                    @foreach($workflow_type as $item)
                                                                        @if($item->name != "On Going")
                                                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <!-- Status Dropdown -->
                                                            <div class="col-xl-6 form-group mb-4">
                                                                <label for="status" class="form-label font-weight-bold">Status</label>
                                                                <select class="form-control form-control-lg" id="status" name="status[]" multiple>
                                                                    <option value="" disabled selected>Select Status...</option>
                                                                    @foreach($statuses as $item)
                                                                        <option value="{{$item->id}}">{{$item->status_name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <!-- Applications Dropdown -->
                                                            <div class="col-12 form-group mb-4">
                                                                <label for="application_ids" class="form-label font-weight-bold">Applications</label>
                                                                <select class="form-control form-control-lg" id="application_ids" name="applications[]" multiple>
                                                                    <option value="" disabled selected>Select Applications...</option>
                                                                    @foreach($applications as $item)
                                                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Hidden Inputs -->
                                                        <input type="hidden" id="crs_group_by_status" value="{{$crs_group_by_status}}" />
                                                        <input type="hidden" id="crs_group_by_applications" value="{{$crs_group_by_applications}}" />

                                                        <!-- Form Footer -->
                                                        <div class="card-footer bg-transparent border-0 pt-3">
                                                            <button type="submit" class="btn btn-success btn-lg w-100">
                                                                <i class="fas fa-search mr-2"></i> Search
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <!--end::Card Body-->
                                            </div>
                                            <!--end::Card-->
                                        </div>


                                    <div class="row">
                                        @if(isset($crs_group_by_applications) && !empty($crs_group_by_applications) )
                                        <div class="col-xl-6 ">
                                            <div class="card card-custom bgi-no-repeat bgi-no-repeat bgi-size-cover gutter-b">
                                                <!--begin::Body-->
                                                <div class="card-body d-flex flex-column align-items-start justify-content-between flex-wrap">
                                                    <div class="p-1 flex-grow-1">
                                                        <h3 class="text-black font-weight-bolder line-height-lg mb-5">Applications</h3>
                                                    </div>
                                                    <canvas id="myChart"></canvas>
                                                </div>
                                                <!--end::Body-->
                                            </div>
                                        </div>
                                        @endif
                                        @if(isset($crs_group_by_status) && !empty($crs_group_by_status) )
                                         <div class="col-xl-6 ">
                                            <div class="card card-custom bgi-no-repeat bgi-no-repeat bgi-size-cover gutter-b">
                                                <!--begin::Body-->
                                                <div class="card-body d-flex flex-column align-items-start justify-content-between flex-wrap">
                                                    <div class="p-1 flex-grow-1">
                                                        <h3 class="text-black font-weight-bolder line-height-lg mb-5">Status</h3>
                                                    </div>
                                                    <canvas id="mysecondChart"></canvas>
                                                </div>
                                                <!--end::Body-->
                                            </div>
                                        </div>
                                        @endif
                                    </div>


                                    
                                </div>
                                <!-- End of Cards Section -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

@endsection

 

@push('script')
<script>
	document.addEventListener('DOMContentLoaded', function () {
      const hiddenArray = document.getElementById('crs_group_by_applications').value;
            const parsedArray = JSON.parse(hiddenArray);

            // Extract application names (labels) and CRs_Count (data) from the parsed array
            const lab  = parsedArray.map(item => item.application_name || 'Unknown');
            const dat = parsedArray.map(item => item.CRs_Count);
             


   const ctx = document.getElementById('myChart');

	  new Chart(ctx, {
	    type: 'pie',
	    data: {
	      labels: lab,
	      datasets: [{
	        label: '# CRs',
	        data: dat,
	        borderWidth: 1
	      }]
	    },
	    options: {
	      scales: {
	        y: {
	          beginAtZero: true
	        }
	      }
	    }
	  });

	  });
	</script>

	<script>
		document.addEventListener('DOMContentLoaded', function () {
      const hiddenArray = document.getElementById('crs_group_by_status').value;
            const parsedArray = JSON.parse(hiddenArray);

            // Extract application names (labels) and CRs_Count (data) from the parsed array
            const lab  = parsedArray.map(item => item.Status_Name || 'Unknown');
            const dat = parsedArray.map(item => item.CRs_Count);
             
   const ctxx = document.getElementById('mysecondChart');

	  new Chart(ctxx, {
	    type: 'pie',
	    data: {
	      labels: lab,
	      datasets: [{
	        label: '# CRs',
	        data: dat,
	        borderWidth: 1
	      }]
	    },
	    options: {
	      scales: {
	        y: {
	          beginAtZero: true
	        }
	      }
	    }
	  });

	   });
	</script>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script>
    $(document).ready(function () {

        $('#workflow_id').on('change', function () {
        
            const selectedId = $(this).val();
            $('#application_ids').empty();

            if (selectedId) {
                $.ajax({
                    url: `{{url("$route_ajax")}}`,
                    method: 'GET',
                    data: {
                            workflow_type_id: selectedId, // Include the selected data
                            _token: '{{ csrf_token() }}'    // Include the CSRF token for security
                        },
                    success: function (data) {
                        if (data.length > 0) {
                            data.forEach(function (item) {
                                $('#application_ids').append(
                                    `<option selected value="${item.id}">${item.name}</option>`
                                );
                            });
                        } else {
                            $('#application_ids').append('<option>No options available</option>');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }
        });
    });
</script>
@endpush
