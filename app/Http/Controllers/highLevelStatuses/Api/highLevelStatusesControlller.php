<?php

namespace App\Http\Controllers\highLevelStatuses\Api;
use Illuminate\Foundation\Http\FormRequest;
use App\Factories\HighLevelStatuses\HighLevelStatusesFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\HighLevelStatuses\highlevelrequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Statuses\Api\StatusRequest;
use App\Http\Resources\HighLevelStatusResource;
use App\Http\Resources\StatusResource;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Request as HttpRequest;

class highLevelStatusesControlller extends Controller
{
    use ValidatesRequests;
    private $highLevelStatuses;

    function __construct(HighLevelStatusesFactory $highLevelStatuses){
        
        $this->highLevelStatuses = $highLevelStatuses::index();
        
    }

    public function index()
    {
      
        //  = $this->status->getAll();
        $highLevelStatuses = HighLevelStatusResource::collection($this->highLevelStatuses->getAll());
        return response()->json(['data' => $highLevelStatuses],200);
    }
    /**
     * Send or resend the verification code.
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(highlevelrequest $request)
    {
       
        $this->highLevelStatuses->create($request->all());

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
    public function update(highlevelrequest $request, $id)
    {
		
        $highLevelStatuses = $this->highLevelStatuses->find($id);
        if(!$highLevelStatuses)
        {
            return response()->json([
                'message' => 'status Not Exists',
            ],422);
        }
        $this->highLevelStatuses->update($request,$id);
        return response()->json([
            'message' => 'Updated Successfully',
        ]);
    }

    public function show($id)
    {
        $highLevelStatuses = $this->highLevelStatuses->find($id);
        if(!$highLevelStatuses)
        {
            return response()->json([
                'message' => 'status Not Exists',
            ],422);
        }
        $highLevelStatuses = new HighLevelStatusResource($highLevelStatuses);
        return response()->json(['data' => $highLevelStatuses],200);
    }

    public function destroy()
    {
        
    }
	public function updateactive($id){
		   $highLevelStatuses = $this->highLevelStatuses->find($id);
		   
		   $this->highLevelStatuses->updateactive($highLevelStatuses['active'],$id);
		   
		    return response()->json([
            'message' => 'Updated Successfully',
        ]);

		  
		
	}

}