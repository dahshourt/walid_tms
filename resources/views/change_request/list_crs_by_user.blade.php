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
                    <h2 class="text-white font-weight-bold my-2 mr-5"></h2>
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
                            <h3 class="card-title">Enter User Name To List It's CRs</h3>
                        </div>
                        
                        <!--begin::Form-->
                        <form class="form" action="{{ url('change_request/listCRsUsers') }}" method="post">
                        	 @csrf
                            <div class="card-body">

                                <!-- Display validation errors -->
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

                                <!-- Workflow Type Dropdown -->
                                <div class="form-group">
                                    <label for="user_type">User Name</label>
                                    <input type="text" name="userName" class="form-control form-control-lg" placeholder="Enter User To List It's CRs" />
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success mr-2">Next</button>
                            </div>
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
