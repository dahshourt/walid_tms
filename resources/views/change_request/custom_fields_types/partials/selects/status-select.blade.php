{{-- partials/selects/status-select.blade.php --}}
<select name="{{ $item->CustomField->name }}" id="{{ $item->CustomField->name }}" class="form-control form-control-lg">
    {{-- Default selected option from current workflow status --}}
    <option value="{{ $cr->getCurrentStatus()?->status?->id ?? '' }}" disabled selected>
        {{ $cr->getCurrentStatus()?->status?->status_name ?? 'Select Status' }}
    </option>

    @foreach($cr->set_status as $status)
        @include('change_request.custom_fields_types.partials.selects.status-option', ['status' => $status])
    @endforeach
</select>

<div id="reason-wrapper"></div>