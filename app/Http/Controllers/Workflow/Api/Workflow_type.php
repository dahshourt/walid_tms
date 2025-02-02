<?php

namespace App\Http\Controllers\Workflow\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
//use App\Http\Requests\Workflow\Api\DependWorkFlowRequest;
use App\Factories\Workflow\Workflow_type_factory;
class Workflow_type extends Controller
{
    use ValidatesRequests;
    private $workflow_type;

    function __construct(Workflow_type_factory $workflow_type_factory){
        
        $this->workflow_type = $workflow_type_factory::index();
        
    }

    public function index()
    {
       $get_workflow_type =  $this->workflow_type->get_workflow_type();
       return response()->json(['data' => $get_workflow_type],200);
    }

    public function subtype($id)
    {
       
       $get_workflow_subtype =  $this->workflow_type->get_workflow_subtype($id);
       return response()->json(['data' => $get_workflow_subtype],200);
    }
    
    public function Allsubtype()
    {
       $get_workflow_subtype =  $this->workflow_type->get_workflow_all_subtype();
       return response()->json(['data' => $get_workflow_subtype],200);
    }
  

}
