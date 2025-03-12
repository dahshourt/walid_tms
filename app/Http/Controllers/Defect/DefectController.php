<?php

namespace App\Http\Controllers\Defect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;
use App\Http\Requests\Change_Request\Api\changeRequest_Requests;
use App\Http\Resources\ChangeRequestListResource;
use App\Http\Resources\ChangeRequestResource;
use App\Factories\ChangeRequest\AttachmetsCRSFactory;
use App\Factories\ChangeRequest\ChangeRequestFactory;
use App\Factories\ChangeRequest\ChangeRequestStatusFactory;
use App\Factories\CustomField\CustomFieldGroupTypeFactory;
use App\Factories\Defect\DefectFactory;
use App\Http\Repository\Defect\DefectRepository;
use App\Models\Technical_team;
use App\Models\Status;
use App\Models\DefectAttachment;
use App\Factories\Statuses\StatusFactory;

class DefectController extends Controller
{
    private $changerequest;
    private $status;
    private $defect;
    public function __construct(DefectFactory $defect , ChangeRequestFactory $changerequest, CustomFieldGroupTypeFactory $custom_field_group_type, StatusFactory $status)
    {
        $this->status = $status::index();
        $this->changerequest = $changerequest::index();
        $this->defect = $defect::index();
        $this->custom_field_group_type = $custom_field_group_type::index();
        $this->view = 'defect';
        $view = 'Defect';
        $title = 'Defect';
        $form_title = 'defect';
        $route = 'create_defect';
        view()->share(compact('view','route','title','form_title'));
       
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {
        
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
         $technical_team = Technical_team::all();
         $defect_status = $this->status->get_defect_status();
        $CustomFields = $this->custom_field_group_type->getAllCustomFieldsWithSelectedByformType("form_type", 7);

        return view("$this->view.create_defect", compact("id", "CustomFields", "technical_team", "defect_status"));  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $defect = $this->defect->AddDefect($request);
        $defect_id = $defect->id;
        
        $defect_status = $this->defect->AddDefectStatus($defect_id, 0, $request->defect_status);
        if($request->comment)
        {
            $comment_id = $this->defect->AddDefectComment($defect_id, $request->comment);
        }
        if($request->business_attachments)
        {
            $this->defect->Defect_Attach($request->business_attachments, $defect_id);
        }
        
        $this->defect->AddDefectLog($defect_id, "Defect {$defect_id} Created Successfully");
        return redirect()->back()->with('status' , 'Defect Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //custom fields
        $technical_team = Technical_team::all();
        $defect_status = $this->status->get_defect_status();
        $CustomFields = $this->custom_field_group_type->getAllCustomFieldsWithSelectedByformType("form_type", 7);
        //get Defect data
        $defect_data =  $this->defect->get_defect_data($id);
        $defect_comments = $this->defect->get_defect_comments($id);
        $defect_attachments = $this->defect->get_defect_attachments($id);
        return view("$this->view.edit_defect", compact("id", "CustomFields", "defect_status", "technical_team", "defect_data", "defect_comments", "defect_attachments"));  
    }


    public function download($id)
    {
        
        $file = DefectAttachment::findOrFail($id);
        $filePath = public_path("uploads\defects\\" . $file['file']); // in config
    
        if (file_exists($filePath)) {
            return response()->download($filePath, $file->file);
        }

        return redirect()->back()->withErrors('File not found.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
