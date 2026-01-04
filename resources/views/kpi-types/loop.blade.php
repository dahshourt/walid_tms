@forelse($collection as $row)
<tr>
    <td>{{ $row->id }}</td>
    <td>{{ $row->name }}</td>
    <td>
        <span class="switch switch-outline switch-icon switch-success">
            <label>
                <input type="checkbox" 
                       class="status-toggle" 
                       data-id="{{ $row->id }}"
                       data-name="{{ $row->name }}"
                       {{ $row->status == '1' ? 'checked' : '' }} />
                <span></span>
            </label>
        </span>
    </td>
    <td>
        @can('Edit KPI Types')
        <a href="{{ route('kpi-types.edit', $row->id) }}" 
           class="btn btn-sm btn-clean btn-icon" 
           title="Edit">
            <i class="la la-edit"></i>
        </a>
        @endcan
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="text-center">No KPI types found.</td>
</tr>
@endforelse



