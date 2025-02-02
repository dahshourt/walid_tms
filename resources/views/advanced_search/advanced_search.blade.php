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
                    <h2 class="text-white font-weight-bold my-2 mr-5">Add Group</h2>
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
                            <h3 class="card-title">Advanced Search</h3>
                        </div>
                        <!--begin::Form-->
                        <form class="form" action="{{ route('advanced.search.result') }}" method="POST" enctype="multipart/form-data" id="searchForm">
                            @csrf
                            @foreach($fields as $field)
                                <div class="form-group {{ $field->styleClasses }}">
                                    <label>{{ $field->label }}</label>
                                    <input 
                                        type="{{ $field->inputType }}" 
                                        class="form-control" 
                                        name="{{ $field->model }}" 
                                        placeholder="{{ $field->placeholder }}">
                                </div>
                            @endforeach
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Card-->

                    @if($errors->any())
                        <div class="error-messages">
                            @foreach($errors->all() as $error)
                                <div class="alert alert-danger alert-dismissible fade show alert-icon" role="alert">
                                    <i class="flaticon-warning"></i>
                                    {{ $error }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('searchForm');
        
        form.addEventListener('reset', function (evt) {
            evt.preventDefault();
            form.reset();
        });
    });
</script>
@endsection
