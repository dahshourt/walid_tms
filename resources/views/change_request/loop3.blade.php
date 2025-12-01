@php
    $roles_name = auth()->user()->roles->pluck('name');
@endphp

@if($collection && count($collection) > 0)
    @foreach ($collection as $item)
        @php
            // Fallback for default group
            $default_group = session('default_group') ?? auth()->user()->default_group;

            // Get technical team visibility flags
            $view_technical_team_flag = optional($item->getCurrentStatus()?->status)->view_technical_team_flag;
            $assigned_technical_teams = $item->technical_Cr
                ? $item->technical_Cr->technical_cr_team->pluck('group_id')->toArray()
                : [];
            $check_if_status_active = $item->technical_Cr
                ? $item->technical_Cr->technical_cr_team
                    ->where('group_id', $default_group)
                    ->where('status', '0')
                    ->count()
                : 0;

            // Generate security token
            $token = md5($item->id . $item->created_at . env('APP_KEY'));
        @endphp

        @if(!$view_technical_team_flag || ($view_technical_team_flag && in_array($default_group, $assigned_technical_teams) && $check_if_status_active))
            <tr>
                {{-- ✅ Change Request Number --}}
                @can('Edit ChangeRequest')
                    <td>
                        <a href='{{ url("$route") }}/{{ $item->id }}/edit?check_dm=1'>
                            {{ $item['cr_no'] }}
                        </a>
                    </td>
                @elsecan('Show ChangeRequest')
                    <td>
                        <a href='{{ url("$route") }}/{{ $item->id }}'>
                            {{ $item['cr_no'] }}
                        </a>
                    </td>
                @else
                    <td>{{ $item['cr_no'] }}</td>
                @endcan

                {{-- ✅ Basic Fields --}}
                <td>{{ $item->title }}</td>
                <td>
                    <span class="description-preview text-primary"
                          data-description="{{ e($item->description) }}"
                          role="button">
                        {{ \Illuminate\Support\Str::limit($item->description, 50) }}
                    </span>
                </td>
                <td>{{ $item->crHold?->resuming_date }}</td>
                <td>{{ $item->crHold?->holdReason?->name }}</td>
                <td>{{ $item->crHold?->justification }}</td>


                @can('edit hold cr')


                {{-- ✅ Action Buttons --}}
                <td class="align-middle">
                <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-success btn-sm _approved_continue mr-5"
                                data-id="{{ $item->id }}"
                                data-token="{{ $token }}">
                            ✅ {{ optional($item->getCurrentStatus()?->status)->status_name ?? '' }}
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm dis_approved_continue"
                                data-id="{{ $item->id }}"
                                data-token="{{ $token }}">
                            ❌ Promo Validation
                        </button>
                    </div>
                </td>
@endcan

            </tr>
        @endif
    @endforeach
@else
    <tr>
        <td colspan="8" class="text-center">No Data Found</td>
    </tr>
@endif
