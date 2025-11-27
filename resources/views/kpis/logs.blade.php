
<div class="modal" id="modal" tabindex="-1" role="dialog" aria-labelledby="logModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="logModalLabel">
                    <i class="flaticon-list-3 mr-2 text-white"></i> KPI History Logs
                </h5>
                <button type="button" class="close text-white" id="close_logs" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light">
                <div class="timeline timeline-6 mt-3">
                    @forelse($logs as $key => $log)
                        <div class="timeline-item align-items-start">
                            <!-- Label -->
                            <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">
                                {{ $log->created_at->format('d M Y') }} {{ $log->created_at->format('h:i A') }}
                            </div>

                            <!-- Badge -->
                            <div class="timeline-badge">
                                <i class="fa fa-genderless text-{{ $key % 2 == 0 ? 'primary' : 'success' }} icon-xl"></i>
                            </div>

                            <!-- Content -->
                            <div class="timeline-content d-flex flex-column pl-3">
                                <span class="font-weight-bolder text-dark-75">
                                    {{ $log->log_text }}
                                </span>
                                <span class="text-muted font-size-sm mt-1">
                                    By: <span class="text-primary font-weight-bold">{{ $log->user->name ?? 'Unknown' }}</span> 
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <span class="text-muted font-weight-bold">No logs available.</span>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" id="close_logs_btn">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Ensure close buttons work
    document.getElementById('close_logs').onclick = function() {
        document.getElementById('modal').style.display = "none";
    };
    document.getElementById('close_logs_btn').onclick = function() {
        document.getElementById('modal').style.display = "none";
    };
</script>

<style>
    .modal {
        background: rgba(0, 0, 0, 0.5);
    }
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    .timeline.timeline-6 .timeline-item .timeline-label {
        width: 85px;
    }
    .timeline.timeline-6 .timeline-item .timeline-badge {
        margin-left: 10px;
        margin-right: 10px;
    }
</style>