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
                    <h2 class="text-white font-weight-bold my-2 mr-5">Dashboard</h2>
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
            <!--begin::Card-->
            <div class="card card-custom card-stretch example example-compact" id="kt_page_stretched_card">
                
            
                <div class="row">
                    <div class="col-lg-6">
						<div class="card card-custom gutter-b">
							<div class="card-header">
								<div class="card-title">
								    <h3 class="card-label">In House Statistics (No of CRs per system) </h3>
							    </div>
							</div>
							<div class="card-body">
                                <canvas id="inhouse-chart" height="200" width="200"></canvas>
						    </div>
					    </div>
										<!--end::Card-->
										
					</div>

                    <div class="col-lg-6">
						<div class="card card-custom gutter-b">
							<div class="card-header">
								<div class="card-title">
								    <h3 class="card-label">Vendor Statistics (No of CRs per system) </h3>
							    </div>
							</div>
							<div class="card-body">
                                <canvas id="vendor-chart" height="200" width="200"></canvas>
						    </div>
					    </div>
										<!--end::Card-->
										
					</div>


				</div>

                <div class="row" >
                    <div class="col-lg-12">
						<div class="card card-custom gutter-b">
							<div class="card-header">
								<div class="card-title">
								    <h3 class="card-label">Status Statistics (No of CRs per Status) </h3>
							    </div>
							</div>
							<div class="card-body">
                                <canvas id="myBarChart" width="400" height="200"></canvas>
						    </div>
					    </div>
										<!--end::Card-->
										
					</div>
				</div>


                <div class="row" >
                    <div class="col-lg-12">
						<div class="card card-custom gutter-b">
							<div class="card-header">
								<div class="card-title">
								    <h3 class="card-label">In House Statistics (No of CRs per Status & system) </h3>
							    </div>
							</div>
							<div class="card-body">
                                <div class="col-md-6 form-group" style="float: right;display:none">
                                    <label for="statusFilter">Select Status:</label>
                                    <select id="statusFilter" class="form-control form-control-lg">
                                        <option value="all">All</option>
                                        @foreach($inhouse_crs_per_status_system as $key=>$item)
                                            <option value="{{ $item->new_status_id }}"> {{ $item->status?$item->status->status_name:'No Name' }} </option>    
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group" style="display:none">
                                    <label for="appFilter">Select App:</label>
                                    <select id="appFilter" class="form-control form-control-lg">
                                        <option value="all">All</option>
                                        @foreach($inhouse_apps as $key=>$item)
                                            <option value="{{ $item->id }}"> {{ $item->name }} </option>    
                                        @endforeach
                                    </select>
                                </div>
                                <canvas id="InHouseStatusChart" width="400" height="200"></canvas>
						    </div>
					    </div>
										<!--end::Card-->
										
					</div>
				</div>

                <div class="row" >
                    <div class="col-lg-12">
						<div class="card card-custom gutter-b">
							<div class="card-header">
								<div class="card-title">
								    <h3 class="card-label">Vendor Statistics (No of CRs per Status & system) </h3>
							    </div>
							</div>
							<div class="card-body">
                                <div class="col-md-6 form-group" style="float: right;display:none">
                                    <label for="statusVendorFilter">Select Status:</label>
                                    <select id="statusVendorFilter" class="form-control form-control-lg">
                                        <option value="all">All</option>
                                        @foreach($vendor_crs_per_status_system as $key=>$item)
                                            <option value="{{ $item->new_status_id }}"> {{ $item->status?$item->status->status_name:'No Name' }} </option>    
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group" style="display:none">
                                    <label for="appVendorFilter">Select App:</label>
                                    <select id="appVendorFilter" class="form-control form-control-lg">
                                        <option value="all">All</option>
                                        @foreach($vendor_apps as $key=>$item)
                                            <option value="{{ $item->id }}"> {{ $item->name }} </option>    
                                        @endforeach
                                    </select>
                                </div>
                                <canvas id="VendorStatusChart" width="400" height="200"></canvas>
						    </div>
					    </div>
										<!--end::Card-->
										
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
    $(document).ready(function () {

     // in house chart   
    // Example data for categories
        const InhouseNames= [];
		const InhouseValues= [];
		@foreach($inhouse_crs as $key=>$item)
            var data_val = "{{ $item->total }}";
			InhouseNames.push("{{ $item->application?$item->application->name:'No Name' }}({{ $item->total }})");
			InhouseValues.push(data_val);
		@endforeach


    // Initialize Chart
    const ctx = document.getElementById("inhouse-chart").getContext("2d");
    let pieChart = new Chart(ctx, {
        type: "pie",
        data: {
            labels: InhouseNames,
            datasets: [
                {
                    data: InhouseValues,
                },
            ],
        },
        options: {
            responsive: true,
        },
    });

    // end of inhouse chart

    // vendor chart
    const VendorNames= [];
    const VendorValues= [];
	@foreach($vendor_crs as $key=>$item)
        var data_val = "{{ $item->total }}";
		VendorNames.push("{{ $item->application?$item->application->name:'No Name' }}({{ $item->total }})");
	    VendorValues.push(data_val);
	@endforeach

    const ctx_vendor = document.getElementById("vendor-chart").getContext("2d");
    let pieChartVendor = new Chart(ctx_vendor, {
        type: "pie",
        data: {
            labels: VendorNames,
            datasets: [
                {
                    data: VendorValues,
                },
            ],
        },
        options: {
            responsive: true,
        },
    });
    //end of vendor chart


    // statuses chart 
    function getRandomColor() { //generates random colours and puts them in string
        const statusColors= [];
        var number_colors = {{ count($status_crs) }};
            for (var i = 0; i < number_colors; i++) {
                var letters = '0123456789ABCDEF'.split('');
                var color = '#';
                for (var x = 0; x < 6; x++ ) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                statusColors.push(color);
            }
        return statusColors;
    } 
    const StatusNames= [];
    const StatusValues= [];
    
	@foreach($status_crs as $key=>$item)
        var data_val = "{{ $item->total }}";
		StatusNames.push("{{ $item->status?$item->status->status_name:'No Name' }}");
	    StatusValues.push(data_val);
	@endforeach
        var status_ctx = $('#myBarChart')[0].getContext('2d');
            
            var chart = new Chart(status_ctx, {
                type: 'bar', // Bar chart
                data: {
                    labels: StatusNames, // X-axis labels
                    datasets: [{
                        label: 'No of CRs per Status',
                        data: StatusValues, // Y-axis data
                        backgroundColor:getRandomColor(),
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
    //end of statuses chart

   
});

</script>

<script>

$(document).ready(function () {

// in house chart   
// Example data for categories
   const InhouseCRsStatusNames= [];
   const InhouseCRsStatusValues= [];
   @foreach($inhouse_crs_per_status_system as $key=>$item)
       var data_val = "{{ $item->total }}";
       InhouseCRsStatusNames.push("{{ $item->status?$item->status->status_name:'No Name' }}");
       InhouseCRsStatusValues.push(data_val);
   @endforeach


    // Initialize Chart
    const ctx = document.getElementById("InHouseStatusChart").getContext("2d");
    let pieChart = new Chart(ctx, {
    type: "pie",
    data: {
        labels: InhouseCRsStatusNames,
        datasets: [
            {
                data: InhouseCRsStatusValues,
            },
        ],
    },
    options: {
        responsive: true,
    },
    });
});


$("#appFilter").change(function(){
    renderChart();
});
$("#statusFilter").change(function(){
    renderChart();
});

function renderChart()
{
    const applicationValue = $('#appFilter').value;
    const statusValue = $('#statusFilter').value;
}
</script>




<script>

$(document).ready(function () {

// in house chart   
// Example data for categories
   const VendorCRsStatusNames= [];
   const VendorCRsStatusValues= [];
   @foreach($vendor_crs_per_status_system as $key=>$item)
       var data_val = "{{ $item->total }}";
       VendorCRsStatusNames.push("{{ $item->status?$item->status->status_name:'No Name' }}");
       VendorCRsStatusValues.push(data_val);
   @endforeach


    // Initialize Chart
    const ctxVendor = document.getElementById("VendorStatusChart").getContext("2d");
    let pieVendorChart = new Chart(ctxVendor, {
    type: "pie",
    data: {
        labels: VendorCRsStatusNames,
        datasets: [
            {
                data: VendorCRsStatusValues,
            },
        ],
    },
    options: {
        responsive: true,
    },
    });
});


$("#appVendorFilter").change(function(){
    renderVendorChart();
});
$("#statusVendorFilter").change(function(){
    renderVendorChart();
});

function renderVendorChart()
{
    const applicationVendorValue = $('#appVendorFilter').value;
    const statusVendorValue = $('#statusVendorFilter').value;
}
</script>

@endpush