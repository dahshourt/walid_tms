<?php

namespace App\Http\Repository\Logs;
use App\Contracts\Logs\LogRepositoryInterface;

// declare Entities
use App\Models\Log;



class LogRepository implements LogRepositoryInterface
{


    public function getAll()
    {
        return Log::all();
    }

    public function create($request)
    {
        return Log::create($request);
    }

    public function delete($id)
    {
        return Log::destroy($id);
    }

    public function update($request, $id)
    {
        return Log::where('id', $id)->update($request);
    }

    public function find($id)
    {
        return Log::find($id);
    }

    public function get_by_cr_id($id)
    {
        return Log::where('cr_id', $id)->get();
    }


public function updateactive($active,$id){
		if($active){
		return 	$this->update(['active'=>'0'],$id);
		} else{

					return 	$this->update(['active'=>'1'],$id);

		}

	}

}
