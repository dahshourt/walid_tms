<?php

namespace App\Http\Controllers\Statuses;

use App\Factories\Statuses\StatusFactory;
use App\Http\Controllers\Controller;
use App\Http\Repository\Groups\GroupRepository;
use App\Http\Repository\Stages\StageRepository;
use App\Http\Requests\Statuses\StatusRequest;
use App\Http\Resources\StatusResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    use ValidatesRequests;

    private $status;

    public function __construct(StatusFactory $status)
    {
        // $this->middleware(function($request,$next){
        //     dd($request);
        // });

        $this->status = $status::index();
        $this->view = 'statuses';
        $view = 'statuses';
        $route = 'statuses';
        $OtherRoute = 'status';

        $title = 'statuses';
        $form_title = 'status';
        view()->share(compact('view', 'route', 'title', 'form_title', 'OtherRoute'));

    }

    public function index()
    {
        $this->authorize('List Statuses'); // permission check
        //  = $this->status->getAll();
        $collection = StatusResource::collection($this->status->paginateAll(['setByGroupStatuses.group', 'viewByGroupStatuses.group', 'stage']));

        return view("$this->view.index", compact('collection'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('Create Status'); // permission check
        //
        $stages = (new StageRepository)->getAll();
        $groups = (new GroupRepository)->getAll();

        return view("$this->view.create", compact('stages', 'groups'));
    }

    /**
     * Send or resend the verification code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StatusRequest $request)
    {
        $this->authorize('Create Status'); // permission check
        $this->status->create($request->all());

        return redirect()->back()->with('status', 'Created Successfully');

    }

    public function edit($id)
    {
        $this->authorize('Edit Status'); // permission check
        $row = $this->status->find($id);
        $stages = (new StageRepository)->getAll();
        $groups = (new GroupRepository)->getAll();
        $set_group_ids = $row->group_statuses->where('type', 1)->pluck('group_id')->toArray();
        $view_group_ids = $row->group_statuses->where('type', 2)->pluck('group_id')->toArray();

        return view("$this->view.edit", compact('row', 'stages', 'groups', 'set_group_ids', 'view_group_ids'));

    }

    /**
     * Send or resend the verification code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(StatusRequest $request, $id)
    {
        $this->authorize('Edit Status'); // permission check

        $status = $this->status->find($id);

        if (! $status) {
            return redirect()->back()->with('status', 'status Not Exists');

        }
        $this->status->update($request, $id);

        // $this->updateactive($id);
        return redirect()->back()->with('status', 'Updated Successfully');

    }

    public function show($id)
    {
        $this->authorize('Show Status'); // permission check
        $status = $this->status->find($id);
        if (! $status) {
            return redirect()->back()->with('status', 'status Not Exists');
        }
        $status = new StatusResource($status);

        return redirect()->back()->with('status', 'Updated Successfully');
    }

    public function destroy()
    {
        $this->authorize('Delete Status'); // permission check

    }

    public function updateactive(Request $request)
    {

        $this->authorize('Active Status'); // permission check

        $id = $request->id;
        $status = $this->status->find($id);

        $this->status->updateactive($status['active'], $id);

        return response()->json([
            'message' => 'Updated Successfully',
            'status' => 'success',
        ]);

    }
}
