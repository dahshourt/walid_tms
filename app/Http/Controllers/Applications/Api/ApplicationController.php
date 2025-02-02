<?php

namespace App\Http\Controllers\Applications\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Applications\Api\ApplicationRequest;
use App\Factories\Applications\ApplicationFactory;

class ApplicationController extends Controller
{
    use ValidatesRequests;
    private $Application;

    function __construct(ApplicationFactory $Application){
        
        $this->Application = $Application::index();
        
    }

    public function index()
    {
        $Applications = $this->Application->getAll();
        return response()->json(['data' => $Applications],200);
    }
    
    

}
