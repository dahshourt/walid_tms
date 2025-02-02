<?php

namespace App\Http\Repository\Releases;
use App\Contracts\Releases\ReleasesRepositoryInterface;

// declare Entities
use App\Models\Release;
use App\Models\Vendor;
use App\Models\WorkFlowType;
use App\Models\NewWorkFlow;
use App\Models\Release_statuse;
use App\Models\ReleaseLogs;
use App\Models\Status;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;
use App\Http\Repository\NewWorkflow\NewWorkflowRepository;
use Auth;

class ReleaseRepository implements ReleasesRepositoryInterface
{

    public function getAll()
    {

    }
   
    public function find($id){
        return Release::find($id);
    }
     
    public function create($request)
    {        

        $releaseWorkflowTypeId = WorkFlowType::where('name', 'Realeas')->value('id');

        
        $workflow = NewWorkFlow::where('type_id', $releaseWorkflowTypeId)->first();
        //dd($workflow);
        return Release::create([
            'name' => $request['name'],
            'release_status' =>$workflow->from_status_id,
            'go_live_planned_date' => $request['go_live_planned_date'],
            'planned_start_iot_date' => $request['planned_start_iot_date'],
            'planned_end_iot_date' => $request['planned_end_iot_date'],
            'planned_start_e2e_date' => $request['planned_start_e2e_date'],
            'planned_end_e2e_date' => $request['planned_end_e2e_date'],
            'planned_start_uat_date' => $request['planned_start_uat_date'],
            'planned_end_uat_date' => $request['planned_end_uat_date'],
            'planned_start_smoke_test_date' => $request['planned_start_smoke_test_date'],
            'planned_end_smoke_test_date' => $request['planned_end_smoke_test_date'],
        ]);

    }

    public function list()
    {
        return Release::with(['vendors','status'])->get();
    }
    public function paginateAll($search)
{
    $group = session('default_group') ?? auth()->user()->default_group;

    $release = Release::whereHas('status.group_statuses', function ($query) use ($group) {
                    $query->where('group_id', $group);
                })
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%") // Search in the `Release` table
                          ->orWhereHas('status', function ($query) use ($search) {
                              $query->where('status_name', 'like', "%{$search}%"); // Search in `releaseStatus.name`
                          });
                })
                ->with(['status.group_statuses', 'status']) // Eager load related data
                ->latest()
                ->paginate(10);
                // $sql = $release->toSql();
                // $bindings = $release->getBindings();
                
                // // Log or dump the query
                // dd(vsprintf(str_replace('?', "'%s'", $sql), $bindings));
    return $release;
}

    

    

    public function show($id)
    {
        return Release::where('id', $id)->first();
    }

    public function update($request , $id)
    {        
        //dd($request);
        $old_release = $this->find($id);
        if(isset($request['status']))
        {
            $this->update_crs_of_release($id,$request['status']);
            $this->UpdateReleaseActualDates($id,$request['status'],$old_release->release_status);
        } 
        
        
        Release::where('id' , $id)->update([
            'name' => $request['name'],
            'release_status' => isset($request['status']) ? $request['status'] : $old_release->release_status ,
            'go_live_planned_date' => $request['go_live_planned_date'],
            'planned_start_iot_date' => $request['planned_start_iot_date'],
            'planned_end_iot_date' => $request['planned_end_iot_date'],
            'planned_start_e2e_date' => $request['planned_start_e2e_date'],
            'planned_end_e2e_date' => $request['planned_end_e2e_date'],
            'planned_start_uat_date' => $request['planned_start_uat_date'],
            'planned_end_uat_date' => $request['planned_end_uat_date'],
            'planned_start_smoke_test_date' => $request['planned_start_smoke_test_date'],
            'planned_end_smoke_test_date' => $request['planned_end_smoke_test_date'],
        ]);


        $this->StoreLog($id,$old_release,$request);


        return true;
        
    }


    public function get_iot_releass(){
        $start_release_workflow  = new NewWorkflowRepository();
        $start_release_workflow  = $start_release_workflow->getFirstCreationStatus(7);
        //dd($start_release_workflow->from_status_id);
        //dd($start_release_workflow[0]->from_status_id,$start_release_workflow[0]->workflowStatuses[0]->to_status_id);
        $today = date('Y-m-d');
        return $releases = Release::whereDate('planned_start_iot_date', $today)
                           ->where('release_status', $start_release_workflow->from_status_id)
                           ->get();
    }

    public function update_release_status($release_id)
    {
        $release = Release::where("id", $release_id)->select("release_status")->first();

        $new_workflow  = new NewWorkflowRepository();
        $new_workflow  = $new_workflow->get_next_status_release($release->release_status);
          Release::where('id', $release_id)->update(['release_status' => $new_workflow->workflowstatus[0]->to_status_id]);
           return $new_workflow->workflowstatus[0]->new_workflow_id;

    }


    public function update_crs_of_release($release_id, $next_status)
    {
      
        $change_request = new ChangeRequestRepository();
        $crs_matched_entred_release = $change_request->get_change_request_by_release($release_id);
        //dd($crs_matched_entred_release[0]->CurrentRequestStatuses->new_status_id);
       // dd($crs_matched_entred_release[1]->CurrentRequestStatuses->new_status_id);
         

         foreach ($crs_matched_entred_release as $key => $value) {
            $request =  [
            'new_status_id' => "$next_status", 
            'old_status_id' => $crs_matched_entred_release[$key]->CurrentRequestStatuses->new_status_id,
            'assign_to' =>  1,
        ];
        $requestCollection = collect($request);
        //dd($requestCollection);
            //echo $value->id . "<br />";
            $updated = $change_request->UpateChangeRequestReleaseStatus($value->id, $requestCollection);
        }
        //dd(' crs');
    }

    public function update_release_its_crs(){
        $all_releases =   $this->get_iot_releass();
        foreach ($all_releases as $key => $value) {
           $next_status =   $this->update_release_status($value->id);
              $this->update_crs_of_release($value->id, $next_status);
        }
        
    }


    public function StoreLog($id , $old_data , $request)
    {
        $user_name = "admin";
        if(\Auth::user())
        {
            $user_name = \Auth::user()->user_name;
        }
        if(isset($request['status']) && ($old_data->release_status !=  $request['status']) )
        {
            $old_status_name = Status::find($old_data->release_status)->status_name;
            $new_status_name = Status::find($request['status'])->status_name;
            $log_text = " Status changed from $old_status_name to $new_status_name by $user_name";
            $data = [
                'release_id' => $id,
                'user_id' => \Auth::user()->id,
                'log_text' => $log_text,
                'status_id' => $request['status'],
            ];
            ReleaseLogs::create($data);
        }
        //dd($id , $request['planned_start_iot_date'],$old_data->planned_start_iot_date);
        if(isset($request['planned_start_iot_date']) && ($old_data->planned_start_iot_date !=  $request['planned_start_iot_date']) )
        {
            $planned_start_iot_date =  $request['planned_start_iot_date'];
            $log_text = " Planned start IOT date changed from $old_data->planned_start_iot_date to $planned_start_iot_date by $user_name";
            $data = [
                'release_id' => $id,
                'user_id' => \Auth::user()->id,
                'log_text' => $log_text,
            ];
            ReleaseLogs::create($data);
        }

        if(isset($request['planned_end_iot_date']) && ($old_data->planned_end_iot_date !=  $request['planned_end_iot_date']) )
        {
            $planned_end_iot_date =  $request['planned_end_iot_date'];
            $log_text = " Planned end IOT date changed from $old_data->planned_end_iot_date to $planned_end_iot_date by $user_name";
            $data = [
                'release_id' => $id,
                'user_id' => \Auth::user()->id,
                'log_text' => $log_text,
            ];
            ReleaseLogs::create($data);
        }

        if(isset($request['planned_start_e2e_date']) && ($old_data->planned_start_e2e_date !=  $request['planned_start_e2e_date']) )
        {
            $planned_start_e2e_date =  $request['planned_start_e2e_date'];
            $log_text = " Planned start E2E date changed from $old_data->planned_start_e2e_date to $planned_start_e2e_date by $user_name";
            $data = [
                'release_id' => $id,
                'user_id' => \Auth::user()->id,
                'log_text' => $log_text,
            ];
            ReleaseLogs::create($data);
        }

        if(isset($request['planned_end_e2e_date']) && ($old_data->planned_end_e2e_date !=  $request['planned_end_e2e_date']) )
        {
            $planned_end_e2e_date =  $request['planned_end_e2e_date'];
            $log_text = " Planned end E2E date changed from $old_data->planned_end_e2e_date to $planned_end_e2e_date by $user_name";
            $data = [
                'release_id' => $id,
                'user_id' => \Auth::user()->id,
                'log_text' => $log_text,
            ];
            ReleaseLogs::create($data);
        }


        if(isset($request['planned_start_uat_date']) && ($old_data->planned_start_uat_date !=  $request['planned_start_uat_date']) )
        {
            $planned_start_uat_date =  $request['planned_start_uat_date'];
            $log_text = " Planned start UAT date changed from $old_data->planned_start_uat_date to $planned_start_uat_date by $user_name";
            $data = [
                'release_id' => $id,
                'user_id' => \Auth::user()->id,
                'log_text' => $log_text,
            ];
            ReleaseLogs::create($data);
        }

        

        if(isset($request['planned_end_uat_date']) && ($old_data->planned_end_uat_date !=  $request['planned_end_uat_date']) )
        {
            $planned_end_uat_date =  $request['planned_end_uat_date'];
            $log_text = " Planned end UAT date changed from $old_data->planned_end_uat_date to $planned_end_uat_date by $user_name";
            $data = [
                'release_id' => $id,
                'user_id' => \Auth::user()->id,
                'log_text' => $log_text,
            ];
            ReleaseLogs::create($data);
        }

        

        if(isset($request['planned_start_smoke_test_date']) && ($old_data->planned_start_smoke_test_date !=  $request['planned_start_smoke_test_date']) )
        {
            $planned_start_smoke_test_date =  $request['planned_start_smoke_test_date'];
            $log_text = " Planned start smoke test date changed from $old_data->planned_start_smoke_test_date to $planned_start_smoke_test_date by $user_name";
            $data = [
                'release_id' => $id,
                'user_id' => \Auth::user()->id,
                'log_text' => $log_text,
            ];
            ReleaseLogs::create($data);
        }


        

        if(isset($request['planned_end_smoke_test_date']) && ($old_data->planned_end_smoke_test_date !=  $request['planned_end_smoke_test_date']) )
        {
            $planned_end_smoke_test_date =  $request['planned_end_smoke_test_date'];
            $log_text = " Planned end smoke test date changed from $old_data->planned_end_smoke_test_date to $planned_end_smoke_test_date by $user_name";
            $data = [
                'release_id' => $id,
                'user_id' => \Auth::user()->id,
                'log_text' => $log_text,
            ];
            ReleaseLogs::create($data);
        }

        

        if(isset($request['go_live_planned_date']) && ($old_data->go_live_planned_date !=  $request['go_live_planned_date']) )
        {
            $go_live_planned_date =  $request['go_live_planned_date'];
            $log_text = " Planned go live date changed from $old_data->go_live_planned_date to $go_live_planned_date by $user_name";
            $data = [
                'release_id' => $id,
                'user_id' => \Auth::user()->id,
                'log_text' => $log_text,
            ];
            ReleaseLogs::create($data);
        }



        return true;
    }


    public function DisplayLogs($release_id)
    {
        $logs=ReleaseLogs::where("release_id",$release_id)->get();
        return $logs;
    }


    public function UpdateReleaseActualDates($id,$new_status,$old_status)
    {
        $current_date =date('Y-m-d');
        $old_status_name = Status::find($old_status)->status_name;
        $new_status_name = Status::find($new_status)->status_name;

        if(str_contains($old_status_name, 'Release Plan') && str_contains($new_status_name, 'IOT'))
        {
            Release::where('id', $id)->update(['actual_start_iot_date' => $current_date]);
        }

        if(str_contains($old_status_name, 'IOT') && str_contains($new_status_name, 'UAT'))
        {
            Release::where('id', $id)->update(['actual_end_iot_date' => $current_date]);
            Release::where('id', $id)->update(['actual_start_uat_date' => $current_date]);
        }

        if(str_contains($old_status_name, 'UAT') && str_contains($new_status_name, 'Smoke'))
        {
            Release::where('id', $id)->update(['actual_end_uat_date' => $current_date]);
            Release::where('id', $id)->update(['actual_start_smoke_test_date' => $current_date]);
        }


        if(str_contains($old_status_name, 'Smoke') && str_contains($new_status_name, 'Closure'))
        {
            Release::where('id', $id)->update(['actual_end_smoke_test_date' => $current_date]);
            Release::where('id', $id)->update(['actual_closure_date' => $current_date]);
        }


        return true;

    }
 

}