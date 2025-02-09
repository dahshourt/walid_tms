@if($collection)

@foreach ($collection as $item)
                                <tr>
                                    <th scope="row">{{ $item->id }}</th>
                                    <td>{{ $item->system->name }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    @can('Edit Cab User')
                                    <td>
                                    @if($item->active)
                                        <span class="label label-lg label-light-success label-inline _change_active" data-id="{{ $item->id }}">Active</span>
                                    @else
                                        <span class="label label-lg label-light-danger label-inline _change_active" data-id="{{ $item->id }}">Inactive</span>
                                    @endif
                                    </td>
                                    @endcan
                                    
                            </tr>
                            @endforeach
@else

<tr>
    <td colspan="7" style="text-align:center">No Data Found</td>                                   
</tr>

@endif