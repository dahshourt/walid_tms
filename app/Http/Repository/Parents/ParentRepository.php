<?php

namespace App\Http\Repository\Parents;
use App\Contracts\Parents\ParentRepositoryInterface;

// declare Entities
use App\Models\Parents_crs;
use App\Models\Change_request;
use App\Models\Application;

class ParentRepository implements ParentRepositoryInterface
{
    public function paginateAll()
    {
        return Parents_crs::latest()->paginate(10);
    }
     public function get_parent_subtype($id)
    {
         
        
        $application= Parents_crs::select("application_name")->where("id", $id)->first();
        
        return  Application::select('name','id')->where('name',$application->application_name)->get();
    }
    public function getAll()
    {
        return Parents_crs::all();
    }
    public function parent_systems($system){
        $application= Parents_crs::select("application_name")->where("name", $system)->first();
        
        return  Application::select('name','id')->where('name',$application->application_name)->get();
  
    
    }
    public function create($request)
    {
       
      $change_request= Change_request::find($request['name']);
     

      
      $application=Application::find($change_request->application_id);

      $data['application_name']=$application->name;
      
      
      $data['name']=$request['name'];
      $data['active']="1";
        
        return Parents_crs::create($data);
    }

    public function delete($id)
    {
        return Parents_crs::destroy($id);
    }

    public function update($request, $id)
    {
        return Parents_crs::where('id', $id)->update($request);
    }

    public function find($id)
    {
        return Parents_crs::find($id);
    }
public function updateactive($active,$id){
		if($active){
		return 	$this->update(['active'=>'0'],$id);
		} else{
			
					return 	$this->update(['active'=>'1'],$id);

		}
		
	}

}