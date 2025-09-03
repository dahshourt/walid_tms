{{-- partials/scripts/select-field-scripts.blade.php --}}

{{-- PHP variables for JavaScript --}}
@php
    $def1 = isset($cr) ? $cr->defects()->count() : 0;
    $def2 = isset($cr) ? $cr->defects()->whereIn('status_id', [86, 87])->count() : 0;
    $status_id = isset($cr) ? $cr->getCurrentStatus()?->status?->id ?? null : null;
@endphp

@include('change_request.custom_fields_types.partials.scripts.technical-teams-handler')
@include('change_request.custom_fields_types.partials.scripts.reason-wrapper-handler')

@if($def1 != $def2)
    @include('change_request.custom_fields_types.partials.scripts.defect-confirmation')
@endif