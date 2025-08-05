<?php

namespace App\Http\Controllers\Search;

use App\Factories\ChangeRequest\ChangeRequestFactory;
use App\Factories\ChangeRequest\ChangeRequestStatusFactory;
use App\Factories\NewWorkFlow\NewWorkFlowFactory;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdvancedSearchRequestResource;
use App\Http\Resources\SearchRequestResource;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Search\TableExport;
use Illuminate\Http\Request;

// use App\Models\User;
// use App\Models\change_request;

// use App\Notifications\mail;
// use GuzzleHttp\Psr7\Request;
// use Notification;

class SearchController extends Controller
{
    private $changerequest;
    private $changerequeststatus;
    private $workflow;
    private $view;

    public function __construct(ChangeRequestFactory $changerequest, ChangeRequestStatusFactory $changerequeststatus, NewWorkFlowFactory $workflow)
    {

        /*$this->middleware(function ($request, $next) {
			$this->user= \Auth::user();
			if(!$this->user->hasRole('Super Admin') && !$this->user->can('Access Search'))
			{
				abort(403, 'This action is unauthorized.');
			}	
			else
			{
				return $next($request);
			}	
		});*/
        $this->changerequest = $changerequest::index();
        $this->changerequeststatus = $changerequeststatus::index();
        $this->changerworkflowequeststatus = $workflow::index();
        $this->view = "search";
        $view = 'search';
        $route = 'change_request';
        $OtherRoute = 'search';
        
        $title = 'Search';
        $form_title = 'search';
        view()->share(compact('view','route','title','form_title','OtherRoute'));
    }
    public function index()
    {
        $this->authorize('Access Search'); 
        return view("$this->view.create");
    }
    public function advanced_search(){
        $this->authorize('Access Advanced Search'); // permission check

        return view("$this->view.advanced_search");
    }
     public function edit($id)
    {
        $this->authorize('Access Search'); 
        $row = $this->changerequest->find($id);
       
        return view("$this->view.edit",compact('row'));

    }
    public function search_result()
    {
        $this->authorize('Access Search'); 

        $cr = $this->changerequest->searhchangerequest(request()->search);
		if(!$cr)
		{
			return redirect('/searchs')->with('error', 'CR NO not exists.');
		}
        //$change_request_custom_fields = $cr->change_request_custom_fields;
        //dd($change_request_custom_fields->where('custom_field_name','title')->first()->custom_field_value);
        $r=new ChangeRequestRepository();
        $crs_in_queues=  $r->getAllWithoutPagination()->pluck("id");
		
        return view("$this->view.index",compact('cr','crs_in_queues'));
    }

    public function AdvancedSearchResult() 
    {
        $this->authorize('Access Advanced Search'); // permission check
        $alldata=$this->changerequest->AdvancedSearchResult(1);
       
        // Retrieve the paginated collection from the model
        $collection = $this->changerequest->AdvancedSearchResult()->appends(request()->query());
        
        // Ensure $collection is an instance of Illuminate\Pagination\LengthAwarePaginator
        if (!($collection instanceof \Illuminate\Pagination\LengthAwarePaginator)) {
            abort(500, 'Expected paginated collection from AdvancedSearchResult.');
        }
        $totalCount = $collection->total(); 
        // Transform the collection into resource format
        $collection = AdvancedSearchRequestResource::collection($collection);
     //  $collection = $collection->toArray(request());
        
        $r = new ChangeRequestRepository();
        $crs_in_queues = $r->getAll()->pluck("id");
        
        // Pass the transformed data to the view
        //session(['advanced_search_items' => $alldata]);
		//session()->put('advanced_search_items', $alldata);
        return view("$this->view.AdvancedSearchResult", ['totalCount'=>$totalCount,'items' => $collection, 'crs_in_queues' => $crs_in_queues]);
    }
    public function AdvancedSearchResultExport(Request $request)
    {
		//dd($request->all());
        $this->authorize('Access Advanced Search'); // permission check
        
        $alldata=$this->changerequest->AdvancedSearchResult(1);
        $items = $alldata;
		//dd($items);
		//$items = \Session::get('advanced_search_items');
		
        // If you want to return the view with items (for debugging purposes):
        // return view("$this->view.AdvancedSearchResult", ['items' => $collection]);

        // Export the filtered results as Excel
        return Excel::download(new TableExport($items), 'advanced_search_results.xlsx');
    }
    
}
