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
                    <div class="text-white font-weight-bold my-2 mr-5 " style="opacity: 0.9;">
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
                                    {{ session('default_group_name') }}
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
                            @can('Access Search')
                            <div class="col-xl-6 col-lg-6 col-md-6 mb-4">
                                <!-- Quick Search -->
                                <div class="card card-custom bgi-no-repeat bgi-size-cover gutter-b" style="height: 150px; width: 100%; background-image: url({{asset('public/new_theme/assets/media/bg/bg-6.jpg')}})">
                                    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
                                        <div class="mr-2">
                                            <h3 class="font-weight-bolder text-white">Quick Search</h3>
                                            <div class="text-white-50 font-size-lg mt-2 ">Search with CR Number</div>
                                        </div>
                                        <a href="{{ url('/searchs') }}" class="btn btn-white font-weight-bold py-3 px-6">Search</a>
                                    </div>
                                </div>
                            </div>
                            @endcan
                            @can('Access Advanced Search')
                            <div class="col-xl-6 col-lg-6 col-md-6 mb-4">
                                <!-- Advanced Search -->
                                <div class="card card-custom bgi-no-repeat bgi-size-cover gutter-b" style="height: 150px; width: 100%; background-image: url({{asset('public/new_theme/assets/media/bg/bg-6.jpg')}})">
                                    <div class="card-body d-flex flex-column align-items-start justify-content-between flex-wrap">
                                        <div class="p-1 flex-grow-1">
                                            <h3 class="text-black font-weight-bolder line-height-lg mb-5 text-white">Advanced Search</h3>
                                        </div>
                                        <a href="{{ url('/search/advanced_search') }}" class="btn btn-link btn-link-white font-weight-bold">
                                            Search
                                            <span class="svg-icon svg-icon-lg svg-icon-black">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                        <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                                                        <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                                    </g>
                                                </svg>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endcan
                        </div>
                        @endcanany
                        @canany(['Create ChangeRequest' , 'My Assignments'])
                        <div class="row w-100">
                            <!-- Create CR -->
                            @can('Create ChangeRequest')
                            <div class="col-xl-6 col-lg-6 col-md-6 mb-4">
                                <div class="card card-custom bgi-no-repeat bgi-size-cover gutter-b text-white" style="height: 300px; width:100%; background-image: url({{asset('public/new_theme/assets/media/bg/bg-6.png')}})">
                                    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
                                        <div class="mr-2">
                                            <h3 class="font-weight-bolder text-white">Create CR</h3>
                                            <div class="text-white font-size-lg mt-2">Start a new change request</div>
                                        </div>
                                        <a href="{{ url('/change_request/workflow/type') }}" class="btn btn-white font-weight-bold py-3 px-6">Create</a>
                                    </div>
                                </div>
                            </div>
                            @endcan
                            @can('My Assignments')
                            <div class="col-xl-6 col-lg-6 col-md-6 mb-4">
                                <div class="card card-custom bgi-no-repeat bgi-size-cover gutter-b text-white" style="height: 300px; width:100%; background-image: url({{asset('public/new_theme/assets/media/bg/bg-6.png')}})">
                                    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
                                        <div class="mr-2">
                                            <h3 class="font-weight-bolder text-white">My Assignments</h3>
                                            <div class="text-white font-size-lg mt-2">View and manage your assigned tickets</div>
                                        </div>
                                        <a href="{{ url('/my_assignments') }}" class="btn btn-white font-weight-bold py-3 px-6">View Assignments</a>
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
        </div>
        <!--end::Container-->
    </div>
    
    <!--end::Entry-->
</div>
<!--end::Content-->


@endsection
