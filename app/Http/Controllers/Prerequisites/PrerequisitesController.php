<?php

namespace App\Http\Controllers\Prerequisites;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Prerequisites\PrerequisitesRequest;
use App\Factories\Prerequisites\PrerequisitesFactory;
use Illuminate\Http\Request;
use App\Models\change_request;
use App\Models\group;
use App\Models\Status;
use App\Models\Prerequisite;
use App\Models\PrerequisiteAttachment;

class PrerequisitesController extends Controller
{
    private $prerequisites;
    private $route = 'prerequisites';


    public function __construct(PrerequisitesFactory $prerequisites){
        $this->prerequisites = $prerequisites::index();
        $this->view = 'prerequisites';
        $view = 'prerequisites';
        $route = 'prerequisites';
        $OtherRoute = 'prerequisites';
        $title = 'Prerequisites';
        $form_title = 'Prerequisite';
        view()->share(compact('view','route','title','form_title','OtherRoute'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('List Prerequisites'); // permission check
        $collection = $this->prerequisites->paginateAll();
        return view("$this->view.index",compact('collection'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('Create Prerequisite'); // permission check
        $changeRequests = change_request::where('workflow_type_id', 9)->get();
        $groups = group::all();
        $defaultStatusId = Status::where('status_name', 'Open')->value('id');
        
        return view("$this->view.create", compact('changeRequests','groups','defaultStatusId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PrerequisitesRequest $request)
    {
        $this->authorize('Create Prerequisite'); // permission check
        $this->prerequisites->create($request->all());
        return redirect()->back()->with('status' , 'Prerequisite Created Successfully' );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Prerequisite  $prerequisite
     * @return \Illuminate\Http\Response
     */
    // In PrerequisitesController.php
    public function show(Prerequisite $prerequisite)
    {
        $this->authorize('Show Prerequisite');
        
        
        $prerequisite->load(['promo', 'group', 'status', 'comments', 'attachments', 'logs']);
        
        return view("$this->view.show", [
            'row' => $prerequisite,
            'form_title' => 'View Prerequisite',
            'changeRequests' => collect([$prerequisite->promo]),    
            'groups' => collect([$prerequisite->group]),    
            'statuses' => collect([$prerequisite->status]),    
            'currentStatus' => $prerequisite->status,
            'title' => 'View Prerequisite',
            'comments' => $prerequisite->comments,
            'logs' => $prerequisite->logs,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Prerequisite  $prerequisite
     * @return \Illuminate\Http\Response
     */
    public function edit(Prerequisite $prerequisite)
    {
        $this->authorize('Edit Prerequisite');

        $prerequisite->load(['comments', 'attachments', 'logs']);
        
        $changeRequests = change_request::where('workflow_type_id', 9)
            ->select('id', 'cr_no', 'title')
            ->get();
            
        $groups = group::select('id', 'title')->get();

        $currentStatus = $prerequisite->status;

        $availableStatuses = collect();

        $currentStatus = $prerequisite->status;

        if ($currentStatus->status_name === 'Open') {
            $availableStatuses = Status::whereIn('status_name', ['Open', 'Pending'])->get();
        } elseif ($currentStatus->status_name === 'Pending') {
            $availableStatuses = Status::whereIn('status_name', ['Pending', 'Open', 'Closed'])->get();
        } else {
            $availableStatuses = collect([$currentStatus]);
        }
        
        return view("$this->view.edit", [
            'row' => $prerequisite,
            'form_title' => 'Prerequisite',
            'changeRequests' => $changeRequests,
            'groups' => $groups,
            'statuses' => $availableStatuses,
            'currentStatus' => $currentStatus,
            'comments' => $prerequisite->comments,
            'attachments' => $prerequisite->attachments,
            'logs' => $prerequisite->logs,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Prerequisite  $prerequisite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Prerequisite $prerequisite)
    {
        $this->authorize('Edit Prerequisite'); // permission check
        
        $this->prerequisites->update($request->all(), $prerequisite);

        return redirect()->route("$this->route.index")->with('status', 'Prerequisite Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Prerequisite  $prerequisite
     * @return \Illuminate\Http\Response
     */
    public function destroy(Prerequisite $prerequisite)
    {
        //
    }

    public function download($id)
    {
        $attachment = PrerequisiteAttachment::findOrFail($id);
        $filePath = public_path('uploads/prerequisites/' . $attachment->file);

        if (file_exists($filePath)) {
            return response()->download($filePath, $attachment->file);
        }

        return redirect()->back()->with('error', 'File not found.');
    }

}
