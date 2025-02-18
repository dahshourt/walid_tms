<?php

namespace App\Http\Controllers\ChangeRequest;

use App\Factories\ChangeRequest\AttachmetsCRSFactory;
use App\Factories\ChangeRequest\ChangeRequestFactory;
use App\Factories\ChangeRequest\ChangeRequestStatusFactory;
use App\Factories\NewWorkFlow\NewWorkFlowFactory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Change_Request\Api\attachments_CRS_Request;
use App\Http\Requests\Change_Request\Api\changeRequest_Requests;
use App\Http\Resources\ChangeRequestListResource;
use App\Http\Resources\ChangeRequestResource;
use App\Http\Resources\MyAssignmentsCRSResource;
use App\Http\Resources\MyCRSResource;
use App\Factories\Workflow\Workflow_type_factory;
use App\Factories\CustomField\CustomFieldGroupTypeFactory;
use App\Factories\Applications\ApplicationFactory;
use App\Factories\Logs\LogFactory;
use App\Factories\Users\UserFactory;
use App\Http\Resources\CustomFieldSelectedGroupResource;
use App\Models\Group;
use App\Models\Change_request;
use App\Models\attachements_crs;
use App\Models\User; 
use App\Models\WorkFlowType;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;
use Validator;
use App\Http\Controllers\Mail\MailController;

class ChangeRequestController extends Controller
{
    private $changerequest;
    private $changerequeststatus;
    private $workflow;
    private $workflow_type;
    private $logs;
    private $users;
    private $applications;
    public function __construct(ChangeRequestFactory $changerequest, ChangeRequestStatusFactory $changerequeststatus, NewWorkFlowFactory $workflow, AttachmetsCRSFactory $attachments,Workflow_type_factory $workflow_type,CustomFieldGroupTypeFactory $custom_field_group_type, ApplicationFactory $applications)
    {
        $this->changerequest = $changerequest::index();
        $this->changerequeststatus = $changerequeststatus::index();
        $this->changerworkflowequeststatus = $workflow::index();
        $this->workflow_type = $workflow_type::index();
        $this->attachments = $attachments::index();
        $this->custom_field_group_type = $custom_field_group_type::index();
        $this->applications = $applications::index();
        
        $this->view = 'change_request';
        $view = 'change_request';
        $route = 'change_request';
        //$OtherRoute = 'user';
        $title = 'List Change Requests';
        $form_title = 'CR';
        view()->share(compact('view','route','title','form_title'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('List change requests'); // permission check
       
        $collection = $this->changerequest->getAll();
        return view("$this->view.index",compact('collection'));
        
    }
      public function selectGroup($groupId)
{
   
    // Assuming you have the user's group stored in a session
    session(['default_group' => $groupId]);
   $group = Group::find($groupId);

    if ($group) {
        session(['default_group_name' => $group->title]);
    } else {
        session()->forget('default_group_name'); // Clear the name if the group is not found
    }
   // Store user groups in session flash data
   session()->put('user_groups', $userGroups);
    return redirect()->back();
}
    public function asd($group = null)

{
    
    $gr = Group::find($group);

    if ($gr) {
        session(['default_group_name' => $gr->title]);
    } else {
        session()->forget('default_group_name'); // Clear the name if the group is not found
    }

    // Check if group is provided in the URL; if not, handle the absence
    if (!$group) {
        return redirect()->back()->with('error', 'No group provided.');
    }
    session(['default_group' => $group]);
    // Fetch all user groups for the dropdown
    $userGroups = auth()->user()->user_groups()->with('group')->get();

    // Check if the provided group exists in the user's groups
    $selectedGroup = $userGroups->pluck('group.id')->contains($group);

    if (!$selectedGroup) {
        // If the group does not exist in the user's groups, return back with an error message
        return redirect()->back()->with('error', 'You do not have access to this group.');
    }
    $selectedGroup = Group::find($group);
    session()->put('current_group',$group);   
    session()->put('current_group_name',$selectedGroup->title);   
    return redirect()->back(); 
    //session()->set('current_group',$group);    
    // // Fetch the selected group object
    // $selectedGroup = Group::find($group);

    // // Fetch change request collection filtered by the group from the URL
    // $collection = $this->changerequest->getAll($group);

    // // Return the view with the selected group and user groups
    // return view("$this->view.index", compact('collection', 'selectedGroup', 'userGroups'));
}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Allsubtype()
    {
        $this->authorize('Create ChangeRequest');
        $get_workflow_subtype =  $this->workflow_type->get_workflow_all_subtype_without_release();
        $target_systems = $this->applications->getAll();
        return view("$this->view.list_work_flow",compact('target_systems'));
    }


    public function create()
    {
    
        $this->authorize('Create ChangeRequest'); // permission check
     
        $form_type = 1; // create CR form type id
        $target_system_id = request()->target_system_id;
        $target_system = $this->applications->find($target_system_id);                            
        //$workflow_type_id = request()->workflow_type_id;
        $workflow_type_id = $this->applications->workflowType($target_system_id)->id;
        
      //echo  $workflow_type_id;
        $CustomFields = $this->custom_field_group_type->CustomFieldsByWorkFlowType($workflow_type_id, $form_type);
    //    echo"<pre>";
    //    print_r($CustomFields);
    //    echo"</pre>"; die;
        $title = 'Create CR';
        return view("$this->view.create",compact('CustomFields','workflow_type_id','target_system','title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(changeRequest_Requests $request)
    {
        
        if($request->hasFile('attach')){
            $input_data = $request->all();

            $validator = Validator::make(
                $input_data, [
                'attach.*' => 'required|mimes:docx,doc,xls,xlsx,pdf,zip,rar,jpeg,jpg,png,gif,msg|max:2048'
                ],[
                    'attach.*.required' => 'Please upload an attachment',
                    'attach.*.mimes' => 'Only docx,doc,xls,xlsx,pdf,zip,rar,jpeg,jpg,png,gif,msg are allowed',
                    'attach.*.max' => 'Sorry! Maximum allowed size for an attachment is 2MB',
                ]
            );
    
            if ($validator->fails()) {
                //return redirect()->back()->withErrors('File not found.');
                //return redirect()->back()->with('error' , 'Created Successfully CR#'.$cr_id  );
                return redirect()->back()->withInput()->withErrors($validator);
            }
        }
        $cr_id = $this->changerequest->create($request->all()); 

        if ($request->file()) {
            $this->attachments->add_files($request->file('attach'), $cr_id);
        }
        return redirect()->back()->with('status' , 'Created Successfully CR#'.$cr_id  );
        
    }
  
    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    { 
        $this->authorize('Show ChangeRequest'); // permission check

        $cr = $this->changerequest->find($id);
        //dd($cr->logs);
        //$this->logs = LogFactory::index();
        if(!$cr)
        {
            $cr =  change_request::find($id);
        }
        $form_type = 2; // create CR form type id
        $title = 'View Change Request';
        $workflow_type_id = $cr->workflow_type_id;
        $status_id = $cr->getCurrentStatus()?->status?->id;
        $status_name = $cr->getCurrentStatus()?->status?->name;
        $CustomFields = $this->custom_field_group_type->CustomFieldsByWorkFlowTypeAndStatus($workflow_type_id, $form_type, $status_id);
        //$logs_ers = $this->logs->get_by_cr_id($id);
        $logs_ers = $cr->logs;
        return view("$this->view.show",compact('CustomFields','cr' , 'status_name' , 'title','logs_ers'));    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
        
        $this->authorize('Edit ChangeRequest'); // permission check
        //$this->logs = LogFactory::index();
        $cr = $this->changerequest->findById($id);

        if(!$cr)
        {
            return redirect()->back()->with('status' , 'CR not exists' );
        } //to check if the cr exists or not

        $cr = $this->changerequest->find($id);

        if(!$cr)
        {
            return redirect()->back()->with('status' , 'You have no access to edit this CR' );
        } // to check if the user has access to edit this cr or not 
        
        //dd($cr->change_request_custom_fields);
        $developer_users =  UserFactory::index()->get_user_by_department_id(2);
        $sa_users =  UserFactory::index()->get_user_by_department_id(4);
        $testing_users =  UserFactory::index()->get_user_by_department_id(3);
        $work= $cr->workflow_type_id;
        $cond = in_array($cr->RequestStatuses->last()?->new_status_id, [66, 67, 68, 69]);
        if(($work==5)&&$cond)
        {
            return redirect()->to('/change_request');
        }
      
        $cap_users =  UserFactory::index()->get_users_cap($cr->application_id);
        $form_type = 2; // create CR form type id
        $workflow_type_id = $cr->workflow_type_id;
        //$logs_ers = $this->logs->get_by_cr_id($id);
        $logs_ers = $cr->logs;
        //$CustomFields = $this->custom_field_group_type->CustomFieldsByWorkFlowType($workflow_type_id, $form_type);
        $status_id = $cr->getCurrentStatus()->status->id;
        $CustomFields = $this->custom_field_group_type->CustomFieldsByWorkFlowTypeAndStatus($workflow_type_id, $form_type, $status_id);
        return view("$this->view.edit",compact('cap_users','CustomFields','cr', 'workflow_type_id', 'logs_ers','developer_users','sa_users','testing_users'));  

    }

    public function download($id)
    {
        $file = attachements_crs::findOrFail($id);
        $filePath = public_path('uploads/' . $file['file_name']); // in config
    //dd($filePath);
        if (file_exists($filePath)) {
            return response()->download($filePath, $file->file);
        }

        return redirect()->back()->withErrors('File not found.');
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(changeRequest_Requests $request, $id)
    { 
      $mails = array();
      //dd(empty($request->cap_users));
      if(!empty($request->cap_users))
      {
        foreach($request->cap_users as $users)
          {
            $mails[] = User::find($users)?->email;
          }

          $mail =  new MailController();
          $mail->send_mail_to_cap_users($mails, $id);
      }
      

        if($request->hasFile('attach')){
            $input_data = $request->all();

            $validator = Validator::make(
                $input_data, [
                'attach.*' => 'required|mimes:docx,doc,xls,xlsx,pdf,zip,rar,jpeg,jpg,png,gif,msg|max:2048'
                ],[
                    'attach.*.required' => 'Please upload an attachment',
                    'attach.*.mimes' => 'Only docx,doc,xls,xlsx,pdf,zip,rar,jpeg,jpg,png,gif,msg are allowed',
                    'attach.*.max' => 'Sorry! Maximum allowed size for an attachment is 2MB',
                ]
            );

            if ($validator->fails()) {
                //return redirect()->back()->withErrors('File not found.');
                //return redirect()->back()->with('error' , 'Created Successfully CR#'.$cr_id  );
                return redirect()->back()->withInput()->withErrors($validator);
            }
        }
      $cr_id=  $this->changerequest->update($id, $request);
        if($cr_id==false){
            return redirect()->to('/change_request')-> with('error', 'No group provided.');

        }
        if ($request->file()) {
            $this->attachments->add_files($request->file('attach'), $id);
        }
        return redirect()->to('/change_request')->with('status' , 'Updated Successfully' );
        //return redirect()->back()->with('status' , 'Updated Successfully' );
    }

    public function update_attach(attachments_CRS_Request $request)
    {
        if ($request->file()) {
            $cr_id = $request->id;
            $this->attachments->add_files($request->file('filesdata'), $cr_id);
            // $this->attachments->update_files($request->file('filesdata'), $cr_id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('Delete ChangeRequest'); // permission check
        
    }
    public function  reorderhome(){
        return view("$this->view.shifiting");
       }
       public function reorderChangeRequest(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'change_request_id' => 'required|exists:change_request,id',
        ]);

        // Extract the Change Request ID from the request
        $crId = $request->input('change_request_id');

        // Call the repository method to reorder times
        $r=new ChangeRequestRepository();
        $result = $r->reorderTimes($crId);

        // Redirect back with success or error message
        if ($result['status']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
    public function search_result($id)
    {
        $cr = '39390';

        return response()->json(['data' => $cr], 200);
    }

    public function my_assignments()
    {
        
        $this->authorize('My Assignments'); // permission check
        
        $collection = $this->changerequest->my_assignments_crs();
        /* echo "<pre>";
        //print_r($collection[0]['RequestStatuses'][0]['status']['status_name']);
        print_r($collection['title']);
         echo "</pre>";
         dd('   oki');*/
        $title = "My Assignments";
        return view("$this->view.index",compact('collection','title'));
    }

    public function my_crs()
    {
        $crs = $this->changerequest->my_crs();
        $my_crs = MyCRSResource::collection($crs);

        return response()->json(['data' => $my_crs], 200);
    }

    public function list_crs_by_user(Request $request){
        
        $this->authorize('Show My CRs');
        $user_id = \Auth::user();
        
        $user_name = $user_id->user_name;

        $workflow_type = $request->input('workflow_type', 'In House');
        //dd($workflow_type);
        $query = new Change_request();
        $query = $query->with(['release','CurrentRequestStatuses'])->where("requester_id", $user_id->id);
        
        if($workflow_type){
            $workflow_type_id = WorkFlowType::where('name' ,$workflow_type)->whereNotNull('parent_id')->value('id');
            
            if($workflow_type_id){
               
                $query->where('workflow_type_id' ,$workflow_type_id);
            }
        }
        //dd($query->get()->toArray());
        $collection = $query->get();
        //$collection = $collection->toArray();
        $r=new ChangeRequestRepository();
        $crs_in_queues=  $r->getAll()->pluck("id");
        return view("$this->view.CRsByuser",compact('collection', 'user_name','crs_in_queues'));
    }

    /*public function Crsbyusers(Request $request)
    {
       $user_name = $request->userName;
       $user_id = User::where("user_name", $user_name)->pluck('id')->first();
       $collection = change_request::where("requester_id", $user_id)->get();
        return view("$this->view.CRsByuser",compact('collection', 'user_name'));

    }*/
  
}
