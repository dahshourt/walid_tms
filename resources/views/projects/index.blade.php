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
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">{{ $title }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center">
                            @can('Create Projects')
                            <!--begin::Button-->
                            <a href='{{ route("projects.create") }}' class="btn btn-primary font-weight-bolder">
                                <span class="svg-icon svg-icon-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <circle fill="#000000" cx="9" cy="15" r="6" />
                                            <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                                        </g>
                                    </svg>
                                </span>New Record
                            </a>
                            <!--end::Button-->
                            @endcan

                            @can('List Projects')
                            <!--begin::Export Button-->
                            @if($collection->count() > 0)
                                <a href="{{ route('projects.export') }}"
                                   class="btn btn-success font-weight-bolder ml-3">
                                    <span class="svg-icon svg-icon-md">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Files/Download.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"/>
                                                <path d="M7,18 L17,18 C18.1045695,18 19,18.8954305 19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 C5,18.8954305 5.8954305,18 7,18 Z M7,20 L17,20 C17.5522847,20 18,20.4477153 18,21 C18,21.5522847 17.5522847,22 17,22 L7,22 C6.44771525,22 6,21.5522847 6,21 C6,20.4477153 6.44771525,20 7,20 Z" fill="#000000" fill-rule="nonzero"/>
                                                <path d="M12,2 C12.5522847,2 13,2.44771525 13,3 L13,13.5857864 L15.2928932,11.2928932 C15.6834175,10.9023689 16.3165825,10.9023689 16.7071068,11.2928932 C17.0976311,11.6834175 17.0976311,12.3165825 16.7071068,12.7071068 L12.7071068,16.7071068 C12.3165825,17.0976311 11.6834175,17.0976311 11.2928932,16.7071068 L7.29289322,12.7071068 C6.90236893,12.3165825 6.90236893,11.6834175 7.29289322,11.2928932 C7.68341751,10.9023689 8.31658249,10.9023689 8.70710678,11.2928932 L11,13.5857864 L11,3 C11,2.44771525 11.4477153,2 12,2 Z" fill="#000000"/>
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>Export Excel
                                </a>
                            @else
                                <span class="btn btn-secondary font-weight-bolder ml-3" style="opacity: 0.6; cursor: not-allowed;" title="No data available to export">
                                    <span class="svg-icon svg-icon-md">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Files/Download.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"/>
                                                <path d="M7,18 L17,18 C18.1045695,18 19,18.8954305 19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 C5,18.8954305 5.8954305,18 7,18 Z M7,20 L17,20 C17.5522847,20 18,20.4477153 18,21 C18,21.5522847 17.5522847,22 17,22 L7,22 C6.44771525,22 6,21.5522847 6,21 C6,20.4477153 6.44771525,20 7,20 Z" fill="#000000" fill-rule="nonzero"/>
                                                <path d="M12,2 C12.5522847,2 13,2.44771525 13,3 L13,13.5857864 L15.2928932,11.2928932 C15.6834175,10.9023689 16.3165825,10.9023689 16.7071068,11.2928932 C17.0976311,11.6834175 17.0976311,12.3165825 16.7071068,12.7071068 L12.7071068,16.7071068 C12.3165825,17.0976311 11.6834175,17.0976311 11.2928932,16.7071068 L7.29289322,12.7071068 C6.90236893,12.3165825 6.90236893,11.6834175 7.29289322,11.2928932 C7.68341751,10.9023689 8.31658249,10.9023689 8.70710678,11.2928932 L11,13.5857864 L11,3 C11,2.44771525 11.4477153,2 12,2 Z" fill="#000000"/>
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>Export Excel
                                </span>
                            @endif
                            <!--end::Export Button-->
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <!--begin: Datatable-->
                    @php
                        // Map project/milestone statuses to full label classes
                        $statusClasses = [
                            'Delivered'    => 'label-light-success',
                            'In Progress'  => 'label-light-primary',
                            'On-Hold'      => 'label-light-warning',
                            'Canceled'     => 'label-light-danger',
                            'Not Started'  => 'label-light-secondary text-dark',
                        ];
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dfUsageTable">
                            <thead>
                                <tr>
                                    <th width="50px"></th>
                                    <th>Project Name</th>
                                    <th>Project Manager</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    @canany(['Edit Projects'])
                                    <th width="100px">Actions</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($collection as $project)
                                <tr class="project-row" data-project-id="{{ $project->id }}">
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-icon btn-light-primary js-toggle-project-details"
                                                data-project-id="{{ $project->id }}"
                                                aria-expanded="false">
                                            <i class="la la-angle-down"></i>
                                        </button>
                                        <!-- Hidden details content used by DataTables child rows -->
                                        <div class="project-details-content d-none">
                                            <div class="bg-light-primary p-5">
                                                <h5 class="font-weight-bold mb-4">Quarters & Milestones</h5>
                                                @forelse($project->quarters as $quarter)
                                                <div class="mb-4">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <button class="btn btn-sm btn-icon btn-light-info js-toggle-quarter-details mr-2"
                                                                data-quarter-id="{{ $quarter->id }}"
                                                                aria-expanded="false">
                                                            <i class="la la-angle-down"></i>
                                                        </button>
                                                        <h6 class="font-weight-bold mb-0">{{ $quarter->quarter }}</h6>
                                                    </div>
                                                    <div class="quarter-details" data-quarter-id="{{ $quarter->id }}" style="display: none; margin-left: 40px;">
                                                        @forelse($quarter->milestones as $milestone)
                                                        <div class="d-flex align-items-center justify-content-between mb-2 p-3 bg-white rounded">
                                                            <div class="flex-grow-1">
                                                                <div class="font-weight-bold">{{ $milestone->milestone }}</div>
                                                                @php
                                                                    $milestoneClass = $statusClasses[$milestone->status] ?? 'label-light-secondary text-dark';
                                                                @endphp
                                                                <span class="label label-inline {{ $milestoneClass }} font-weight-bold mt-1">
                                                                    {{ $milestone->status }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        @empty
                                                        <div class="text-muted p-3">No milestones for this quarter</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                                @empty
                                                <div class="text-muted">No quarters defined for this project</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $project->name }}</td>
                                    <td>{{ $project->project_manager_name }}</td>
                                    <td>
                                        @php
                                            $statusClass = $statusClasses[$project->status] ?? 'label-light-secondary text-dark';
                                        @endphp
                                        <span class="label label-inline {{ $statusClass }} font-weight-bold">
                                            {{ $project->status }}
                                        </span>
                                    </td>
                                    <td>{{ $project->created_at->format('Y-m-d H:i') }}</td>
                                    @canany(['Edit Projects'])
                                    <td>
                                        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-clean btn-icon" title="Edit">
                                            <i class="la la-edit"></i>
                                        </a>
                                    </td>
                                    @endcanany
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No projects found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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

@push('script')
<script>
    $(document).ready(function() {
        // Toggle project details using DataTables child rows
        $(document).on('click', '.js-toggle-project-details', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var $row = $btn.closest('tr');
            var table = $('#dfUsageTable').DataTable();
            var row = table.row($row);

            if (row.child.isShown()) {
                row.child.hide();
                $btn.attr('aria-expanded', 'false');
                $btn.find('i.la').removeClass('la-angle-up').addClass('la-angle-down');
            } else {
                var detailsHtml = $row.find('.project-details-content').html();
                row.child(detailsHtml).show();
                $btn.attr('aria-expanded', 'true');
                $btn.find('i.la').removeClass('la-angle-down').addClass('la-angle-up');
            }
        });

        // Toggle quarter details
        $(document).on('click', '.js-toggle-quarter-details', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var quarterId = $btn.data('quarter-id');
            var $details = $('.quarter-details[data-quarter-id="' + quarterId + '"]');
            var expanded = $btn.attr('aria-expanded') === 'true';

            if (expanded) {
                $btn.attr('aria-expanded', 'false');
                $btn.find('i.la').removeClass('la-angle-up').addClass('la-angle-down');
                $details.slideUp();
            } else {
                $btn.attr('aria-expanded', 'true');
                $btn.find('i.la').removeClass('la-angle-down').addClass('la-angle-up');
                $details.slideDown();
            }
        });

        // Click on row to toggle (except on buttons/links)
        $(document).on('click', 'tr.project-row', function(e) {
            if ($(e.target).closest('a, button, .js-toggle-project-details').length) {
                return;
            }
            $(this).find('.js-toggle-project-details').trigger('click');
        });
    });
</script>
@endpush


