<?php

namespace App\Http\Repository\Applications;
use App\Contracts\Applications\ApplicationRepositoryInterface;

// declare Entities
use App\Models\Application;
use Illuminate\Support\Facades\DB;



class ApplicationRepository implements ApplicationRepositoryInterface
{

    
    public function getAll()
    {
        return Application::all();
    }
    public function paginateAll()
    {
        return Application::latest()->paginate(10);
    }
    public function create($request)
    {
        return Application::create($request);
    }

    public function delete($id)
    {
        return Application::destroy($id);
    }

    public function update($request, $id)
    {
        return Application::where('id', $id)->update($request);
    }

    public function find($id)
    {
        return Application::find($id);
    }

    public function workflowType($id)
    { 
        $application = Application::find($id);
        return $application->workflow_type;
    }
public function updateactive($active,$id){
		if($active){
		return 	$this->update(['active'=>'0'],$id);
		} else{
			
					return 	$this->update(['active'=>'1'],$id);

		}
		
	}

    public function application_based_on_workflow($workflowTypeId)
    {
         return Application::where('workflow_type_id', $workflowTypeId)->get();
    }

    public function get_crs_group_bu_applications($applications_req, $workflow_type_req)
    {
        

        if(empty($applications_req))
        {
            $all_apps = $this->getAll();
             foreach($all_apps as $item)
             {
                $applications_req[] = "$item->id";
             }
            $new_app = trim(json_encode($applications_req), '[]');  
        }else
        {
            $new_app = trim(json_encode($applications_req), '[]');    
        }
        

         
               return  DB::select
             ("
                SELECT 
                    COUNT(cr.id) 'CRs_Count',
                      (select `name` from applications where applications.id = cr.application_id) `application_name`
                    
                FROM
                    change_request AS cr
                    where  cr.workflow_type_id = \"$workflow_type_req\"
                     AND cr.application_id IN($new_app)
                GROUP BY cr.application_id;
             "); 
        
         
    }

}