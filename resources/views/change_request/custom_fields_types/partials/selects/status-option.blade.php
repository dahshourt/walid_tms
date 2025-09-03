{{-- partials/selects/status-option.blade.php --}}
@php
    $toStatus = $status->workflowstatus[0]->to_status ?? null;
@endphp

@if($toStatus)
    @php
        $testDurationEmpty = $cr->test_duration === 0 || $cr->test_duration === null || $cr->test_duration === '';
        $shouldShowOption = false;
        
        if ($testDurationEmpty) {
            $shouldShowOption = in_array($toStatus->id, [20, 48]);
        } else {
            if ($toStatus->id == 20 || $toStatus->id == 74) {
                $shouldShowOption = true;
            } elseif ($toStatus->id == 21) {
                $shouldShowOption = true;
            } elseif (!in_array($toStatus->id, [48])) {
                $shouldShowOption = true;
            }
        }
        
        $displayText = $toStatus->high_level->name ?? $status->to_status_label ?? $toStatus->status_name;
    @endphp

    @if($shouldShowOption)
        <option value="{{ $status->id }}" 
                {{ $custom_field_value == $status->id ? 'selected' : '' }}
                data-status-name="{{ $toStatus->status_name }}"
                data-defect="{{ $toStatus->defect }}">
            {{ $displayText }}
        </option>
    @endif
@endif