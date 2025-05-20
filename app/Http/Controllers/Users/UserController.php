<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Users\UserRequest;
use App\Factories\Users\UserFactory;

use App\Http\Repository\Departments\DepartmentRepository;
use App\Http\Repository\Groups\GroupRepository;
use App\Http\Repository\Units\UnitRepository;
use App\Http\Repository\Roles\RolesRepository;
use App\Http\Repository\Permissions\PermissionRepository;
use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Users\TableExport;


use Illuminate\Http\Request;


class UserController extends Controller
{
    use ValidatesRequests;
    private $user;

    function __construct(UserFactory $user){
        
        $this->user = $user::index();
        $this->view = 'users';
        $view = 'users';
        $route = 'users';
        $OtherRoute = 'user';
        
        $title = 'Users';
        $form_title = 'User';
        view()->share(compact('view','route','title','form_title','OtherRoute'));
        
    }
    public function exportTable()
    {
         $this->authorize('List Users'); // permission check
        
        return Excel::download(new TableExport, 'Users-data.xlsx');
    }
    public function index()
    {
        $this->authorize('List Users'); // permission check
        $collection = $this->user->paginateAll();
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
        $this->authorize('Create User'); // permission check

        $departments = (new DepartmentRepository)->getAll();
        $groups = (new GroupRepository)->getAll();
        $units = (new UnitRepository)->getAll();
        $roles = (new RolesRepository)->list();
        $permissions = (new PermissionRepository)->list();

        return view("$this->view.create",compact('departments','groups','units','roles','permissions'));
    }

    /**
     * Send or resend the verification code.
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request)
    {
         $this->authorize('Create User'); // permission check
        $this->user->create($request->all());

        return redirect()->back()->with('status' , 'Added Successfully' );
    }


    public function edit($id)
    {
        $this->authorize('Edit User'); // permission check

        $row = $this->user->find($id);
        $departments = (new DepartmentRepository)->getAll();
        $groups = (new GroupRepository)->getAll();
        $units = (new UnitRepository)->getAll();
        $roles = (new RolesRepository)->list();
        $permissions = (new PermissionRepository)->list();

        // echo"<pre>";
        // print_r($row);
        // echo "</pre>"; die;
        return view("$this->view.edit",compact('row','departments','groups','units','roles','permissions'));

    }

    /**
     * Send or resend the verification code.
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, $id)
    {
        $this->authorize('Edit User'); // permission check

        // $validator = Validator::make($request->all(), [
        //     'password' => [
        //         'sometimes','nullable','confirmed','required_with:password_confirmed', function ($attribute, $value, $fail) {
        //             if (1!=1) {
        //                 $fail('Old Password didn\'t match');
        //             }
        //         },
        //     ],
        // ]);
        
        // if($validator->fails()) {
        //     return redirect()->back()->withInput()->withErrors($validator);
        // }
        
        $this->user->update($request->except(['_token', '_method','user_id']),$id);
        return redirect()->back()->with('status' , 'Updated Successfully' );
    }

    public function show($id)
    {
        $this->authorize('Show User'); // permission check

        $user = $this->user->find($id);
        if(!$user)
        {
            return response()->json([
                'message' => 'User Not Exists',
            ],422);
        }
        $user = new UsersResource($user);
        return response()->json(['data' => $user],200);
    }

    public function destroy()
    {
        $this->authorize('Delete User'); // permission check
        
    }
	public function updateactive(Request $request){

        $this->authorize('Active User'); // permission check
		   
        $data = $this->user->find($request->id);
		
		$this->user->updateactive($data->active,$request->id);
		   
		    return response()->json([
            'message' => 'Updated Successfully',
            'status' => 'success'
        ]);

		  
		
	}


    public function get_users_with_group_and_role($role_id,$default_group){
         
        $user = $this->user->get_users_with_group_and_role($role_id, $default_group);
        return response()->json([
            'data' => $user,
        ],200);
    }

}
