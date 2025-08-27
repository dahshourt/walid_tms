<?php

namespace App\Http\Repository\Workflow;
use App\Contracts\Workflow\workflow_type_contracts;

// declare Entities
use App\Models\WorkFlowType;

class Workflow_type_repository implements workflow_type_contracts
{

    
    public function get_workflow_type()
    {
         
        return WorkFlowType::select("id" ,"name","parent_id")->where("parent_id", NULL)->get();
    }

    public function get_workflow_subtype($id)
    {
         
        return WorkFlowType::select("id" ,"name","parent_id")->where("parent_id", $id)->get();
    }
    public function get_workflow_all_subtype()
    {
         
        return WorkFlowType::select("id" ,"name","parent_id")->WhereNotNull('parent_id')->get();
    }


    public function get_workflow_all_subtype_without_release()
    {
         
        return WorkFlowType::select("id" ,"name","parent_id")->WhereNotNull('parent_id')->where('active','1')->get();
    }
    

}