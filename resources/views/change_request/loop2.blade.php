@php
    $default_group = session('default_group') ?: auth()->user()->default_group;
@endphp

@forelse ($collection as $item)
    @if($item->shouldShowToUser($default_group))
        <tr>
            @can('Edit CR_Pending')
                <td><a href='{{ url("$route") }}/{{ $item->id }}/edit?check_dm=1'>{{ $item['cr_no'] }} </a></td>
            @else
                @can('Show CR_Pending')
                    <td><a href='{{ url("$route") }}/{{ $item->id }}'>{{ $item['cr_no'] }} </a></td>
                @else
                    <td>{{ $item['cr_no'] }} </td>
                @endcan
            @endcan

            <th scope="row">{{ $item->title}}</th>
            <th scope="row">
                <span class="description-preview text-primary"
                      data-description="{{ e($item->description) }}"
                      role="button">
                    {{ \Illuminate\Support\Str::limit($item->description, 50) }}
                </span>
            </th>
            <td>{{ $item->getCurrentStatus() && $item->getCurrentStatus()->status ? $item->getCurrentStatus()->status->status_name : "" }}</td>

            @can('Edit cr pending cap')
                <td>
                    <div class="d-inline-flex gap-2">
                        <button type="button" class="btn btn-outline-success btn-sm _approved_active_cab"
                                data-id="{{ $item->id }}"
                                data-workflow="{{ $item->getSetStatus()->where('workflow_type', '0')->pluck('id')->first() }}"
                                data-token="{{ $item->generateActionToken() }}">
                            ✅ Approved
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm _rejected_active_cab ml-5"
                                data-id="{{ $item->id }}"
                                data-workflow="{{ $item->getSetStatus()->where('workflow_type', '1')->pluck('id')->first() }}"
                                data-token="{{ $item->generateActionToken() }}">
                            ❌ Need to review
                        </button>
                    </div>
                </td>
            @endcan
        </tr>
    @endif
@empty
    <tr>
        <td colspan="7" style="text-align:center">No Data Found</td>
    </tr>
@endforelse