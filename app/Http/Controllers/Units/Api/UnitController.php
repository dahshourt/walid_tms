<?php

namespace App\Http\Controllers\Units\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Units\Api\UnitRequest;
use App\Factories\Units\UnitFactory;

class UnitController extends Controller
{
    use ValidatesRequests;
    private $Unit;

    function __construct(UnitFactory $Unit){
        
        $this->Unit = $Unit::index();
        
    }

    public function index()
    {
        $Units = $this->Unit->getAll();
        return response()->json(['data' => $Units],200);
    }
    
    

}
