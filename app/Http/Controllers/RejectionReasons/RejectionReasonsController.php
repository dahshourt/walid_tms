<?php

namespace App\Http\Controllers\RejectionReasons;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\RejectionReasons\RejectionReasonsRequest;
use App\Factories\RejectionReasons\RejectionReasonsFactory;
use Illuminate\Http\Request;

class RejectionReasonsController extends Controller
{
    use ValidatesRequests;
    private $RejectionReasons;

    function __construct(RejectionReasonsFactory $RejectionReasons){
        
        $this->rejectionReason = $RejectionReasons::index();
        $this->view = 'rejection_reasons';
        $view = 'rejection_reasons';
        $route = 'rejection_reasons';
        $OtherRoute = 'rejection_reasons';//rejection_reasons
        
        $title = 'Rejection reasons';
        $form_title = 'Rejection reason';
        view()->share(compact('view','route','title','form_title','OtherRoute'));
        
    }

    public function index()
    {
        $this->authorize('List RejectionReasons'); // permission check
        $collection = $this->rejectionReason->paginateAll();
        return view("$this->view.index",compact('collection'));
    }
    public function create()
    {
        //
        $this->authorize('Create RejectionReason'); // permission check
        

        return view("$this->view.create");
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
        die("sss");
        
        $this->rejectionReason->create($request->all());
        return redirect()->route("$this->view.index")->with('message' , 'Created Successfully' );
        
    }
    public function edit($id)
    {

        $this->authorize('Edit RejectionReason'); // permission check
        $row = $this->rejectionReason->find($id);
       
        return view("$this->view.edit",compact('row'));

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
        $this->rejectionReason->update($request->except(['_token', '_method']),$id);
            return redirect()->back()->with('status' , 'Updated Successfully' );
       // return redirect()->route('rejection_reason.index')->with('status' , 'Updated Successfully' );
        
    }

    public function show($id)
    {

        $this->authorize('Show RejectionReason'); // permission check
        $RejectionReasons = $this->rejectionReason->find($id);
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
        $RejectionReasons = $this->rejectionReason->find($id);
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
        $this->authorize('Delete RejectionReason'); // permission check
        
    }

    public function updateactive(Request $request)
    {

        $this->authorize('Active RejectionReason'); // permission check
       $id= $request->id;
    
        $RejectionReasons =$this->rejectionReason->find($id);
		  
        $this->rejectionReason->updateactive($RejectionReasons['active'],$id);
        
		   
        return response()->json([
            'message' => 'Updated Successfully',
            'status' => 'success'
        ]);

	}

}
