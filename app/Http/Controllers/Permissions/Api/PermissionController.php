<?php

namespace App\Http\Controllers\Permissions\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Factories\Permissions\PermissionFactory;
use Illuminate\Http\Request;
use Auth;
class PermissionController extends Controller
{
    use ValidatesRequests;
    private $group;

    function __construct(PermissionFactory $permission){
        
        $this->permission = $permission::index();
        
    }

    public function index(Request $request)
    {
      //  dd("l");
   
        $permission=$this->permission->getAll();
        return response()->json(['data' => $permission],200);

    }

    public function all()
    {
        
        $groups = $this->group->getAll();
        return response()->json(['data' => $groups],200);
    }
    /**
     * Send or resend the verification code.
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */

     public function permission_group($group){
        $pathh = $this->permission->permission_group($group);
        return response()->json(['data' => $pathh],200);

     }
    public function store(Request $request)
    {
       
        $store= $this->permission->store_permission($request->all());

        // dd($store);
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
    public function getpath($path)
    {
       // dd($path);
        $pathh = $this->permission->get_path($path);
        return response()->json(['data' => $pathh],200);
    }
    

}
