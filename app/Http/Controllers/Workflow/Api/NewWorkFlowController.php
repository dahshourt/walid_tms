<?php

namespace App\Http\Controllers\Workflow\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Workflow\Api\NewWorkflowRequest;
use App\Factories\NewWorkFlow\NewWorkFlowFactory;

use App\Http\Resources\WorkFlowResource;

class NewWorkFlowController extends Controller
{
    use ValidatesRequests;
    private $Workflow;

    function __construct(NewWorkFlowFactory $Workflow){
        
        $this->Workflow = $Workflow::index();
        
    }

    public function index()
    {
        $Workflows = $this->Workflow->getAll();
        $Workflows = WorkFlowResource::collection($Workflows);
        return response()->json(['data' => $Workflows],200);
    }
    /**
     * Send or resend the verification code.
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(NewWorkflowRequest $request)
    {
      
        $this->Workflow->create($request->all());

        return response()->json([
            'message' => 'Created Successfully',
        ]);
    }

    /**
     * Send or resend the verification code.
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(NewWorkflowRequest $request, $id)
    {
        $Workflow = $this->Workflow->find($id);
        if(!$Workflow)
        {
            return response()->json([
                'message' => 'Workflow Not Exists',
            ],422);
        }
        $this->Workflow->update($request,$id);
        return response()->json([
            'message' => 'Updated Successfully',
        ]);
    }

    public function show($id)
    {
        $Workflow = $this->Workflow->find($id);
        if(!$Workflow)
        {
            return response()->json([
                'message' => 'Workflow Not Exists',
            ],422);
        }
        $Workflow = new WorkFlowResource($Workflow);
        return response()->json(['data' => $Workflow],200);
    }

    public function WorkflowStatuses($id)
    {
        $Workflow = $this->Workflow->find($id);
        if(!$Workflow)
        {
            return response()->json([
                'message' => 'Workflow Not Exists',
            ],422);
        }
        return response()->json(['data' => $Workflow->statuses],200);
    }

    public function destroy()
    {
        
    }


    public function listFromStatuses($id)
    {
        $Workflow = $this->Workflow->listFromStatuses($id);
        if(!$Workflow)
        {
            return response()->json([
                'message' => 'Workflow Not Exists',
            ],422);
        }
        return response()->json(['data' => $Workflow],200);
    }

}
