@if($cr)


                                <tr>
                                    <th scope="row">
									@if(in_array($cr->id, $crs_in_queues->toArray()))
                                        @if(!(($cr->workflow_type_id == 5) && (in_array($cr->Req_status()->latest('id')->first()?->new_status_id, [66, 67, 68, 69]))))
                                        @can('Edit ChangeRequest')
                                            <a href='{{ url("$route") }}/{{ $cr->id }}/edit'>{{ $cr->id }} </a>
                                        @endcan 
										@endif
                                    @else
										 <a href='{{ url("$route") }}/{{ $cr->id }}'>{{ $cr->id }} </a>
									@endif		
                                    </th>
                                    @if($cr->workflow_type_id == 5)
                                        <td>{{ $cr->title }}</td>
                                        <td>{{ $cr->description }}</td>
                                        <td>{{ $cr->getCurrentStatus()?->status?->status_name }}</td>
                                        <td>{{ $cr->application->name }}</td>
                                        <td>{{ @$cr->Release->name }}</td>
                                        <td>{{ @$cr->Release->go_live_planned_date }}</td>
                                        <td>{{ @$cr->Release->planned_start_iot_date }}</td>
                                        <td>{{ @$cr->Release->planned_end_iot_date }}</td>
                                        <td>{{ @$cr->Release->planned_start_e2e_date }}</td>
                                        <td>{{ @$cr->Release->planned_end_e2e_date }}</td>
                                        <td>{{ @$cr->Release->planned_start_uat_date }}</td>
                                        <td>{{ @$cr->Release->planned_end_uat_date }}</td>
                                        <td>{{ @$cr->Release->planned_start_smoke_test_date }}</td>
                                        <td>{{ @$cr->Release->planned_end_smoke_test_date }}</td>
                                    @else
                                    <td>{{ $cr->title }}</td>
                                        <td>{{ $cr->description }}</td>
                                        <td>{{ $cr->getCurrentStatus()?->status?->status_name }}</td>
                                        <td>{{ $cr->application->name }}</td>
                                        <td>{{ $cr->design_duration }}</td>
                                        <td>{{ $cr->start_design_time }}</td>
                                        <td>{{ $cr->end_design_time }}</td>
                                        <td>{{ $cr->develop_duration }}</td> 
                                        <td>{{ $cr->start_develop_time }}</td>
                                        <td>{{ $cr->end_develop_time }}</td>
                                        <td>{{ $cr->test_duration }}</td> 
                                        <td>{{ $cr->start_test_time }}</td>
                                        <td>{{ $cr->end_test_time }}</td>
                                        <td>{{ $cr->created_at }}</td>
                                        <td>{{ $cr->updated_at }}</td>
                                    @endif

                                    <td>
                                        <div class="d-inline-flex">
                                            @can('Show ChangeRequest')
                                                <a href='{{ url("$route") }}/{{ $cr->id }}' class="btn btn-sm btn-clean btn-icon mr-2" title="Show details">
                                                    <span class="svg-icon svg-icon-md">
                                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                                <path d="M12,2 C6.477,2 2,6.477 2,12 C2,17.523 6.477,22 12,22 C17.523,22 22,17.523 22,12 C22,6.477 17.523,2 12,2 Z M12,19.5 C7.805,19.5 4.5,16.195 4.5,12 C4.5,7.805 7.805,4.5 12,4.5 C16.195,4.5 19.5,7.805 19.5,12 C19.5,16.195 16.195,19.5 12,19.5 Z M11,16 L13,16 L13,13 L11,13 L11,16 Z M11,11 L13,11 L13,8 L11,8 L11,11 Z" fill="#000000"></path>
                                                            </g>
                                                        </svg>
                                                    </span>
                                                </a>
                                            @endcan
                                            @if(in_array($cr->id, $crs_in_queues->toArray()))
                                                @if(!(($cr->workflow_type_id == 5) && (in_array($cr->Req_status()->latest('id')->first()?->new_status_id, [66, 67, 68, 69]))))
                                                    @can('Edit ChangeRequest')
                                                        <a href='{{ url("$route") }}/{{ $cr->id }}/edit' class="btn btn-sm btn-clean btn-icon mr-2" title="Edit details">
                                                            <span class="svg-icon svg-icon-md">
                                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                                        <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                                                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    @endcan
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                            </tr>
                            
@else

<tr>
    <td colspan="7" style="text-align:center">No Data Found</td>                                   
</tr>

</table>
</div>

@endif