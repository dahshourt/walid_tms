 @if(isset($changeRequest)&&$changeRequest)


                                <tr>
                                   
                                    <th scope="row">{{ isset($changeRequest->id)?$changeRequest->id:'' }}</th>
                                    <td>{{  isset($changeRequest->title)?$changeRequest->title :'' }}</td>
                                   
                                    <td>{{ isset($release->name)?$release->name:'' }}</td>
                                    <td>{{ isset($release->go_live_planned_date)?$release->go_live_planned_date:'' }}</td>
                                    <td>{{isset($release->planned_start_iot_date)? $release->planned_start_iot_date :''}}</td>
                                    <td>{{ isset($release->planned_end_iot_date)?$release->planned_end_iot_date:'' }}</td>
                                    <td>{{isset($release->planned_start_e2e_date)? $release->planned_start_e2e_date :''}}</td>
                                    <td>{{ isset($release->planned_end_e2e_date)?$release->planned_end_e2e_date:'' }}</td>
                                    <td>{{isset($release->planned_start_uat_date)? $release->planned_start_uat_date :''}}</td>
                                    <td>{{ isset($release->planned_end_uat_date)?$release->planned_end_uat_date:'' }}</td>
                                    <td>{{isset($release->planned_start_smoke_test_date)? $release->planned_start_smoke_test_date :''}}</td>
                                    <td>{{ isset($release->planned_end_smoke_test_date)?$release->planned_end_smoke_test_date:'' }}</td>
                                   
                                    <td>{{ isset($release->status->name)?$release->status->name :''}}</td>
                                    <td style="display: none;">
        <div class="dropdown dropdown-inline">
            <!-- Action buttons -->
        </div>
        <a href="javascript:;" class="btn btn-sm btn-clean btn-icon mr-2" title="Edit details">
            <!-- SVG for Edit -->
        </a>
        <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" title="Delete">
            <!-- SVG for Delete -->
        </a>
    </td>        
</tr>



@endif