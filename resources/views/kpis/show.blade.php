@extends('layouts.app')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Heading-->
                <div class="d-flex flex-column">
                    <!--begin::Title-->
                    <h2 class="text-white font-weight-bold my-2 mr-5">{{ $title }}</h2>
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title m-0">View KPI</h3>
                            <button type="button" id="openModal" class="btn btn-primary">View History Logs</button>
                        </div>
                        <div class="card-body">
                            @include("$view.form")
                        </div>
                        <div class="card-footer">
                            <a href="{{ route("$route.index") }}" class="btn btn-light-primary font-weight-bold">Back</a>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    @include("kpis.comments_history")
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    @if(($row->classification ?? null) === 'PM')
                        @include("kpis.related_projects")
                    @else
                        @include("kpis.related_crs")
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    @include("kpis.logs")
                </div>
            </div>
        </div>
        <!--end::Container-->
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