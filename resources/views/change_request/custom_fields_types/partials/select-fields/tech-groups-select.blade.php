{{-- partials/select-fields/tech-group-select.blade.php --}}
@if(!empty($technical_groups))
    <select name="{{ $item->CustomField->name }}" class="form-control form-control-lg" {{ (isset($item->enable)&&($item->enable!=1)) ? "disabled" : "" }} {{ (isset($item->enable)&&($item->enable ==1) && $technical_groups->count() > 1)  ? 'required' : '' }}  >
		@if($technical_groups->count() > 1)
        <option value="">Select</option>
		@endif
        @foreach($technical_groups as $technical_groups)
            <option value="{{$technical_groups?->id}}" {{ $custom_field_value == $technical_groups->id ? 'selected' : '' }}>
                {{$technical_groups?->title}}
            </option>
        @endforeach
		
    </select>
@endif