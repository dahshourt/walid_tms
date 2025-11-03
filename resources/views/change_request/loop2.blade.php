@php
    $roles_name = auth()->user()->roles->pluck('name');
@endphp
@if($collection)

@foreach ($collection as $item)
@php

if (session('default_group')) {
    $default_group = session('default_group');
} else {
    $default_group = auth()->user()->default_group;
}
$view_technical_team_flag = $item->getCurrentStatus()->status->view_technical_team_flag;
$assigned_technical_teams = $item->technical_Cr? $item->technical_Cr->technical_cr_team->pluck('group_id')->toArray() : [];
$check_if_status_active = $item->technical_Cr?$item->technical_Cr->technical_cr_team->where('group_id',$default_group)->where('status','0')->count() : 0;
@endphp

@if(!$view_technical_team_flag || ($view_technical_team_flag && in_array($default_group, $assigned_technical_teams) && $check_if_status_active))
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
                                    <th scope="row">{{ $item->description}}</th>
                                    <td>{{ $item->getCurrentStatus() && $item->getCurrentStatus()->status ? $item->getCurrentStatus()->status->status_name : "" }}</td>
                                    @if(!empty($roles_name) && isset($roles_name[0]) && $roles_name[0] != "Viewer")
                                   
                                    @endif
                                    @php
    $token = md5($item->id . $item->created_at . env('APP_KEY'));
@endphp
@can('Edit cr pending cap')
<td>
    <div class="d-inline-flex gap-2">
        <button type="button" class="btn btn-outline-success btn-sm _approved_active_cab"
                data-id="{{ $item->id }}"
                data-token="{{ $token }}">
            ✅ Approved
        </button>
        <button type="button" class="btn btn-outline-danger btn-sm _rejected_active_cab ml-5"
                data-id="{{ $item->id }}"
                data-token="{{ $token }}">
            ❌ Need to review
        </button>
    </div>
</td>
@endcan

                                        
                            </tr>
                            @endif
                            @endforeach
@else

<tr>
    <td colspan="7" style="text-align:center">No Data Found</td>                                   
</tr>

@endif