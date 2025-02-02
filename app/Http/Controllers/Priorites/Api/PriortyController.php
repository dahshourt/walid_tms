<?php

namespace App\Http\Controllers\Priorites\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
//use App\Http\Requests\Priotitys\Api\PriotityRequest;
use App\Factories\Priorites\PriorityFactory;

class PriortyController extends Controller
{
    use ValidatesRequests;
    private $Priotity;

    function __construct(PriorityFactory $Priotity){
        
        $this->Priotity = $Priotity::index();
        
    }

    public function index()
    {
        $Priotites = $this->Priotity->getAll();
        return response()->json(['data' => $Priotites],200);
    }
    
    

}
