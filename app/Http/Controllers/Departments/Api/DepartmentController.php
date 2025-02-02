<?php

namespace App\Http\Controllers\Departments\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Departments\Api\DepartmentRequest;
use App\Factories\Departments\DepartmentFactory;

class DepartmentController extends Controller
{
    use ValidatesRequests;
    private $Department;

    function __construct(DepartmentFactory $Department){
        
        $this->Department = $Department::index();
        
    }

    public function index()
    {
        $Departments = $this->Department->getAll();
        return response()->json(['data' => $Departments],200);
    }
    
    

}
