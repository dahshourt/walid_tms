<?php

namespace App\Http\Repository\CustomField;
use App\Contracts\CustomField\CustomFieldRepositoryInterface;
 
// declare Entities
use App\Models\CustomField;

class CustomFieldRepository implements CustomFieldRepositoryInterface
{
    
    public function getAll()
    {
        return CustomField::all();
    }

    public function create($request)
    {
        return CustomField::create($request);
    }

    public function delete($id)
    {
        return CustomField::destroy($id);
    }

    public function update($request, $id)
    {
        return CustomField::where('id', $id)->update($request);
    }

    public function find($id)
    {
        return CustomField::find($id);
    }
    
		
    
}