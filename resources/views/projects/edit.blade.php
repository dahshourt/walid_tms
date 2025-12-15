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
            <form action="{{ route('projects.update', $row->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('projects.form')

                <div class="card card-custom">
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary" {{ $row->status === 'Delivered' ? 'disabled' : '' }}>
                                    <i class="la la-save"></i> Update Project
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@endsection


