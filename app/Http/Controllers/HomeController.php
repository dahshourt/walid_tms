<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Repository\Statuses\StatusRepository;
use App\Http\Repository\Applications\ApplicationRepository;//ParentRepository
use App\Http\Repository\Workflow\Workflow_type_repository;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $route = '';
    public function __construct()
    {
         $this->route = '/charts_dashboard';
        // view()->share(compact('route'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        $statuses = (new StatusRepository)->getAll();
        $applications = (new ApplicationRepository)->getAll();
        $workflow_type = (new Workflow_type_repository)->get_workflow_all_subtype_without_release();
        $route = $this->route;

        /*$crs_group_by_status = "";
        $crs_group_by_applications = "";

         if ($request->isMethod('post') && $request->all()) 
         {
            $workflow_type_req = $request->workflow;
            $applications_req = $request->applications;
            $status_req = $request->status;
            
            $crs_group_by_status = (new StatusRepository)->get_crs_group_by_status($status_req, $workflow_type_req);
            $crs_group_by_status = json_encode($crs_group_by_status);
            $crs_group_by_applications = (new ApplicationRepository)->get_crs_group_bu_applications($applications_req, $workflow_type_req);
            $crs_group_by_applications = json_encode($crs_group_by_applications);
           // dd($crs_group_by_applications);

         }*/
        return view('home', compact('statuses', 'applications', 'workflow_type', 'route'));      
    }

    public function dashboard(Request $request)
    {
        $this->authorize('Dashboard'); // permission check
        $statuses = (new StatusRepository)->getAll();
        //$applications = (new ApplicationRepository)->getAll();
        $workflow_type = (new Workflow_type_repository)->get_workflow_all_subtype_without_release();
        // $workflowTypeId = $request->input('workflow');
        $applications =[];// $this->application_based_on_workflow($workflowTypeId);

        $route = $this->route;
        $route_ajax = "application_based_on_workflow";
        $crs_group_by_status = "";
        $crs_group_by_applications = "";

         if ($request->isMethod('post') && $request->all()) 
         {
            $workflow_type_req = $request->workflow;
            $applications_req = $request->applications;
            $status_req = $request->status;

            $validatedData = $request->validate([
                'workflow' => 'required',
            ]);

            //$crs_group_by_status = (new StatusRepository)->get_crs_group_by_status($status_req, $workflow_type_req);
            $crs_group_by_status = (new StatusRepository)->get_crs_group_by_status($status_req, $workflow_type_req,$applications_req);
            $crs_group_by_status = json_encode($crs_group_by_status);
            $crs_group_by_applications = (new ApplicationRepository)->get_crs_group_bu_applications($applications_req, $workflow_type_req);
            $crs_group_by_applications = json_encode($crs_group_by_applications);
           // dd($crs_group_by_applications);

         }
        return view('dashboard', compact('statuses', 'applications', 'workflow_type', 'route_ajax', 'route', 'crs_group_by_status', 'crs_group_by_applications')); 
    }

    public function application_based_on_workflow(Request $request)
    { 
        $workflowTypeId = $request->input('workflow_type_id');
        return  (new ApplicationRepository)->application_based_on_workflow($workflowTypeId);
    }

    public function SelectGroup()
    {
		//return view('auth.select_group');
    }

    public function storeGroup(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'group' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator);
        }
        \Session::put('group',\Auth::user()->user_groups->where('group_id',$request->group)->first()->group);
        return redirect('/');
    }



    public function StatisticsDashboard()
    {
        $this->authorize('Dashboard');
        $inhouse_crs = (new ChangeRequestRepository)->CountCrsPerSystem(3);
        
        $vendor_crs = (new ChangeRequestRepository)->CountCrsPerSystem(5);
        $status_crs = (new ChangeRequestRepository)->CountCrsPerStatus();
        $inhouse_crs_per_status_system = (new ChangeRequestRepository)->CountCrsPerSystemAndStatus(3);
        $vendor_crs_per_status_system = (new ChangeRequestRepository)->CountCrsPerSystemAndStatus(5);
        $inhouse_apps = (new ApplicationRepository)->application_based_on_workflow(3);
        $vendor_apps = (new ApplicationRepository)->application_based_on_workflow(5);
        return view('statistics_dashboard',compact('inhouse_crs','vendor_crs','status_crs','inhouse_crs_per_status_system','inhouse_apps','vendor_crs_per_status_system','vendor_apps')); 
    }

}
