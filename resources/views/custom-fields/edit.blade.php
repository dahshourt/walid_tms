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
                    <h2 class="text-white font-weight-bold my-2 mr-5">Edit {{ $form_title }}</h2>
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
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">Edit {{ $form_title }}: {{ $row->name }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('custom-fields.index') }}" class="btn btn-light-primary font-weight-bolder">
                            <i class="ki ki-long-arrow-back icon-sm"></i>
                            Back to List
                        </a>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('custom-fields.update', $row->id) }}" class="form">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @include("$view.form")
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-6">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="la la-save"></i>
                                    Update {{ $form_title }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

@endsection
