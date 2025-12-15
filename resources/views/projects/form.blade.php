@php
    $isDisabled = isset($row) && $row->status === 'Delivered' ? 'disabled' : '';
@endphp

<div class="card card-custom card-stretch gutter-b">
    <div class="card-header">
        <div class="card-title">
            <h3 class="card-label">Project Information</h3>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            {{-- Project Name --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-weight-bold">Project Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" 
                           value="{{ old('name', $row->name ?? '') }}" 
                           {{ $isDisabled }} required>
                    @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Project Manager Name --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-weight-bold">Project Manager Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="project_manager_name" 
                           value="{{ old('project_manager_name', $row->project_manager_name ?? '') }}" 
                           {{ $isDisabled }} required>
                    @error('project_manager_name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Project Status --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-weight-bold">Project Status <span class="text-danger">*</span></label>
                    <select class="form-control" name="status" {{ $isDisabled }} required>
                        <option value="">Select Status</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status }}" 
                                {{ (old('status', $row->status ?? '') == $status) ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                        @endforeach
                    </select>
                    @error('status')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom card-stretch gutter-b">
    <div class="card-header">
        <div class="card-title">
            <h3 class="card-label">Quarters & Milestones</h3>
        </div>
    </div>
    <div class="card-body">
        <div id="quarters-container">
            @if(isset($row) && $row->quarters->count() > 0)
                @foreach($row->quarters as $index => $quarter)
                <div class="quarter-item mb-4 p-4 border rounded" data-index="{{ $index }}">
                    <input type="hidden" name="quarters[{{ $index }}][id]" value="{{ $quarter->id }}">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-weight-bold mb-0">Quarter {{ $index + 1 }}</h5>
                        <button type="button" class="btn btn-sm btn-danger remove-quarter" {{ $isDisabled }}>
                            <i class="la la-trash"></i> Remove Quarter
                        </button>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="font-weight-bold">Quarter <span class="text-danger">*</span></label>
                            <select class="form-control" name="quarters[{{ $index }}][quarter]" {{ $isDisabled }} required>
                                <option value="">Select Quarter</option>
                                @foreach($quarters as $q)
                                <option value="{{ $q }}" {{ $quarter->quarter == $q ? 'selected' : '' }}>{{ $q }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="milestones-container" data-quarter-index="{{ $index }}">
                        <h6 class="font-weight-bold mb-3">Milestones</h6>
                        @foreach($quarter->milestones as $mIndex => $milestone)
                        <div class="milestone-item mb-3 p-3 bg-light rounded" data-milestone-index="{{ $mIndex }}">
                            <input type="hidden" name="quarters[{{ $index }}][milestones][{{ $mIndex }}][id]" value="{{ $milestone->id }}">
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>Milestone {{ $mIndex + 1 }}</strong>
                                <button type="button" class="btn btn-sm btn-danger remove-milestone" {{ $isDisabled }}>
                                    <i class="la la-minus"></i>
                                </button>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <label class="font-weight-bold">Milestone Description</label>
                                    <textarea class="form-control" name="quarters[{{ $index }}][milestones][{{ $mIndex }}][milestone]" 
                                              rows="2" {{ $isDisabled }}>{{ $milestone->milestone }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold">Status</label>
                                    <select class="form-control" name="quarters[{{ $index }}][milestones][{{ $mIndex }}][status]" {{ $isDisabled }}>
                                        @foreach($milestoneStatuses as $mStatus)
                                        <option value="{{ $mStatus }}" {{ $milestone->status == $mStatus ? 'selected' : '' }}>{{ $mStatus }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-sm btn-light-success add-milestone" {{ $isDisabled }}>
                        <i class="la la-plus"></i> Add Milestone
                    </button>
                </div>
                @endforeach
            @else
                <div class="quarter-item mb-4 p-4 border rounded" data-index="0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-weight-bold mb-0">Quarter 1</h5>
                        <button type="button" class="btn btn-sm btn-danger remove-quarter" {{ $isDisabled }}>
                            <i class="la la-trash"></i> Remove Quarter
                        </button>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="font-weight-bold">Quarter <span class="text-danger">*</span></label>
                            <select class="form-control" name="quarters[0][quarter]" {{ $isDisabled }} required>
                                <option value="">Select Quarter</option>
                                @foreach($quarters as $q)
                                <option value="{{ $q }}">{{ $q }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="milestones-container" data-quarter-index="0">
                        <h6 class="font-weight-bold mb-3">Milestones</h6>
                        <div class="milestone-item mb-3 p-3 bg-light rounded" data-milestone-index="0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>Milestone 1</strong>
                                <button type="button" class="btn btn-sm btn-danger remove-milestone" {{ $isDisabled }}>
                                    <i class="la la-minus"></i>
                                </button>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <label class="font-weight-bold">Milestone Description</label>
                                    <textarea class="form-control" name="quarters[0][milestones][0][milestone]" 
                                              rows="2" {{ $isDisabled }}></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold">Status</label>
                                    <select class="form-control" name="quarters[0][milestones][0][status]" {{ $isDisabled }}>
                                        @foreach($milestoneStatuses as $mStatus)
                                        <option value="{{ $mStatus }}">{{ $mStatus }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-light-success add-milestone" {{ $isDisabled }}>
                        <i class="la la-plus"></i> Add Milestone
                    </button>
                </div>
            @endif
        </div>

        <button type="button" class="btn btn-light-primary mt-3" id="add-quarter" {{ $isDisabled }}>
            <i class="la la-plus"></i> Add Quarter
        </button>
    </div>
</div>

@push('script')
<script>
    $(document).ready(function() {
        let quarterIndex = {{ isset($row) && $row->quarters->count() > 0 ? $row->quarters->count() : 1 }};

        // Add Quarter
        $('#add-quarter').on('click', function() {
            const quarterHtml = `
                <div class="quarter-item mb-4 p-4 border rounded" data-index="${quarterIndex}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-weight-bold mb-0">Quarter ${quarterIndex + 1}</h5>
                        <button type="button" class="btn btn-sm btn-danger remove-quarter">
                            <i class="la la-trash"></i> Remove Quarter
                        </button>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="font-weight-bold">Quarter <span class="text-danger">*</span></label>
                            <select class="form-control" name="quarters[${quarterIndex}][quarter]" required>
                                <option value="">Select Quarter</option>
                                @foreach($quarters as $q)
                                <option value="{{ $q }}">{{ $q }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="milestones-container" data-quarter-index="${quarterIndex}">
                        <h6 class="font-weight-bold mb-3">Milestones</h6>
                        <div class="milestone-item mb-3 p-3 bg-light rounded" data-milestone-index="0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>Milestone 1</strong>
                                <button type="button" class="btn btn-sm btn-danger remove-milestone">
                                    <i class="la la-minus"></i>
                                </button>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <label class="font-weight-bold">Milestone Description</label>
                                    <textarea class="form-control" name="quarters[${quarterIndex}][milestones][0][milestone]" rows="2"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold">Status</label>
                                    <select class="form-control" name="quarters[${quarterIndex}][milestones][0][status]">
                                        @foreach($milestoneStatuses as $mStatus)
                                        <option value="{{ $mStatus }}">{{ $mStatus }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-light-success add-milestone">
                        <i class="la la-plus"></i> Add Milestone
                    </button>
                </div>
            `;

            $('#quarters-container').append(quarterHtml);
            quarterIndex++;
        });

        // Remove Quarter
        $(document).on('click', '.remove-quarter', function() {
            const $quarterItem = $(this).closest('.quarter-item');
            
            if ($('.quarter-item').length > 1) {
                const quarterId = $quarterItem.find('input[name*="[id]"]').val();
                
                if (quarterId) {
                    // If quarter has an ID, show confirmation
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This quarter and all its milestones will be deleted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Add the quarter ID to deleted quarters tracking
                            if (!$('#deleted-quarters-container').length) {
                                $('#quarters-container').after('<div id="deleted-quarters-container"></div>');
                            }
                            $('#deleted-quarters-container').append(
                                `<input type="hidden" name="deleted_quarter_ids[]" value="${quarterId}">`
                            );
                            
                            // Remove the quarter from DOM
                            $quarterItem.remove();
                            reindexQuarters();
                        }
                    });
                } else {
                    // New quarter without ID, just remove it
                    $quarterItem.remove();
                    reindexQuarters();
                }
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Remove',
                    text: 'At least one quarter is required!',
                });
            }
        });

        // Add Milestone
        $(document).on('click', '.add-milestone', function() {
            const $quarterItem = $(this).closest('.quarter-item');
            const quarterIdx = $quarterItem.data('index');
            const $milestonesContainer = $quarterItem.find('.milestones-container');
            const milestoneCount = $milestonesContainer.find('.milestone-item').length;

            const milestoneHtml = `
                <div class="milestone-item mb-3 p-3 bg-light rounded" data-milestone-index="${milestoneCount}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Milestone ${milestoneCount + 1}</strong>
                        <button type="button" class="btn btn-sm btn-danger remove-milestone">
                            <i class="la la-minus"></i>
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <label class="font-weight-bold">Milestone Description</label>
                            <textarea class="form-control" name="quarters[${quarterIdx}][milestones][${milestoneCount}][milestone]" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold">Status</label>
                            <select class="form-control" name="quarters[${quarterIdx}][milestones][${milestoneCount}][status]">
                                @foreach($milestoneStatuses as $mStatus)
                                <option value="{{ $mStatus }}">{{ $mStatus }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            `;

            $milestonesContainer.append(milestoneHtml);
        });

        // Remove Milestone
        $(document).on('click', '.remove-milestone', function() {
            const $milestoneItem = $(this).closest('.milestone-item');
            const $milestonesContainer = $milestoneItem.closest('.milestones-container');
            const milestoneId = $milestoneItem.find('input[name*="[id]"]').val();

            if ($milestonesContainer.find('.milestone-item').length > 1) {
                if (milestoneId) {
                    // If milestone has an ID, show confirmation for soft delete
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This milestone will be deleted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $milestoneItem.remove();
                            reindexMilestones($milestonesContainer);
                        }
                    });
                } else {
                    $milestoneItem.remove();
                    reindexMilestones($milestonesContainer);
                }
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Remove',
                    text: 'At least one milestone is required per quarter!',
                });
            }
        });

        function reindexQuarters() {
            $('.quarter-item').each(function(index) {
                $(this).attr('data-index', index);
                $(this).find('h5').text('Quarter ' + (index + 1));
                
                // Update quarter select name
                $(this).find('select[name*="[quarter]"]').attr('name', `quarters[${index}][quarter]`);
                
                // Update hidden ID field if exists
                const $hiddenId = $(this).find('input[name*="quarters"][name*="[id]"]');
                if ($hiddenId.length) {
                    $hiddenId.attr('name', `quarters[${index}][id]`);
                }
                
                // Update milestones container
                $(this).find('.milestones-container').attr('data-quarter-index', index);
                
                // Reindex milestones within this quarter
                reindexMilestones($(this).find('.milestones-container'));
            });
        }

        function reindexMilestones($container) {
            const quarterIdx = $container.data('quarter-index');
            $container.find('.milestone-item').each(function(index) {
                $(this).attr('data-milestone-index', index);
                $(this).find('strong').text('Milestone ' + (index + 1));
                
                // Update milestone fields
                $(this).find('textarea').attr('name', `quarters[${quarterIdx}][milestones][${index}][milestone]`);
                $(this).find('select').attr('name', `quarters[${quarterIdx}][milestones][${index}][status]`);
                
                // Update hidden ID field if exists
                const $hiddenId = $(this).find('input[name*="milestones"][name*="[id]"]');
                if ($hiddenId.length) {
                    $hiddenId.attr('name', `quarters[${quarterIdx}][milestones][${index}][id]`);
                }
            });
        }
    });
</script>
@endpush

@push('css')
<style>
    .quarter-item {
        background: #f8f9fa;
        border: 2px solid #e4e6ef !important;
    }

    .milestone-item {
        background: #ffffff !important;
        border: 1px solid #e4e6ef;
    }

    .card-custom.card-stretch.gutter-b {
        border: 1px solid #e4e6ef;
        margin-bottom: 25px;
    }

    .card-custom.card-stretch.gutter-b .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e4e6ef;
    }
</style>
@endpush


