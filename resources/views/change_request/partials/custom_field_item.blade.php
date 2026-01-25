@if($item->CustomField->name == 'on_behalf')
    @php
        $crTeamAdminGroup = config('constants.group_names.cr_team');
        $isCrAdmin = false;
        if (auth()->check()) {
            $isCrAdmin = auth()->user()->user_groups()->whereHas('group', function ($q) use ($crTeamAdminGroup) {
                $q->where('title', $crTeamAdminGroup);
            })->exists();
        }
    @endphp
    @if(!$isCrAdmin)
        @return
    @endif
@endif

@php 
        $custom_field_value = null;
    if (isset($cr)) {
        $custom_field_value = $cr->change_request_custom_fields->where('custom_field_name', $item->CustomField->name)->sortByDesc('id')->first();
        $custom_field_value = $custom_field_value ? $custom_field_value->custom_field_value : $cr->{$item->CustomField->name};
    }
@endphp

@include("$view.custom_fields_types.file")
@include("$view.custom_fields_types.input")
@include("$view.custom_fields_types.checkbox")
@include("$view.custom_fields_types.select")
@include("$view.custom_fields_types.textarea")
@include("$view.custom_fields_types.multiselect")
@include("$view.custom_fields_types.button")
@include("$view.custom_fields_types.radio")
@include("$view.custom_fields_types.date")
@include("$view.custom_fields_types.datetime")