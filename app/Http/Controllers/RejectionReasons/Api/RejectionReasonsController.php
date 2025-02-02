<?php

namespace App\Http\Controllers\RejectionReasons\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\RejectionReasons\Api\RejectionReasonsRequest;
use App\Factories\RejectionReasons\RejectionReasonsFactory;

class RejectionReasonsController extends Controller
{
    use ValidatesRequests;
    private $RejectionReasons;

    function __construct(RejectionReasonsFactory $RejectionReasons){
        
        $this->RejectionReasons = $RejectionReasons::index();
        
    }

    public function index()
    {
        $systems = $this->RejectionReasons->getAll();
        return response()->json(['data' => $systems],200);
    }
    /**
     * Send or resend the verification code.
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RejectionReasonsRequest $request)
    {
        
        $this->RejectionReasons->create($request->all());

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
    public function update(RejectionReasonsRequest $request, $id)
    {
        $RejectionReasons = $this->RejectionReasons->find($id);
        if(!$RejectionReasons)
        {
            return response()->json([
                'message' => 'system Not Exists',
            ],422);
        }
        $this->RejectionReasons->update($request->except('_method'),$id);
        return response()->json([
            'message' => 'Updated Successfully',
        ]);
    }

    public function show($id)
    {
        $RejectionReasons = $this->RejectionReasons->find($id);
        if(!$RejectionReasons)
        {
            return response()->json([
                'message' => 'system Not Exists',
            ],422);
        }
        return response()->json(['data' => $RejectionReasons],200);
    }

    public function StageStatuses($id)
    {
        $RejectionReasons = $this->RejectionReasons->find($id);
        if(!$system)
        {
            return response()->json([
                'message' => 'system Not Exists',
            ],422);
        }
        return response()->json(['data' => $RejectionReasons->statuses],200);
    }

    public function destroy()
    {
        
    }
	public function updateactive($id){
		   $RejectionReasons = $this->RejectionReasons->find($id);
		   
		   $this->RejectionReasons->updateactive($RejectionReasons['active'],$id);
		   
		    return response()->json([
            'message' => 'Updated Successfully',
        ]);

		  
		
	}

}
