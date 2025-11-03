{{-- partials/select-fields/status-options.blade.php --}}

{{-- Default selected option from current workflow status --}}
<option value="{{ $cr->getCurrentStatus()?->status?->id ?? '' }}" disabled selected>
    {{ $cr->getCurrentStatus()?->status?->status_name ?? 'Select Status' }}
</option>

@foreach($cr->set_status as $status)
    @php
        $toStatus = $status->workflowstatus[0]->to_status ?? null;
    @endphp

    @if($toStatus)
        {{-- If test_duration is 0, null, or empty: allow ID 20 --}}
        @if($cr->test_duration === 0 || $cr->test_duration === null || $cr->test_duration === '')
            @if($toStatus->id == 20 || $toStatus->id == 48)
                @include('change_request.custom_fields_types.partials.select-fields.status-option-item', compact('status', 'toStatus', 'custom_field_value'))
            @else
                @include('change_request.custom_fields_types.partials.select-fields.status-option-item', compact('status', 'custom_field_value'))
            @endif
        @else
            {{-- test_duration has a value --}}
            @if($toStatus->id == 20)
                @include('change_request.custom_fields_types.partials.select-fields.status-option-item', compact('status', 'custom_field_value'))
            @endif

            @if($toStatus->id != 20)
                @if($toStatus->id == 74)
                    @include('change_request.custom_fields_types.partials.select-fields.status-option-item', compact('status', 'toStatus', 'custom_field_value'))
                @else
                    @if($toStatus->id != 48)
                        @if($toStatus->id == 21)
                            @include('change_request.custom_fields_types.partials.select-fields.status-option-item', compact('status', 'toStatus', 'custom_field_value'))
                        @else
                            @include('change_request.custom_fields_types.partials.select-fields.status-option-item', compact('status', 'custom_field_value'))
                        @endif
                    @endif
                @endif
            @endif
        @endif
    @endif
@endforeach
