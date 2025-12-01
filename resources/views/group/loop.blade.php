@if($collection)
    @foreach ($collection as $item)
        <tr>
            <th scope="row">{{ $item->id }}</th>
            <td>{{ $item->title }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->head_group_name }}</td>
            <td>{{ $item->head_group_email }}</td>
            @can('Active Group')
                <td>
            <span
                class="label label-lg {{ $item->active ? 'label-light-success' : 'label-light-danger' }} label-inline _change_active"
                data-id="{{ $item->id }}"
            >
        {{ $item->active ? 'Active' : 'Inactive' }}
    </span>
                </td>
            @endcan
            @can('Edit Group')
                <td>
                    <a href='{{url("$route")}}/{{ $item->id }}/edit' class="btn btn-sm btn-clean btn-icon mr-2"
                       title="Edit Group">
                    <span class="svg-icon svg-icon-md">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                             height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path
                                    d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                            </g>
                        </svg>
                    </span>
                    </a>

                    @hasrole('Super Admin')
                    <a href='{{url("group/statuses")}}/{{ $item->id }}' class="btn btn-outline-success">
                        <i class="flaticon2-poll-symbol"></i> Group Statuses
                    </a>
                    @endhasrole


                </td>
            @endcan
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="7" style="text-align:center">No Data Found</td>
    </tr>
@endif
