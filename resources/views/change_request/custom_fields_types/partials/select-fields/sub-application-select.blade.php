{{-- partials/select-fields/sub-application-select.blade.php --}}
@if(!empty($sub_applications) && count($sub_applications) > 0)
    <select name="{{ $item->CustomField->name }}" class="form-control form-control-lg" {{ (isset($item->enable)&&($item->enable!=1)) ? "disabled" : "" }}>
        <option value="">Select</option>
        @foreach($sub_applications as $sub_application)
            <option value="{{$sub_application?->id}}" {{ $custom_field_value == $sub_application->id ? 'selected' : '' }}>
                {{$sub_application?->name}}
            </option>
        @endforeach
    </select>
@endif