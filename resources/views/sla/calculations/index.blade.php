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

                <!--begin::Card-->
                <div class="card">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">{{ $title }}</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            <a href='{{ route("sla-calculations.create") }}' class="btn btn-primary font-weight-bolder">
                                <span class="svg-icon svg-icon-md">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                        viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <circle fill="#000000" cx="9" cy="15" r="6" />
                                            <path
                                                d="M8.8,7 C9.8,5.2 11.8,4 14,4 C17.3,4 20,6.7 20,10 C20,12.2 18.8,14.2 17,15.2 C17,15.1 17,15 17,15 C17,10.6 13.4,7 9,7 C8.9,7 8.9,7 8.8,7 Z"
                                                fill="#000000" opacity="0.3" />
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                                New Record
                            </a>
                            <!--end::Button-->
                        </div>
                    </div>

                    <div class="card-body">
                        <!--begin: Datatable-->
                        <table class="table table-separate table-head-custom table-checkable" id="kt_datatable2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>SLA Time</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Group</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calculations as $index => $slaCalculation)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $slaCalculation->sla_time }}</td>
                                        <td>{{ ucfirst($slaCalculation->type) }}</td>
                                        <td>{{ $slaCalculation->status->name ?? '-' }}</td>
                                        <td>{{ $slaCalculation->group->name ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('sla-calculations.edit', $slaCalculation->id) }}"
                                                class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('sla-calculations.destroy', $slaCalculation->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!--end: Datatable-->
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
