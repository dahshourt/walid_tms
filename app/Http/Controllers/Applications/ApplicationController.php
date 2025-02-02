<?php

namespace App\Http\Controllers\Applications;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Applications\ApplicationRequest;
use App\Factories\Applications\ApplicationFactory;

class ApplicationController extends Controller
{
    use ValidatesRequests;
    private $Application;

    function __construct(ApplicationFactory $Application){
        
        $this->Application = $Application::index();
        $this->view = 'applications';
        $view = 'applications';
        $route = 'applications';
        $OtherRoute = 'application';
        
        $title = 'Applications';
        $form_title = 'Application';
        view()->share(compact('view','route','title','form_title','OtherRoute'));
        
    }

    public function index()
    {
        $collection = $this->Application->getAll();
        return view("$this->view.index",compact('collection'));
    }
    public function edit($id)
    {
        $row = $this->Application->find($id);
       
        return view("$this->view.edit",compact('row'));

    }
    

}
