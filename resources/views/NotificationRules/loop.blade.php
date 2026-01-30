@if($collection)

@foreach ($collection as $item)
    @php
        // Format event class for display
        $eventDisplay = str_contains($item->event_class, 'Created') ? 'CR Created' : 'CR Status Updated';
        
        // Format conditions for display
        $conditionDisplay = 'No conditions';
        if ($item->conditions && is_array($item->conditions)) {
            $condLabels = [
                'workflow_type' => 'Workflow =',
                'workflow_type_not' => 'Workflow ≠',
                'new_status_id' => 'New Status =',
                'old_status_id' => 'Old Status =',
            ];
            $parts = [];
            foreach ($item->conditions as $type => $value) {
                $label = $condLabels[$type] ?? $type;
                if (in_array($type, ['workflow_type', 'workflow_type_not'])) {
                    $workflow = \App\Models\WorkFlowType::find($value);
                    $valueName = $workflow ? $workflow->name : "ID: $value";
                } elseif (in_array($type, ['new_status_id', 'old_status_id'])) {
                    $status = \App\Models\Status::find($value);
                    $valueName = $status ? $status->status_name : "ID: $value";
                } else {
                    $valueName = $value;
                }
                $parts[] = "$label $valueName";
            }
            $conditionDisplay = implode(', ', $parts);
        }
        
        // Count recipients by channel
        $toCount = $item->recipients->where('channel', 'to')->count();
        $ccCount = $item->recipients->where('channel', 'cc')->count();
        $bccCount = $item->recipients->where('channel', 'bcc')->count();
        $recipientSummary = [];
        if ($toCount > 0) $recipientSummary[] = "{$toCount} TO";
        if ($ccCount > 0) $recipientSummary[] = "{$ccCount} CC";
        if ($bccCount > 0) $recipientSummary[] = "{$bccCount} BCC";
    @endphp
    <tr class="text-center">
        <td>{{ $item->id }}</td>
        <td>{{ $item->name }}</td>
        <td>
            <span class="label label-{{ str_contains($item->event_class, 'Created') ? 'primary' : 'info' }} label-inline font-weight-bold">
                {{ $eventDisplay }}
            </span>
        </td>
        <td>
            @if(count($recipientSummary) > 0)
                <span class="text-dark-50">{{ implode(', ', $recipientSummary) }}</span>
            @else
                <span class="text-muted">None</span>
            @endif
        </td>
        <td>
            <span class="label label-{{ $item->is_active ? 'success' : 'danger' }} label-inline font-weight-bold">
                {{ $item->is_active ? 'Active' : 'Inactive' }}
            </span>
        </td>
        <td>
            <div class="d-inline-flex justify-content-center w-100">
                {{-- Show Button --}}
                <a href='{{ url("$route") }}/{{ $item->id }}' class="btn btn-icon btn-light btn-hover-primary btn-sm mr-2" title="View Rule">
                    <span class="svg-icon svg-icon-md svg-icon-primary">
                        <!--View Icon-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                             height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path
                                    d="M12,20 C7.581722,20 4,16.418278 4,12 C4,7.581722 7.581722,4 12,4 C16.418278,4 20,7.581722 20,12 C20,16.418278 16.418278,20 12,20 Z M12,6 C8.6862915,6 6,8.6862915 6,12 C6,15.3137085 8.6862915,18 12,18 C15.3137085,18 18,15.3137085 18,12 C18,8.6862915 15.3137085,6 12,6 Z"
                                    fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                <path
                                    d="M12,16 C14.209139,16 16,14.209139 16,12 C16,9.790861 14.209139,8 12,8 C9.790861,8 8,9.790861 8,12 C8,14.209139 9.790861,16 12,16 Z"
                                    fill="#000000" fill-rule="nonzero"/>
                            </g>
                        </svg>
                    </span>
                </a>

                @can('Edit Notification Rules')
                {{-- Edit Button --}}
                <a href='{{url("$route")}}/{{ $item->id }}/edit' class="btn btn-icon btn-light btn-hover-primary btn-sm mr-2" title="Edit Rule">
                    <span class="svg-icon svg-icon-md svg-icon-primary">
                        <!--Edit Icon-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                             height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path
                                    d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                            </g>
                        </svg>
                    </span>
                </a>
                @endcan

                {{--
                @can('Delete Notification Rules')
                <form action='{{ url("$route") }}/{{ $item->id }}' method="POST" onsubmit="return confirm('Are you sure you want to delete this rule? This will also delete all associated recipients.');" style="display:inline;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="btn btn-icon btn-light btn-hover-primary btn-sm mr-2" title="Delete Rule">
                        <span class="svg-icon svg-icon-md svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero"></path>
                                    <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                                </g>
                            </svg>
                        </span>
                    </button>
                </form>
                @endcan
                --}}
            </div>
        </td>
    </tr>
@endforeach

@else
<tr>
    <td colspan="8" style="text-align:center">No Notification Rules Found</td>
</tr>
@endif
