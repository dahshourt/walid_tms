{{-- partials/select-fields/status-options.blade.php --}}

{{-- Default selected option from current workflow status --}}
<option value="{{ $cr->getCurrentStatus()?->status?->id ?? '' }}" disabled selected>
    @php
        $currentStatusName = $cr->getCurrentStatus()?->status?->status_name ?? 'Select Status';
        if ($cr->isDependencyHold()) {
            $blockingCrs = $cr->getBlockingCrNumbers();
            $crList = !empty($blockingCrs) ? ' (CR#' . implode(', CR#', $blockingCrs) . ')' : '';
            $currentStatusName = 'Design Estimation - Pending Dependency' . $crList;
        }
    @endphp
    @php
        $need_design = optional($cr->changeRequestCustomFields->where('custom_field_name', 'need_design')->first())->custom_field_value;
    @endphp
    {{ $currentStatusName }}


</option>

@php
    if ($cr->workflow_type_id == 9)  // promo   
    {
        if ($currentStatusName == "SA FB") {
            if (!$need_design)
                $need_design = "no";
            $excludeId = config('change_request.need_design_exclude_status.' . $need_design . '.id');
            $cr->set_status = $cr->set_status->filter(function ($item) use ($excludeId) {
                return ($item->workflowstatus[0]->to_status_id ?? null) != $excludeId;
            });
        }
    }
@endphp



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