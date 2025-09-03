{{-- partials/selects/rejection-reason-select.blade.php --}}
<select name="{{ $item->CustomField->name }}" class="form-control form-control-lg">
    <option value="">Select</option>
    
    @if(!empty($rejects))
        @foreach($rejects as $reject)
            <option value="{{$reject?->id}}">{{$reject?->name}}</option>
        @endforeach
    @endif
</select>