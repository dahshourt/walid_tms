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
        

        return view("$this->view.create");
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
        $this->parent->create($request->all());

        return redirect()->back()->with('status' , 'Added Successfully' );
    }


    public function edit($id)
    {
        $this->authorize('Edit Parent'); // permission check
        $row = $this->parent->find($id);
       
        return view("$this->view.edit",compact('row'));

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


   

}
