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
                    <h2 class="text-white font-weight-bold my-2 mr-5">Welcome , {{ auth()->user()->name }}</h2>
                    <!--end::Title-->
                    <div class="text-white font-weight-bold my-2 mr-5" style="opacity: 0.9;">
                        <i class="fas fa-clock mr-2"></i>
                         Last login : {{ auth()->user()->last_login }}
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
            <div class="card card-custom card-stretch example example-compact" id="kt_page_stretched_card">
                <div class="card-body">
                    <!-- Align Heading and Boxes -->
                    <div class="d-flex flex-column align-items-center gap-2">
                        <!-- Heading -->
                        <h2 class="mb-2">
                            You are logged in as
                            <span>
                                @if(session()->has('default_group'))
                                    {{ session('current_group_name') }}
                                @else
                                    @if(auth()->user()->default_group)
                                        {{ auth()->user()->defualt_group->name }}
                                    @endif
                                @endif
                            </span>
                        </h2>
                    
                        <br>
                        @canany(['Access Search' , 'Access Advanced Search'])
                        <!-- Search Boxes in Row -->
                        <div class="row w-100">
                            @php
                                $hasSearch = auth()->user()->can('Access Search');
                                $hasAdvancedSearch = auth()->user()->can('Access Advanced Search');
                                $searchColClass = $hasSearch && $hasAdvancedSearch ? 'col-lg-6' : 'col-12';
                            @endphp
                            
                            @can('Access Search')
                            <div class="{{ $searchColClass }} col-md-12 mb-4">
                                <!-- Quick Search -->
                                <div class="card card-custom bgi-no-repeat bgi-size-cover gutter-b h-100" style="background-image: url({{asset('public/new_theme/assets/media/bg/bg-6.jpg')}})">
                                    <div class="card-body d-flex flex-column h-100">
                                        <div class="mb-auto">
                                            <h3 class="font-weight-bolder text-white">Quick Search</h3>
                                            <div class="text-white-50 font-size-lg mt-2">Search with CR Number</div>
                                        </div>
                                        <div class="mt-4">
                                            <a href="{{ url('/searchs') }}" class="btn btn-white font-weight-bold py-3 px-6">Search</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endcan
                            
                            @can('Access Advanced Search')
                            <div class="{{ $searchColClass }} col-md-12 mb-4">
                                <!-- Advanced Search -->
                                <div class="card card-custom bgi-no-repeat bgi-size-cover gutter-b h-100" style="background-image: url({{asset('public/new_theme/assets/media/bg/bg-6.jpg')}})">
                                    <div class="card-body d-flex flex-column h-100">
                                        <div class="mb-auto">
                                            <h3 class="font-weight-bolder text-white">Advanced Search</h3>
                                            <div class="text-white-50 font-size-lg mt-2">Search with multiple criteria</div>
                                        </div>
                                        <div class="mt-4">
                                            <a href="{{ url('/search/advanced_search') }}" class="btn btn-white font-weight-bold py-3 px-6">
                                                <span class="d-flex align-items-center">
                                                    <span>Advanced Search</span>
                                                    <span class="svg-icon svg-icon-white svg-icon-md ml-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                                        </svg>
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endcan
                        </div>
                        @endcanany
                        
                        @canany(['Create ChangeRequest' , 'My Assignments'])
                        <div class="row w-100">
                            @php
                                $hasCreateCR = auth()->user()->can('Create ChangeRequest');
                                $hasAssignments = auth()->user()->can('My Assignments');
                                $actionColClass = ($hasCreateCR && $hasAssignments) ? 'col-lg-6' : 'col-12';
                            @endphp
                            
                            @can('Create ChangeRequest')
                            <div class="{{ $actionColClass }} col-md-12 mb-4">
                                <div class="card card-custom bgi-no-repeat bgi-size-cover gutter-b h-100" style="background-image: url({{asset('public/new_theme/assets/media/bg/bg-6.png')}})">
                                    <div class="card-body d-flex flex-column h-100">
                                        <div class="mb-auto">
                                            <h3 class="font-weight-bolder text-white">Create CR</h3>
                                            <div class="text-white font-size-lg mt-2">Start a new change request</div>
                                        </div>
                                        <div class="mt-4">
                                            <a href="{{ url('/change_request/workflow/type') }}" class="btn btn-white font-weight-bold py-3 px-6">Create</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endcan
                            
                            @can('My Assignments')
                            <div class="{{ $actionColClass }} col-md-12 mb-4">
                                <div class="card card-custom bgi-no-repeat bgi-size-cover gutter-b h-100" style="background-image: url({{asset('public/new_theme/assets/media/bg/bg-6.png')}})">
                                    <div class="card-body d-flex flex-column h-100">
                                        <div class="mb-auto">
                                            <h3 class="font-weight-bolder text-white">My Assignments</h3>
                                            <div class="text-white font-size-lg mt-2">View and manage your assigned tickets</div>
                                        </div>
                                        <div class="mt-4">
                                            <a href="{{ url('/my_assignments') }}" class="btn btn-white font-weight-bold py-3 px-6">View Assignments</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endcan
                        </div>
                        @endcanany
                    </div>
                    <!-- End of Heading and Boxes -->
                </div>
            </div>
            <!--end::Card-->

            <!--begin::Card-->
            <div class="card card-custom gutter-b mt-5">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">KPI Status Chart</h3>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="kpiChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
            <!--end::Card-->
            
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var kpiData = @json($kpiData);

                    var ctx = document.getElementById('kpiChart').getContext('2d');
                    var kpiChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: kpiData.labels,
                            datasets: kpiData.datasets
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'KPI Count by Year and Status'
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    },
                                    title: {
                                        display: true,
                                        text: 'Count'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Status'
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

@endsection
