<?php

namespace App\Http\Controllers\Groups;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Groups\GroupRequest;
use App\Factories\Groups\GroupFactory;
use Illuminate\Http\Request;
use App\Http\Repository\Applications\ApplicationRepository;
class GroupController extends Controller
{
    use ValidatesRequests;
    private $group;

    function __construct(GroupFactory $group){        
        $this->group = $group::index();
        $this->view = 'group';
        $OtherRoute = 'groups';
        $view = 'group';
        $route = 'groups';
        $title = 'Groups List';
        $form_title = 'Groups';
        view()->share(compact('view','route','title','form_title','OtherRoute'));
    }

     
    public function index()
    {
        $this->authorize('List Groups'); // permission check
        $collection = $this->group->getAll();
       
        return view("$this->view.index",compact('collection'));
    }
    public function edit($id)
    {
        $this->authorize('Edit Group'); // permission check
        $row = $this->group->find($id);
        $parent_id = null ;
        $parent_groups = $this->group->getAllWithFilter($parent_id);
        $app=new ApplicationRepository();
        $applications=$app->getAll();
        return view("$this->view.edit",compact('parent_groups','row','applications'));
    }
    
    public function create()
    {
        $this->authorize('Create Group'); // permission check
        $parent_id = null ;
        $parent_groups = $this->group->getAllWithFilter($parent_id);
        $app=new ApplicationRepository();
        $applications=$app->getAll();
        return view("$this->view.create",compact('parent_groups','applications'));
    }

    public function store(GroupRequest $request)
    {
        $this->group->create($request->all());
        return redirect()->back()->with('status' , 'Added Successfully' );
    }

    /**
     * Send or resend the verification code.
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(GroupRequest $request,$id)
    {
        // echo "<pre>";
        // print_r($request->all());
        // echo "</pre>"; die;
        $group = $this->group->find($id);
        if(!$group)
        {
            
            return redirect()->back()->with('status' , ' some thing error' );
        }
       
       
        $this->group->update($request,$id);
        return redirect()->back()->with('status' , 'Added Successfully' );
    }

    public function show($id)
    {
        $this->authorize('Show Group'); // permission check
        $group = $this->group->find($id);
        return response()->json(['data' => $group],200);
    }

     
	public function updateactive(Request $request)
    {
        $this->authorize('Active Group'); // permission check
        $id=$request->id;
        $group = $this->group->find($id);   
     
        $this->group->updateactive($group['active'],$id);
		   
        return response()->json([
            'message' => 'Updated Successfully',
            'status' => 'success'
        ]);

	}
    public function destroy($id){
        $this->authorize('Delete Group'); // permission check
    }

}
