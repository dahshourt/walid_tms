@php
    $roles_name = auth()->user()->roles->pluck('name');
    $current_user_is_just_a_viewer = count($roles_name) === 1 && $roles_name[0] === 'Viewer';
@endphp
@if($collection)
    @foreach ($collection as $item)
        <tr>

            @can('Edit ChangeRequest')
                <td>
                    @if(request('workflow_type') == 'Promo')

                        @if($item->getCurrentStatus() && isset($item->getCurrentStatus()->status)&&isset($status_promo_view) && in_array($item->getCurrentStatus()->status->id, $status_promo_view))
                            <a href='{{ url("$route") }}/{{ $item->id }}/edit'>{{ $item['cr_no'] }} </a>
                        @else
                            <a href='{{ url("$route") }}/{{ $item["id"] }}'>{{ $item['cr_no'] }} </a>
                        @endif
                    @else
                        @if($item->getCurrentStatus() && isset($item->getCurrentStatus()->status) && in_array($item->getCurrentStatus()->status->id, [64, 79,41,44]))
                            <a href='{{ url("$route") }}/{{ $item->id }}/edit?check_business=1'>{{ $item['cr_no'] }} </a>
                        @else
                            <a href='{{ url("$route") }}/{{ $item["id"] }}'>{{ $item['cr_no'] }} </a>
                        @endif
                    @endif
                </td>
            @else
                @can('Show ChangeRequest')
                    <td><a href='{{ url("$route") }}/{{ $item->id }}'>{{ $item['cr_no'] }} </a></td>
                @else
                    <td>{{ $item['cr_no'] }} </td>
                @endcan
            @endcan


            <th scope="row">{{ $item['title']}}</th>
            @php
                $cr_status = $item->getCurrentStatus()?->status;
                $cr_status_name = $cr_status?->status_name;
                if ($current_user_is_just_a_viewer) {
                    $high_level_status_name = $cr_status?->high_level?->name;
                    $cr_status_name = $high_level_status_name ?? $cr_status_name;
                }
            @endphp
            <td>{{ $cr_status_name }}</td>
            @if(!empty($roles_name) && isset($roles_name[0]) && $roles_name[0] != "Viewer")
                @if(request('workflow_type', 'In House') == 'In House')
                    <td>{{ $item['design_duration'] }}</td>
                    <td>{{ $item['start_design_time'] }}</td>
                    <td>{{ $item['end_design_time'] }}</td>
                    <td>{{ $item['develop_duration'] }}</td>
                    <td>{{ $item['start_develop_time'] }}</td>
                    <td>{{ $item['end_develop_time'] }}</td>
                    <td>{{ $item['test_duration'] }}</td>
                    <td>{{ $item['start_test_time'] }}</td>
                    <td>{{ $item['end_test_time'] }}</td>
                    <td>{{ $item['CR_duration'] }}</td>
                    <td>{{ $item['start_CR_time'] }}</td>
                    <td>{{ $item['end_CR_time'] }}</td>
                @endif
                @if(request('workflow_type') == 'Vendor')
                    <td>{{$item['release'] ?  $item['release']->name : 'No Release'}}</td>
                @endif
            @endif
            @if(request('workflow_type') == 'Promo')
                <td>{{ $item["created_at"] }}</td>
            @endif
            <td>
                <div class="d-inline-flex">
                    @can('Show ChangeRequest')

                        <a href='{{ url("$route") }}/{{ $item["id"] }}' class="btn btn-sm btn-clean btn-icon mr-2"
                           title="Show details">
                                            <span class="svg-icon svg-icon-md">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                     height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                        <path
                                                            d="M12,2 C6.477,2 2,6.477 2,12 C2,17.523 6.477,22 12,22 C17.523,22 22,17.523 22,12 C22,6.477 17.523,2 12,2 Z M12,19.5 C7.805,19.5 4.5,16.195 4.5,12 C4.5,7.805 7.805,4.5 12,4.5 C16.195,4.5 19.5,7.805 19.5,12 C19.5,16.195 16.195,19.5 12,19.5 Z M11,16 L13,16 L13,13 L11,13 L11,16 Z M11,11 L13,11 L13,8 L11,8 L11,11 Z"
                                                            fill="#000000"></path>
                                                    </g>
                                                </svg>
                                            </span>
                        </a>
                    @endcan
                    @can('Edit ChangeRequest')
                        @if(request('workflow_type') == 'Promo')

                            @if($item->getCurrentStatus() && isset($item->getCurrentStatus()->status)&&isset($status_promo_view) && in_array($item->getCurrentStatus()->status->id, $status_promo_view))

                                <a href='{{url("$route")}}/{{ $item["id"] }}/edit'
                                   class="btn btn-sm btn-clean btn-icon mr-2" title="Edit details">
                                            <span class="svg-icon svg-icon-md">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                     height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                        <path
                                                            d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15"
                                                              height="2" rx="1"></rect>
                                                    </g>
                                                </svg>
                                            </span>
                                </a>
                            @endif
                        @else
                            @if($item->getCurrentStatus() && isset($item->getCurrentStatus()->status) && in_array($item->getCurrentStatus()->status->id, [64, 79,41,44,\App\Services\StatusConfigService::getStatusId('pending_agreed_business'),\App\Services\StatusConfigService::getStatusId('prototype_approval_business')]))
                                <a href='{{url("$route")}}/{{ $item["id"] }}/edit?check_business=1'
                                   class="btn btn-sm btn-clean btn-icon mr-2" title="Edit details">
                                            <span class="svg-icon svg-icon-md">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                     height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                        <path
                                                            d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15"
                                                              height="2" rx="1"></rect>
                                                    </g>
                                                </svg>
                                            </span>
                                </a>
                            @endif
                        @endif
                    @endcan
                </div>

            </td>
        </tr>
    @endforeach
@else

    <tr>
        <td colspan="7" style="text-align:center">No Data Found</td>
    </tr>

@endif
