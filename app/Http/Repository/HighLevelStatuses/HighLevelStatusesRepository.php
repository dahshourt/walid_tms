<?php

namespace App\Http\Repository\HighLevelStatuses;
use App\Contracts\divisionManager\DivisionManagerRepositoryInterface;
use App\Contracts\HighLevelStatuses\HighLevelStatusesRepositoryInterface;
use App\Models\DivisionManagers;
// declare Entities
use App\Models\Status;
use App\Models\GroupStatuses;
use App\Models\HighLevelStatuses;

class HighLevelStatusesRepository implements HighLevelStatusesRepositoryInterface
{

    
    public function getAll()
    {
        
        return HighLevelStatuses::all();
    }
    public function paginateAll()
    {
        return HighLevelStatuses::latest()->paginate(10);
    }
    public function create($request)
    {
       
        $HighLevelStatuses = HighLevelStatuses::create($request);
 
     
     
        Status::whereIn('id',$request['status_id'])->update(['high_level_status_id'=>$HighLevelStatuses->id]);
       
        return $HighLevelStatuses;



        // $highLevelStatuses=new HighLevelStatuses();
        // $highLevelStatuses->name=$request->name;
        // if($request->active){
        //     $highLevelStatuses->active=1;
        // }
        // $highLevelStatuses->save();
        // $highLevelStatuses->id;
        // return $highLevelStatuses;

       
        
    }

    

    public function delete($id)
    {
        return Status::destroy($id);
    }

    public function update($request, $id)
    {
       // \DB::enableQueryLog(); 
   //die;
   
			$HighLevelStatuses =  HighLevelStatuses::where('id', $id)->update(collect($request)->except(['_method','status_id'])->toArray());
            
        
            Status::where('high_level_status_id', $id)->update(['high_level_status_id' => null]);


           Status::whereIn('id', $request['status_id'])->update(['high_level_status_id' => $id]);



        return $HighLevelStatuses;
    }
	
	public function update1($request, $id)
    {
		
			$status =  HighLevelStatuses::where('id', $id)->update($request);

	
        
        return $status;
    }

    public function find($id)
    {
        return HighLevelStatuses::find($id);
    }
	public function updateactive($active,$id){
		if($active){
			
		return 	$this->update1(['active'=>'0'],$id);
		} else{
			
					return 	$this->update1(['active'=>'1'],$id);

		}
		
	}



}