

{{-- partials/select-fields/deployment-impact-select.blade.php --}}
<select name="{{ $item->CustomField->name }}" class="form-control form-control-lg">
    <option value="">Select</option>
    @foreach($item->CustomField->getCustomFieldValue() as $value)
        @if(in_array($value->id, $ApplicationImpact->pluck('impacts_id')->toArray()))
            <option value="{{ $value->id }}" {{ $custom_field_value == $value->id ? 'selected' : '' }}>
                {{ $value->name }}
            </option>
        @endif
        @if(empty($ApplicationImpact->pluck('impacts_id')->toArray()))
            <option value="{{ $value->id }}" {{ $custom_field_value == $value->id ? 'selected' : '' }}>
                {{ $value->name }}
            </option>
        @endif
    @endforeach
</select>
