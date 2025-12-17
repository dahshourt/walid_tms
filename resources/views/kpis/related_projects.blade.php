@php
    $isView = request()->routeIs('*.show');
    $kpiProjects = $row->projects ?? collect();
@endphp

@if(isset($row))
<div class="card card-custom card-stretch gutter-b">
    <div class="card-header border-0 pt-5 d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bolder">Related Projects</h3>
        <a href="{{ route('projects.export-by-kpi', ['kpi' => $row->id]) }}" class="btn btn-success font-weight-bolder btn-sm">
            <span class="svg-icon svg-icon-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M7,18 L17,18 C18.1045695,18 19,18.8954305 19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 C5,18.8954305 5.8954305,18 7,18 Z M7,20 L17,20 C17.5522847,20 18,20.4477153 18,21 C18,21.5522847 17.5522847,22 17,22 L7,22 C6.44771525,22 6,21.5522847 6,21 C6,20.4477153 6.44771525,20 7,20 Z" fill="#000000" fill-rule="nonzero"/>
                        <path d="M12,2 C12.5522847,2 13,2.44771525 13,3 L13,13.5857864 L15.2928932,11.2928932 C15.6834175,10.9023689 16.3165825,10.9023689 16.7071068,11.2928932 C17.0976311,11.6834175 17.0976311,12.3165825 16.7071068,12.7071068 L12.7071068,16.7071068 C12.3165825,17.0976311 11.6834175,17.0976311 11.2928932,16.7071068 L7.29289322,12.7071068 C6.90236893,12.3165825 6.90236893,11.6834175 7.29289322,11.2928932 C7.68341751,10.9023689 8.31658249,10.9023689 8.70710678,11.2928932 L11,13.5857864 L11,3 C11,2.44771525 11.4477153,2 12,2 Z" fill="#000000"/>
                    </g>
                </svg>
            </span>Export Excel
        </a>
    </div>
    <div class="card-body">
        @if(!$isView)
            <div class="form-group">
                <label class="font-weight-bold">Link Projects</label>
                <select class="form-control kt-select2" name="projects" id="kpi_unlinked_projects" multiple>
                    @foreach(($unlinkedProjects ?? []) as $project)
                        <option value="{{ $project->id }}">
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                <span class="form-text text-muted">Select projects not yet linked to any KPI to link them here.</span>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="50px"></th>
                    <th>Project Name</th>
                    <th>Project Manager</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($kpiProjects as $project)
                    <tr class="project-row" data-project-id="{{ $project->id }}">
                        <td class="text-center">
                            <button class="btn btn-sm btn-icon btn-light-primary js-toggle-project-details"
                                    data-project-id="{{ $project->id }}"
                                    aria-expanded="false">
                                <i class="la la-angle-down"></i>
                            </button>
                        </td>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->project_manager_name }}</td>
                        <td>
                            <span class="label label-inline label-light-{{ $project->status === 'Delivered' ? 'success' : ($project->status === 'In Progress' ? 'primary' : ($project->status === 'On-Hold' ? 'warning' : ($project->status === 'Canceled' ? 'danger' : 'secondary'))) }} font-weight-bold">
                                {{ $project->status }}
                            </span>
                        </td>
                    </tr>
                    <tr class="project-details-row" data-project-id="{{ $project->id }}" style="display: none;">
                        <td colspan="4" class="p-0">
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
                                                        <span class="label label-inline label-light-{{ $milestone->status === 'Delivered' ? 'success' : ($milestone->status === 'In Progress' ? 'primary' : ($milestone->status === 'On-Hold' ? 'warning' : ($milestone->status === 'Canceled' ? 'danger' : 'secondary'))) }} font-weight-bold mt-1">
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No projects linked to this KPI.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('script')
    <script>
        jQuery(document).ready(function () {
            const unlinkedSelect = $('#kpi_unlinked_projects');
            const updateProjectsUrl = "{{ route('kpis.update-projects', ['kpi' => $row->id]) }}";
            const csrfToken = $('meta[name=\"csrf-token\"]').attr('content');
            const existingProjectIds = @json(($row->projects ?? collect())->pluck('id')->toArray());

            if (unlinkedSelect.length) {
                unlinkedSelect.select2({
                    placeholder: "Select projects",
                    allowClear: true,
                    width: '100%'
                });

                unlinkedSelect.on('change', function () {
                    const selected = $(this).val() || [];
                    const merged = Array.from(new Set(existingProjectIds.concat(selected.map(Number))));

                    $.ajax({
                        url: updateProjectsUrl,
                        type: 'POST',
                        data: {
                            project_ids: merged,
                            _token: csrfToken,
                        },
                    })
                        .done(function (response) {
                            if (response && response.success) {
                                location.reload();
                            } else {
                                console.error(response && response.message ? response.message : 'Unable to update projects');
                            }
                        })
                        .fail(function (xhr) {
                            console.error('Error updating projects', xhr);
                        });
                });
            }

            // Toggle project details (copied from projects index)
            $(document).on('click', '.js-toggle-project-details', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var projectId = $btn.data('project-id');
                var $row = $btn.closest('tr');
                var $details = $('tr.project-details-row[data-project-id="' + projectId + '"]');
                var expanded = $btn.attr('aria-expanded') === 'true';

                if (expanded) {
                    $btn.attr('aria-expanded', 'false');
                    $btn.find('i.la').removeClass('la-angle-up').addClass('la-angle-down');
                    $details.hide();
                } else {
                    $btn.attr('aria-expanded', 'true');
                    $btn.find('i.la').removeClass('la-angle-down').addClass('la-angle-up');
                    if ($details.prev()[0] !== $row[0]) {
                        $details.insertAfter($row);
                    }
                    $details.show();
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
@endif

