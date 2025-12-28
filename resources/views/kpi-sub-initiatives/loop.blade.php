@forelse($collection as $row)
<tr>
    <td>{{ $row->id }}</td>
    <td>{{ $row->name }}</td>
    <td>{{ $row->initiative->name ?? 'N/A' }}</td>
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
        @can('Edit KPI Sub-Initiatives')
        <a href="{{ route('kpi-sub-initiatives.edit', $row->id) }}" 
           class="btn btn-sm btn-clean btn-icon" 
           title="Edit">
            <i class="la la-edit"></i>
        </a>
        @endcan
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center">No KPI sub-initiatives found.</td>
</tr>
@endforelse



