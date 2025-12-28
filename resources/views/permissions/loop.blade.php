@if($collection)

@foreach ($collection as $item)
                                <tr>
                                    <th scope="row">{{ $item->id }}</th>
                                    <td>{{ $item->name }}</td>  
                                    
                                    <td>
                                        <div class="d-inline-flex">

                                        @can('Edit Permission')
                                            
                                        <a href='{{url("$route")}}/{{ $item->id }}/edit' class="btn btn-sm btn-clean btn-icon mr-2" title="Edit Permission">
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

                                        @can('Delete Permission')

                                        <form action='{{ url("$route") }}/{{ $item->id }}' method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-sm btn-clean btn-icon mr-2" title="Delete Permission">
                                                <span class="svg-icon svg-icon-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24"></rect>
                                                            <path d="M16,9 L16,19 C16,20.1045695 15.1045695,21 14,21 L10,21 C8.8954305,21 8,20.1045695 8,19 L8,9 L16,9 Z M19,7 L5,7 L5,9 L19,9 L19,7 Z M14,3 C14,2.44771525 13.5522847,2 13,2 L11,2 C10.4477153,2 10,2.44771525 10,3 L10,4 L14,4 L14,3 Z" fill="#000000"></path>
                                                        </g>
                                                    </svg>
                                                </span>
                                            </button>
                                        </form>

                                        @endcan
                                    
                                        
                                    </td>
                                    
                                </div>
                                </tr>
                            @endforeach
@else

<tr>
    <td colspan="7" style="text-align:center">No Data Found</td>                                   
</tr>

@endif