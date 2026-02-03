{{-- partials/select-fields/status-option-item.blade.php --}}

<option value="{{ $status->id }}" data-status-name="{{ $status->workflowstatus[0]->to_status->status_name }}"
    data-defect="{{ $cr->defects->where('status_id', 85)->count() > 0 ? 1 : 0 }}">
    @if($status->to_status_label)
        {{ $status->to_status_label }}
    @else
        {{ $status->workflowstatus[0]->to_status->status_name }}
    @endif
</option>