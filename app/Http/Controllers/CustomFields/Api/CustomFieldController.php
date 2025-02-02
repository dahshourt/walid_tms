<?php

namespace App\Http\Controllers\CustomFields\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\CustomFields\Api\CustomFieldRequest;
use App\Factories\CustomField\CustomFieldFactory;

class CustomFieldController extends Controller
{
    use ValidatesRequests;
    private $CustomField;

    function __construct(CustomFieldFactory $CustomField){
        
        $this->CustomField = $CustomField::index();
        
    }

    public function index()
    {
        $CustomFields = $this->CustomField->getAll();
        return response()->json(['data' => $CustomFields],200);
    }


    public function store(CustomFieldRequest $request)
    {
        $this->CustomField->create($request->all());

        return response()->json([
            'message' => 'Created Successfully',
        ]);
    }


    public function update(CustomFieldRequest $request,$id)
    {
        $CustomField = $this->CustomField->find($id);
        if(!$CustomField)
        {
            return response()->json([
                'message' => 'Group Not Exists',
            ],422);
        }
        $this->CustomField->update($request,$id);
        return response()->json([
            'message' => 'Updated Successfully',
        ]);
    }


    public function show($id)
    {
        $CustomField = $this->CustomField->find($id);
        return response()->json(['data' => $CustomField],200);
    }
    
    

}
