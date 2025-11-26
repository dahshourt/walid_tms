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
                <!--begin::Toolbar-->
                <div class="d-flex align-items-center">
                    <a href="{{ route('requester-department.index') }}" class="btn btn-white font-weight-bold">
                        <i class="flaticon2-back"></i> Back to List
                    </a>
                </div>
                <!--end::Toolbar-->
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
                        <h3 class="card-title">Edit {{ $form_title }}</h3>
                    </div>
                    <!--begin::Form-->
                    <form class="form" action="{{ route('requester-department.update', $row->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $row->name) }}" required autofocus />
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <div class="radio-inline">
                                    <label class="radio radio-primary">
                                        <input type="radio" name="active" value="1" {{ $row->active ? 'checked' : '' }} />
                                        <span></span>
                                        Active
                                    </label>
                                    <label class="radio radio-primary">
                                        <input type="radio" name="active" value="0" {{ !$row->active ? 'checked' : '' }} />
                                        <span></span>
                                        Inactive
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary mr-2">Update</button>
                            <a href="{{ route('requester-department.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
    <!--end::Content-->
@endsection
