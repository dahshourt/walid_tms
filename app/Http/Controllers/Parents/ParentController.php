<?php

namespace App\Http\Controllers\Parents;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Parents\ParentRequest;
use App\Factories\Parents\ParentFactory;

use App\Http\Repository\Departments\DepartmentRepository;
use App\Http\Repository\Groups\GroupRepository;
use App\Http\Repository\Units\UnitRepository;
use App\Http\Repository\Roles\RolesRepository;
use App\Http\Repository\Workflow\Workflow_type_repository;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;

use Illuminate\Http\Request;


class ParentController extends Controller
{
    use ValidatesRequests;
    private $parent;

    function __construct(ParentFactory $parent){
        
        $this->parent = $parent::index();
        $this->view = 'parents';
        $view = 'parents';
        $route = 'parents';
        $OtherRoute = 'parent';
        
        $title = 'Parents';
        $form_title = 'Parent';
        view()->share(compact('view','route','title','form_title','OtherRoute'));
        
    }

    public function index()
    {

        $this->authorize('List Parents'); // permission check
        
        $collection = $this->parent->paginateAll();
        
        return view("$this->view.index",compact('collection'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        $this->authorize('Create Parent'); // permission check
        $workflow=new Workflow_type_repository();
		$workflow_subtype =   $workflow->get_workflow_all_subtype();

        return view("$this->view.create",compact('workflow_subtype'));
    }

    /**
     * Send or resend the verification code.
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ParentRequest $request)
    {
         $this->authorize('Create Parent'); // permission check
        $this->parent->create($request->all());

        return redirect()->back()->with('status' , 'Added Successfully' );
    }


    public function edit($id)
    {
        $this->authorize('Edit Parent'); // permission check
        $row = $this->parent->find($id);
		$workflow=new Workflow_type_repository();
		$workflow_subtype =   $workflow->get_workflow_all_subtype();
		
		$crs=new ChangeRequestRepository();
		$crs =   $crs->ListCRsByWorkflowType($row->change_request->workflow_type_id);
        return view("$this->view.edit",compact('row','workflow_subtype','crs'));

    }

    /**
     * Send or resend the verification code.
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ParentRequest $request, $id)
    {
         $this->authorize('Edit Parent'); // permission check
       
 $this->parent->update($request->except(['_token', '_method']),$id);
// and then you can get query log


       
        return redirect()->back()->with('status' , 'Updated Successfully' );
    }

    public function show($id)
    {
        $this->authorize('Show Parent'); // permission check
        $parent = $this->parent->find($id);
        if(!$parent)
        {
            return response()->json([
                'message' => 'Parent Not Exists',
            ],422);
        }
        $parent = new ParentsResource($parent);
        return response()->json(['data' => $parent],200);
    }

    public function destroy()
    {
        $this->authorize('Delete Parent'); // permission check
        
    }
	public function updateactive(Request $request){

        $this->authorize('Active Parent'); // permission check
		   
        $data = $this->parent->find($request->id);
		
		$this->parent->updateactive($data->active,$request->id);
		   
		    return response()->json([
            'message' => 'Updated Successfully',
            'status' => 'success'
        ]);

		  
		
	}
	
	
	public function ListCRsbyWorkflowtype(Request $request)
	{
		$crs=new ChangeRequestRepository();
		$crs =   $crs->ListCRsByWorkflowType($request->workflowtype);
		return view("$this->view.list_crs",compact('crs'));
	}


   

}
