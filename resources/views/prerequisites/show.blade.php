@extends('layouts.app')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex flex-column">
                    <h2 class="text-white font-weight-bold my-2 mr-5">{{ $title }}</h2>
                </div>
            </div>
        </div>
    </div>
    <!--end::Subheader-->

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom gutter-b example example-compact">
                        <div class="card-header">
                            <h3 class="card-title">View Prerequisite</h3>
                        </div>
                        
                        <div class="card-body">
                            @include("$view.form")
                        </div>
                        
                        <div class="card-footer">
                            <a href="{{ route("$route.index") }}" class="btn btn-light-primary font-weight-bold">Back</a>
                            <button type="button" id="openModal" class="btn btn-primary">View History Logs</button>
                        </div>
                        @include("prerequisites.logs")
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Entry-->
</div>
@endsection

@push('script')
    <script>
        var modal = document.getElementById("modal");
        var btn = document.getElementById("openModal");
        var closeBtn = document.getElementById("close_logs");

        btn.onclick = function () {
            modal.style.display = "block";
        };

        closeBtn.onclick = function () {
            modal.style.display = "none";
        };

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };
    </script>
@endpush