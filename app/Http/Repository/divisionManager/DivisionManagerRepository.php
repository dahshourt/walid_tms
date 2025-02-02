<?php

namespace App\Http\Repository\divisionManager;
use App\Contracts\divisionManager\DivisionManagerRepositoryInterface;
use App\Models\DivisionManagers;
// declare Entities
use App\Models\Status;
use App\Models\GroupStatuses;



class DivisionManagerRepository implements DivisionManagerRepositoryInterface
{

    
    public function getAll()
    {
        return DivisionManagers::latest()->paginate(10);
    }

    public function create($request)
    {
        
        $status = DivisionManagers::create($request);
       
        return $status;
    }

    

    public function delete($id)
    {
        return Status::destroy($id);
    }

    public function update($request, $id)
    {
		
			$status =  DivisionManagers::where('id', $id)->update($request);

	
       
        return $status;
    }
	
	public function update1($request, $id)
    {
		
			$status =  DivisionManagers::where('id', $id)->update($request);

	
        
        return $status;
    }

    public function find($id)
    {
        return DivisionManagers::find($id);
    }
	public function updateactive($active,$id){
		if($active){
			
		return 	$this->update1(['active'=>'0'],$id);
		} else{
			
					return 	$this->update1(['active'=>'1'],$id);

		}
		
	}



}