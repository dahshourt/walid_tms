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
                <label class="font-weight-bold d-block text-center mb-3">Link Projects</label>
                <div class="d-flex justify-content-center">
                    <div style="width: 60%;">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <select class="form-control kt-select2" name="projects" id="kpi_unlinked_projects" multiple>
                                    @foreach(($unlinkedProjects ?? []) as $project)
                                        <option value="{{ $project->id }}">
                                            {{ $project->name }} - {{ $project->status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button"
                                    id="kpi_link_projects_btn"
                                    class="btn btn-primary ml-3"
                                    style="display: none;">
                                Link
                            </button>
                        </div>
                        <span class="form-text text-muted text-center d-block mt-2">
                            Select projects not yet linked to any KPI, then click <strong>Link</strong> to attach them to this KPI.
                        </span>
                    </div>
                </div>
            </div>

            <!-- Loader -->
            <div id="kpi_projects_loader" class="text-center" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        @endif

        @php
            $statusClasses = [
                'Delivered'    => 'label-light-success',
                'In Progress'  => 'label-light-primary',
                'On-Hold'      => 'label-light-warning',
                'Canceled'     => 'label-light-danger',
                'Not Started'  => 'label-light-secondary text-dark',
            ];
        @endphp
        <div class="table-responsive">
            <table id="kpi_related_projects_table" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="50px"></th>
                    <th>Project Name</th>
                    <th>Project Manager</th>
                    <th>Status</th>
                    @if(!$isView)
                        <th width="100px" class="text-center">Action</th>
                    @endif
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
                            @php $statusClass = $statusClasses[$project->status] ?? 'label-light-secondary text-dark'; @endphp
                            <span class="label label-inline {{ $statusClass }} font-weight-bold">
                                {{ $project->status }}
                            </span>
                        </td>
                        @if(!$isView)
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-icon btn-light-danger js-detach-project"
                                        data-project-id="{{ $project->id }}"
                                        data-project-name="{{ $project->name }}"
                                        title="Detach Project">
                                    <i class="la la-trash"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                    <tr class="project-details-row" data-project-id="{{ $project->id }}" style="display: none;">
                        <td colspan="{{ $isView ? '4' : '5' }}" class="p-0">
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
                                                        @php $milestoneClass = $statusClasses[$milestone->status] ?? 'label-light-secondary text-dark'; @endphp
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
                        </td>
                    </tr>
                @empty
                    <tr class="no-projects-row">
                        <td colspan="{{ $isView ? '4' : '5' }}" class="text-center text-muted">No projects linked to this KPI.</td>
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
            const attachUrl = "{{ route('kpi-projects.attach', ['kpi' => $row->id, 'project' => ':projectId']) }}";
            const detachUrl = "{{ route('kpi-projects.detach', ['kpi' => $row->id, 'project' => ':projectId']) }}";
            const linkButton = $('#kpi_link_projects_btn');
            const loader = $('#kpi_projects_loader');
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const existingProjectIds = @json(($row->projects ?? collect())->pluck('id')->toArray());

            const statusClasses = {
                'Delivered': 'label-light-success',
                'In Progress': 'label-light-primary',
                'On-Hold': 'label-light-warning',
                'Canceled': 'label-light-danger',
                'Not Started': 'label-light-secondary text-dark',
            };

            function showLoader() {
                loader.show();
                $('#kpi_related_projects_table').hide();
            }

            function hideLoader() {
                loader.hide();
                $('#kpi_related_projects_table').show();
            }

            /**
             * Append a single project (with its quarters & milestones) to the table without touching existing rows.
             */
            function appendProjectRow(project) {
                const $tbody = $('#kpi_related_projects_table tbody');
                const isView = {{ $isView ? 'true' : 'false' }};

                if (!project || typeof project.id === 'undefined') {
                    return;
                }

                // Remove placeholder \"no projects\" row if it exists
                $tbody.find('tr.no-projects-row').remove();

                // Ensure we don't end up with duplicated rows for the same project
                $tbody.find('tr.project-row[data-project-id="' + project.id + '"]').remove();
                $tbody.find('tr.project-details-row[data-project-id="' + project.id + '"]').remove();

                const statusClass = statusClasses[project.status] || 'label-light-secondary text-dark';

                let html = '';

                // Main project row
                html += '<tr class="project-row" data-project-id="' + project.id + '">';
                html += '    <td class="text-center">';
                html += '        <button class="btn btn-sm btn-icon btn-light-primary js-toggle-project-details"';
                html += '                data-project-id="' + project.id + '"';
                html += '                aria-expanded="false">';
                html += '            <i class="la la-angle-down"></i>';
                html += '        </button>';
                html += '    </td>';
                html += '    <td>' + (project.name || '') + '</td>';
                html += '    <td>' + (project.project_manager_name || '') + '</td>';
                html += '    <td>';
                html += '        <span class="label label-inline ' + statusClass + ' font-weight-bold">';
                html +=              (project.status || '');
                html += '        </span>';
                html += '    </td>';

                if (!isView) {
                    html += '    <td class="text-center">';
                    html += '        <button type="button"';
                    html += '                class="btn btn-sm btn-icon btn-light-danger js-detach-project"';
                    html += '                data-project-id="' + project.id + '"';
                    html += '                data-project-name="' + (project.name || '') + '"';
                    html += '                title="Detach Project">';
                    html += '            <i class="la la-trash"></i>';
                    html += '        </button>';
                    html += '    </td>';
                }

                html += '</tr>';

                // Details row
                html += '<tr class="project-details-row" data-project-id="' + project.id + '" style="display: none;">';
                html += '    <td colspan="' + (isView ? '4' : '5') + '" class="p-0">';
                html += '        <div class="bg-light-primary p-5" data-project-details-id="' + project.id + '">';
                html += '            <h5 class="font-weight-bold mb-4">Quarters & Milestones</h5>';

                const projectQuartersRaw = project.quarters || [];
                if (Array.isArray(projectQuartersRaw) && projectQuartersRaw.length > 0) {
                    // Ensure we only render each quarter once per project
                    const uniqueQuarters = [];
                    const seenQuarterIds = new Set();
                    projectQuartersRaw.forEach(function (quarter) {
                        if (!quarter || typeof quarter.id === 'undefined') {
                            return;
                        }
                        if (!seenQuarterIds.has(quarter.id)) {
                            seenQuarterIds.add(quarter.id);
                            uniqueQuarters.push(quarter);
                        }
                    });

                    uniqueQuarters.forEach(function (quarter) {
                        if (!quarter) return;

                        html += '<div class="mb-4" data-quarter-project-id="' + project.id + '">';
                        html += '    <div class="d-flex align-items-center mb-2">';
                        html += '        <button class="btn btn-sm btn-icon btn-light-info js-toggle-quarter-details mr-2"';
                        html += '                data-quarter-id="' + quarter.id + '"';
                        html += '                aria-expanded="false">';
                        html += '            <i class="la la-angle-down"></i>';
                        html += '        </button>';
                        html += '        <h6 class="font-weight-bold mb-0">' + (quarter.quarter || '') + '</h6>';
                        html += '    </div>';

                        html += '    <div class="quarter-details" data-quarter-id="' + quarter.id + '" style="display: none; margin-left: 40px;">';

                        const milestonesRaw = quarter.milestones || [];
                        if (Array.isArray(milestonesRaw) && milestonesRaw.length > 0) {
                            // Ensure we only render each milestone once per quarter
                            const uniqueMilestones = [];
                            const seenMilestoneIds = new Set();
                            milestonesRaw.forEach(function (milestone) {
                                if (!milestone || typeof milestone.id === 'undefined') {
                                    return;
                                }
                                if (!seenMilestoneIds.has(milestone.id)) {
                                    seenMilestoneIds.add(milestone.id);
                                    uniqueMilestones.push(milestone);
                                }
                            });

                            uniqueMilestones.forEach(function (milestone) {
                                if (!milestone) return;
                                const milestoneStatusClass = statusClasses[milestone.status] || 'label-light-secondary text-dark';

                                html += '<div class="d-flex align-items-center justify-content-between mb-2 p-3 bg-white rounded">';
                                html += '    <div class="flex-grow-1">';
                                html += '        <div class="font-weight-bold">' + (milestone.milestone || '') + '</div>';
                                html += '        <span class="label label-inline ' + milestoneStatusClass + ' font-weight-bold mt-1">';
                                html +=              (milestone.status || '');
                                html += '        </span>';
                                html += '    </div>';
                                html += '</div>';
                            });
                        } else {
                            html += '<div class="text-muted p-3">No milestones for this quarter</div>';
                        }

                        html += '    </div>';
                        html += '</div>';
                    });
                } else {
                    html += '<div class="text-muted">No quarters defined for this project</div>';
                }

                html += '        </div>';
                html += '    </td>';
                html += '</tr>';

                $tbody.append(html);
            }

            function renderProjectsTable(projects) {
                const $tbody = $('#kpi_related_projects_table tbody');
                const isView = {{ $isView ? 'true' : 'false' }};

                // Completely empty the tbody to prevent any DOM issues
                $tbody.empty();

                if (!Array.isArray(projects) || projects.length === 0) {
                    $tbody.html(
                        '<tr>' +
                        '<td colspan="' + (isView ? '4' : '5') + '" class="text-center text-muted">No projects linked to this KPI.</td>' +
                        '</tr>'
                    );
                    return;
                }

                // Remove duplicate projects by ID to prevent rendering duplicates
                const uniqueProjects = [];
                const seenIds = new Set();
                projects.forEach(function (project) {
                    if (project && typeof project.id !== 'undefined' && !seenIds.has(project.id)) {
                        seenIds.add(project.id);
                        uniqueProjects.push(project);
                    }
                });

                let html = '';

                // Process each project independently to ensure clean separation
                uniqueProjects.forEach(function (project) {

                    const statusClass = statusClasses[project.status] || 'label-light-secondary text-dark';

                    // Project row
                    html += '<tr class="project-row" data-project-id="' + project.id + '">';
                    html += '    <td class="text-center">';
                    html += '        <button class="btn btn-sm btn-icon btn-light-primary js-toggle-project-details"';
                    html += '                data-project-id="' + project.id + '"';
                    html += '                aria-expanded="false">';
                    html += '            <i class="la la-angle-down"></i>';
                    html += '        </button>';
                    html += '    </td>';
                    html += '    <td>' + (project.name || '') + '</td>';
                    html += '    <td>' + (project.project_manager_name || '') + '</td>';
                    html += '    <td>';
                    html += '        <span class="label label-inline ' + statusClass + ' font-weight-bold">';
                    html +=              (project.status || '');
                    html += '        </span>';
                    html += '    </td>';

                    if (!isView) {
                        html += '    <td class="text-center">';
                        html += '        <button type="button"';
                        html += '                class="btn btn-sm btn-icon btn-light-danger js-detach-project"';
                        html += '                data-project-id="' + project.id + '"';
                        html += '                data-project-name="' + (project.name || '') + '"';
                        html += '                title="Detach Project">';
                        html += '            <i class="la la-trash"></i>';
                        html += '        </button>';
                        html += '    </td>';
                    }

                    html += '</tr>';

                    // Details row with quarters & milestones - scoped to THIS project only
                    html += '<tr class="project-details-row" data-project-id="' + project.id + '" style="display: none;">';
                    html += '    <td colspan="' + (isView ? '4' : '5') + '" class="p-0">';
                    html += '        <div class="bg-light-primary p-5" data-project-details-id="' + project.id + '">';
                    html += '            <h5 class="font-weight-bold mb-4">Quarters & Milestones</h5>';

                    // Ensure we only process quarters for THIS project - check if quarters exist and is array
                    const projectQuarters = project.quarters;
                    if (projectQuarters && Array.isArray(projectQuarters) && projectQuarters.length > 0) {
                        // Remove duplicate quarters by ID
                        const uniqueQuarters = [];
                        const seenQuarterIds = new Set();
                        projectQuarters.forEach(function (quarter) {
                            if (quarter && typeof quarter.id !== 'undefined' && !seenQuarterIds.has(quarter.id)) {
                                seenQuarterIds.add(quarter.id);
                                uniqueQuarters.push(quarter);
                            }
                        });

                        uniqueQuarters.forEach(function (quarter) {
                            html += '<div class="mb-4" data-quarter-project-id="' + project.id + '">';
                            html += '    <div class="d-flex align-items-center mb-2">';
                            html += '        <button class="btn btn-sm btn-icon btn-light-info js-toggle-quarter-details mr-2"';
                            html += '                data-quarter-id="' + quarter.id + '"';
                            html += '                aria-expanded="false">';
                            html += '            <i class="la la-angle-down"></i>';
                            html += '        </button>';
                            html += '        <h6 class="font-weight-bold mb-0">' + (quarter.quarter || '') + '</h6>';
                            html += '    </div>';

                            html += '    <div class="quarter-details" data-quarter-id="' + quarter.id + '" style="display: none; margin-left: 40px;">';

                            const quarterMilestones = quarter.milestones;
                            if (quarterMilestones && Array.isArray(quarterMilestones) && quarterMilestones.length > 0) {
                                quarterMilestones.forEach(function (milestone) {
                                    if (!milestone) {
                                        return; // Skip invalid milestone data
                                    }
                                    const milestoneStatusClass = statusClasses[milestone.status] || 'label-light-secondary text-dark';

                                    html += '<div class="d-flex align-items-center justify-content-between mb-2 p-3 bg-white rounded">';
                                    html += '    <div class="flex-grow-1">';
                                    html += '        <div class="font-weight-bold">' + (milestone.milestone || '') + '</div>';
                                    html += '        <span class="label label-inline ' + milestoneStatusClass + ' font-weight-bold mt-1">';
                                    html +=              (milestone.status || '');
                                    html += '        </span>';
                                    html += '    </div>';
                                    html += '</div>';
                                });
                            } else {
                                html += '<div class="text-muted p-3">No milestones for this quarter</div>';
                            }

                            html += '    </div>';
                            html += '</div>';
                        });
                    } else {
                        html += '<div class="text-muted">No quarters defined for this project</div>';
                    }

                    html += '        </div>';
                    html += '    </td>';
                    html += '</tr>';
                });

                // Replace entire tbody content in one operation
                $tbody.html(html);
            }

            if (unlinkedSelect.length) {
                unlinkedSelect.select2({
                    placeholder: "Select projects",
                    allowClear: true,
                    width: '100%'
                });

                unlinkedSelect.on('change', function () {
                    const selected = $(this).val() || [];
                    if (selected.length > 0) {
                        linkButton.show();
                    } else {
                        linkButton.hide();
                    }
                });

                linkButton.on('click', function () {
                    const selected = unlinkedSelect.val() || [];
                    if (selected.length === 0) {
                        return;
                    }

                    // Attach projects one by one
                    const projectIds = selected.map(Number);
                    let completed = 0;
                    let errors = [];

                    linkButton.prop('disabled', true);
                    showLoader();

                    function attachNextProject() {
                        if (completed >= projectIds.length) {
                            hideLoader();
                            linkButton.prop('disabled', false);

                            // Remove successfully attached projects from select
                            projectIds.forEach(function (projectId) {
                                if (!errors.some(function(err) { return err.includes(projectId.toString()); })) {
                                    unlinkedSelect.find('option[value="' + projectId + '"]').remove();
                                }
                            });

                            // Clear selection and hide button
                            unlinkedSelect.val(null).trigger('change');
                            linkButton.hide();

                            if (errors.length > 0) {
                                alert('Some projects could not be attached:\n' + errors.join('\n'));
                            }

                            return;
                        }

                        const projectId = projectIds[completed];
                        const url = attachUrl.replace(':projectId', projectId);

                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                project_id: projectId,
                                _token: csrfToken,
                            },
                        })
                            .done(function (response) {
                                if (response && response.success && response.data && response.data.project) {
                                    const project = response.data.project;

                                    // Update internal list of existing IDs
                                    if (existingProjectIds.indexOf(project.id) === -1) {
                                        existingProjectIds.push(project.id);
                                    }

                                    // Remove from select so it can't be linked again
                                    unlinkedSelect.find('option[value="' + project.id + '"]').remove();

                                    // Append the new project row to the table
                                    appendProjectRow(project);
                                } else {
                                    const errorMsg = response.message || 'Failed to attach project ID: ' + projectId;
                                    errors.push(errorMsg);
                                }
                            })
                            .fail(function (xhr) {
                                const errorMsg = xhr.responseJSON && xhr.responseJSON.message
                                    ? xhr.responseJSON.message
                                    : 'Error attaching project ID: ' + projectId;
                                errors.push(errorMsg);
                            })
                            .always(function () {
                                completed++;
                                attachNextProject();
                            });
                    }

                    attachNextProject();
                });
            }

            // Detach project handler
            $(document).on('click', '.js-detach-project', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $btn = $(this);
                const projectId = $btn.data('project-id');
                const projectName = $btn.data('project-name');

                if (!confirm('Are you sure you want to detach "' + projectName + '" from this KPI?')) {
                    return;
                }

                const url = detachUrl.replace(':projectId', projectId);

                $btn.prop('disabled', true);
                showLoader();

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: csrfToken,
                    },
                })
                    .done(function (response) {
                        if (response && response.success) {
                            // Remove the project rows from DOM
                            $('tr.project-row[data-project-id="' + projectId + '"]').remove();
                            $('tr.project-details-row[data-project-id="' + projectId + '"]').remove();

                            // Remove from internal IDs list
                            const index = existingProjectIds.indexOf(projectId);
                            if (index !== -1) {
                                existingProjectIds.splice(index, 1);
                            }
                        } else {
                            alert(response.message || 'Failed to detach project');
                            $btn.prop('disabled', false);
                        }
                    })
                    .fail(function (xhr) {
                        const errorMsg = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Error detaching project';
                        alert(errorMsg);
                        $btn.prop('disabled', false);
                    })
                    .always(function () {
                        hideLoader();
                    });
            });

            // Toggle project details
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
                if ($(e.target).closest('a, button, .js-toggle-project-details, .js-detach-project').length) {
                    return;
                }
                $(this).find('.js-toggle-project-details').trigger('click');
            });
        });
    </script>
@endpush
@endif
