{{-- partials/select-fields/application-select.blade.php --}}
<select name="{{ $item->CustomField->name }}" class="form-control form-control-lg">
    <option value="{{$target_system->id}}">{{$target_system->name}}</option>
</select>