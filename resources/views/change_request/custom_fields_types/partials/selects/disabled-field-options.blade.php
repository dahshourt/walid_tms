{{-- partials/selects/disabled-field-options.blade.php --}}
@php
    $selectedValue = isset($cr) ? old($item->CustomField->name, $custom_field_value) : "";
@endphp

@if($selectedValue)
    @foreach($item->CustomField->getCustomFieldValue() as $value)
        @if($value->id == $selectedValue)
            <option value="{{ $value->id }}" selected>{{ $value->name }}</option>
        @endif
    @endforeach
@else
    <option value="">Select</option>
@endif