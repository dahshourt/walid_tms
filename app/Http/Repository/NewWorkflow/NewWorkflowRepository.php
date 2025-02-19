<?php

namespace App\Http\Repository\NewWorkFlow;
use App\Contracts\NewWorkflow\NewWorkflowRepositoryInterface;

// declare Entities
use App\Models\NewWorkFlow;
use App\Models\NewWorkFlowStatuses;
use App\Models\Status;
use DB;

class NewWorkflowRepository implements NewWorkflowRepositoryInterface
{

    public function paginateAll()
    { 
       /* return  NewWorkFlow::with([
            'from_status',                // To get the status from which the workflow starts
            'workflowstatus.to_status'    // To get the statuses to which the workflow transitions
        ])->get();*/
        return NewWorkFlow::with([
            'from_status', 
            'workflowstatus.to_status',
        ])->get();

    }

    public function ListAllWorkflowWithoutRelease()
    { 
       /* return  NewWorkFlow::with([
            'from_status',                // To get the status from which the workflow starts
            'workflowstatus.to_status'    // To get the statuses to which the workflow transitions
        ])->get();*/
        return NewWorkFlow::where('type_id','!=',7)->with([
            'from_status', 
            'workflowstatus.to_status',
        ])->get();

    }

    public function StoreWorkFlowStatuses($workflow_id,$request)
    {

     
        NewWorkFlowStatuses::where('new_workflow_id',$workflow_id)->delete();
        if(is_array($request['to_status_id']))
        {
            NewWorkFlowStatuses::where('new_workflow_id',$workflow_id)->delete();
            foreach($request['to_status_id'] as $value)
            {
                $new_workflow_statuses = new NewWorkFlowStatuses;
                $new_workflow_statuses->new_workflow_id = $workflow_id;
                $new_workflow_statuses->to_status_id = $value;
                if(isset($request['default_status']) && $request['default_status'] == 1){
                       
                    $new_workflow_statuses->default_to_status = '1';
                }else{
                     
                    $new_workflow_statuses->default_to_status = '0';
                }
                
                $new_workflow_statuses->save();
            }
        }
        else
        {
            $new_workflow_statuses = new NewWorkFlowStatuses;
            $new_workflow_statuses->new_workflow_id = $workflow_id;
            $new_workflow_statuses->to_status_id = $request['to_status_id'];
                if(isset($request['default_status']) && $request['default_status'] == 1){
                     
                    $new_workflow_statuses->default_to_status = '1';
                }else{
                    
                    $new_workflow_statuses->default_to_status = '0';
                }
            $new_workflow_statuses->save();
        }
        return true;
    }
    
    public function getAll()
    {
        return NewWorkFlow::all();
    }

    public function create($request)
    {  
       if($request['type_id']){
        $request['workflow_type'] = $request['type_id'];
       }
        $request['workflow_type'] = $request['workflow_type'] == 1 ? '1' : '0';
        //dd($request['to_status_lable']);
        $request['to_status_label'] = $request['to_status_lable'];
        $workflow = NewWorkFlow::create($request);
        $this->StoreWorkFlowStatuses($workflow->id,$request);
    }
   

    public function delete($id)
    {
        return NewWorkFlow::destroy($id);
    }

    public function update($request, $id)
    {
        $defualt = 0;
        //dd($request['default_status']);
        if(isset($request['default_status']))
        {
            $defualt = $request['default_status'];
        }
        $except=['to_status_id','_method','default_to_status','_token','to_status_lable','default_status'];
        $request['workflow_type'] = $request['workflow_type'] == 1 ? '1' : '0';
        $request['same_time'] = $request['same_time'] == 1 ? '1' : '0';
        $request['to_status_label'] = $request['to_status_lable'];
        $new_workflow = NewWorkFlow::where('id', $id)->update($request->except($except));
        $request['default_status'] = $defualt;
        
        
        $this->StoreWorkFlowStatuses($id,$request);
        return $new_workflow;
    }

    public function find($id)
    {
        return NewWorkFlow::with('from_status','workflowstatus','workflowstatus.to_status')->find($id);
    }

    public function listFromStatuses($id)
    {
        return NewWorkFlow::select('from_status_id')->with('from_status')->where('to_status_id',$id)->get();
    }

    public function getFirstCreationStatus($type_id)
    {
        $intial_status = NewWorkFlow::select('from_status_id')->whereHas('workflowstatus', function($q){
            $q->whereColumn('to_status_id', 'new_workflow.from_status_id');
        })->where('type_id',$type_id)->first();

        return $intial_status;
    }
    public function update1($request, $id)
    {
		
			$status =  NewWorkFlow::where('id', $id)->update($request);

	
        
        return $status;
    }
    public function updateactive($active,$id){
		if($active){
			
		return 	$this->update1(['active'=>'0'],$id);
		} else{
			
			return 	$this->update1(['active'=>'1'],$id);
		}
		
	}

    /*public function get_to_status_by_fromstaus()
    {
      return  NewWorkFlow::where('type_id', 7)
                ->whereHas('workflowStatuses', function ($query) {
                    $query->whereColumn('new_workflow.from_status_id', '=', 'new_workflow_statuses.to_status_id');
                })
                ->with('workflowStatuses')
                ->get();
    }*/

    public function get_next_status_release($id)
    {
        return NewWorkFlow::with("workflowstatus.to_status")->where("from_status_id", $id)->first();
    }
    

}