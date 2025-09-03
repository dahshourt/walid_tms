{{-- partials/selects/permission-based-options.blade.php --}}
@cannot('Set Time For Another User')
    @if(in_array($item->CustomField->name, ['tester_id', 'designer_id', 'developer_id']))
        <option value="{{ auth()->user()->id }}" selected>{{ auth()->user()->name }}</option>
    @endif
@endcannot