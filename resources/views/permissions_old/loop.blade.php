@if($collection)

@foreach ($collection as $item)
                                <tr>
                                    <th scope="row">{{ $item->id }}</th>
                                    <td>{{ $item->group->title }}</td>
                                    <td>{{ $item->permision_module_rule->rule_name }}</td>
                                 
                                    
                            </tr>
                            @endforeach
@else

<tr>
    <td colspan="7" style="text-align:center">No Data Found</td>                                   
</tr>

@endif