<?php

namespace App\Http\Controllers\Releases;

use App\Factories\Releases\ReleaseFactory;
use App\Http\Controllers\Controller;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;
use App\Http\Requests\Releases\ReleaseRequest;
use App\Http\Resources\releaseResource;
use App\Models\NewWorkFlow;
use App\Models\Status;
use App\Models\WorkFlowType;
use Exception;
use Illuminate\Http\Request;

class ReleaseController extends Controller
{
    private $release;

    public function __construct(ReleaseFactory $release)
    {

        $this->release = $release::index();
        $this->view = 'releases';
        $view = 'releases';
        $route = 'releases';
        $OtherRoute = 'release';

        $title = 'Releases';
        $form_title = 'Release';
        view()->share(compact('view', 'route', 'title', 'form_title', 'OtherRoute'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('List Release');
        if (isset($_GET['search'])) {
            $search = $_GET['search'];
        } else {
            $search = '';
        }
        $collection = $this->release->paginateAll($search);

        return view("$this->view.index", compact('collection'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('Create Release');

        $releaseWorkflowTypeId = WorkFlowType::where('name', 'Release')->whereNotNull('parent_id')->value('id');

        $workflow = NewWorkFlow::where('type_id', $releaseWorkflowTypeId)->first();

        $statuses = collect([$workflow->from_status]);

        // dd($statuses);

        // $statuses = Status::all();
        return view("$this->view.create", compact('statuses'));
    }

    public function reorderhome()
    {
        $this->authorize('Release To CRs');

        return view("$this->view.shifiting");
    }
    // public function show_crs(Request $request)
    // {
    //     // Validate the incoming request
    //     $request->validate([
    //         'change_request_id' => 'required|exists:change_request,id',
    //     ]);

    //     // Extract the Change Request ID from the request
    //     $crId = $request->input('change_request_id');

    //     // Call the repository method to reorder times
    //     $r=new ChangeRequestRepository();
    //     $changeRequest = $r->findWithReleaseAndStatus($crId);

    //     if ($changeRequest && $changeRequest->release) {
    //         $release = $changeRequest->release;
    //         $releaseStatus = $release->releaseStatus;
    //     }

    //     return view("$this->view.shifiting", compact('changeRequest', 'release', 'releaseStatus','errorMessage'));

    // }

    public function show_crs(Request $request)
    {

        $this->authorize('CRs Related To Releases');

        $changeRequest = null;
        $release = null;
        $releaseStatus = null;
        $errorMessage = null;

        try {
            // Validate the incoming request
            $request->validate([
                'change_request_id' => 'required|exists:change_request,id',
            ]);

            // Extract the Change Request ID from the request
            $crId = $request->input('change_request_id');

            // Call the repository method to retrieve the Change Request
            $repository = new ChangeRequestRepository();
            $changeRequest = $repository->findWithReleaseAndStatus($crId); // $changeRequest->release_name
            $release = $this->release->find($changeRequest->release_name);

            if ($$release) {

                $releaseStatus = $release->releaseStatus;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            $errorMessage = 'Invalid Change Request ID. Please ensure it exists in the database.';
        } catch (Exception $e) {
            // Handle unexpected errors
            $errorMessage = 'An unexpected error occurred: ' . $e->getMessage();
        }

        // Return the view with data and error message if any
        return view("$this->view.result_release", compact('changeRequest', 'release', 'releaseStatus', 'errorMessage'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReleaseRequest $request)
    {
        $this->authorize('Create Release');
        $this->release->create($request->all());

        return redirect()->back()->with('status', 'Template Added Successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('Show Release');

        $row = $this->release->show($id);

        $releaseWorkflowTypeId = WorkFlowType::where('name', 'Release')->whereNotNull('parent_id')->value('id');

        $workflow = NewWorkFlow::where('type_id', $releaseWorkflowTypeId)->first();

        $statuses = collect([$workflow->from_status]);

        // $release = releaseResource::collection($release);
        return view("$this->view.release_show", compact('row', 'statuses'));
    }

    public function show_release($id)
    {
        $this->authorize('Show Release');
        $row = $this->release->find($id);

        $releaseWorkflowTypeId = WorkFlowType::where('name', 'Release')->whereNotNull('parent_id')->value('id');

        // $workflow = NewWorkFlow::where('type_id', $releaseWorkflowTypeId)->first();
        $workflow = NewWorkFlow::where('from_status_id', $row->release_status)->where('type_id', $releaseWorkflowTypeId)->where('active', '1')->orderBy('id', 'desc')->get();
        // dd($workflow);
        $statuses = [];
        foreach ($workflow as $key => $value) {
            $statuses[] = $value->workflowstatus[0]->to_status;
        }
        $statuses = collect($statuses);
        // $current_status = collect([$workflow[0]->from_status]);
        $current_status = Status::find($row->release_status);
        $current_status = collect([$current_status]);

        // $release = releaseResource::collection($release);
        return view("$this->view.release_show", compact('row', 'statuses'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {

        $this->authorize('Edit Release');

        // dd($this->authorize('Edit Release'),$this->authorize('Edit Release Status'));

        $row = $this->release->find($id);

        $releaseWorkflowTypeId = WorkFlowType::where('name', 'Release')->whereNotNull('parent_id')->value('id');

        // $workflow = NewWorkFlow::where('type_id', $releaseWorkflowTypeId)->first();
        $workflow = NewWorkFlow::where('from_status_id', $row->release_status)->where('type_id', $releaseWorkflowTypeId)->where('active', '1')->orderBy('id', 'desc')->get();
        // dd($workflow);
        $statuses = [];
        foreach ($workflow as $key => $value) {
            $statuses[] = $value->workflowstatus[0]->to_status;
        }
        $statuses = collect($statuses);
        // $current_status = collect([$workflow[0]->from_status]);
        $current_status = Status::find($row->release_status);
        $current_status = collect([$current_status]);

        return view("$this->view.edit", compact('row', 'statuses', 'current_status'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReleaseRequest $request, $id)
    {
        $this->authorize('Edit Release');

        $this->release->update($request->except(['_token', '_method']), $id);

        return redirect()->back()->with('status', 'Release Updated Successfully');
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

    public function lisVendor()
    {
        $list_vendo = $this->release->listVendor();

        return response()->json(['data' => $list_vendo], 200);
    }

    public function lisReleaseStatus()
    {
        $listReleaseStatus = $this->release->listStatus();

        return response()->json(['data' => $listReleaseStatus], 200);
    }

    public function update_release_its_crs()
    {
        $this->authorize('Release To CRs');

        $all_releases = $this->release->get_iot_releass();

        foreach ($all_releases as $key => $value) {
            $next_status = $this->release->update_release_status($value->id);
            $this->release->update_crs_of_release($value->id, $next_status);
        }

    }

    public function ReleaseLogs($id)
    {
        $this->authorize('Show Release Logs');
        $logs = $this->release->DisplayLogs($id);

        return view("$this->view.release_logs",compact('logs'));
    }
}
