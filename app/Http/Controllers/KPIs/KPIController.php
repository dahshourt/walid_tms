<?php

namespace App\Http\Controllers\KPIs;

use App\Factories\KPIs\KPIFactory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kpi;
use App\Http\Requests\KPIs\KPIRequest;
use App\Models\Change_request;

class KPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $KPI;

    public function __construct(KPIFactory $KPI)
    {

        $this->KPI = $KPI::index();
        $this->view = 'kpis';
        $view = 'kpis';
        $route = 'kpis';
        $OtherRoute = 'kpis';
        
        $title = 'Strategic KPIs';
        $form_title = 'Strategic KPIs';
        view()->share(compact('view', 'route', 'title', 'form_title', 'OtherRoute'));

    }

    public function index()
    {
        $this->authorize('List KPIs');
        $collection = $this->KPI->getAll();

        return view("$this->view.index", compact('collection'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('Create KPIs');
        $priorities = Kpi::PRIORITY;
        $quarters = Kpi::QUARTER;
        $types = Kpi::TYPE;
        $classifications = Kpi::CLASSIFICATION;

        return view("$this->view.create", compact('priorities', 'quarters', 'types', 'classifications'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(KPIRequest $request)
    {
        $this->authorize('Create KPIs');
        $data = array_merge($request->validated(), [
            'kpi_comment' => $request->input('kpi_comment'),
        ]);

        $this->KPI->create($data);

        return redirect()->route('kpis.index')->with('status', 'KPI Added Successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('Show KPIs');
        $row = $this->KPI->find($id);

        $priorities = Kpi::PRIORITY;
        $quarters = Kpi::QUARTER;
        $types = Kpi::TYPE;
        $classifications = Kpi::CLASSIFICATION;
        $logs = $row ? $row->logs : collect();
        $comments = $row ? $row->comments : collect();
        $changeRequests = $row ? $row->changeRequests : collect();

        return view("$this->view.show", compact('row', 'priorities', 'quarters', 'types', 'classifications', 'logs', 'comments', 'changeRequests'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('Edit KPIs');
        $row = $this->KPI->find($id);

        $priorities = Kpi::PRIORITY;
        $quarters = Kpi::QUARTER;
        $types = Kpi::TYPE;
        $classifications = Kpi::CLASSIFICATION;
        $logs = $row ? $row->logs : collect();
        $comments = $row ? $row->comments : collect();
        $changeRequests = $row ? $row->changeRequests : collect();

        return view("$this->view.edit", compact('row', 'priorities', 'quarters', 'types', 'classifications', 'logs', 'comments', 'changeRequests'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(KPIRequest $request, $id)
    {
        $this->authorize('Edit KPIs');
        $data = array_merge($request->validated(), [
            'kpi_comment' => $request->input('kpi_comment'),
        ]);

        $this->KPI->update($data, $id);

        return redirect()->route('kpis.edit', $id)->with('status', 'KPI Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('Delete KPIs');
        $this->KPI->delete($id);

        return redirect()->back()->with('success', 'KPI deleted successfully!');
    }

    /**
     * AJAX: search Change Request by cr_no for a specific KPI.
     */
    public function searchChangeRequest(Request $request, $kpiId)
    {
        $this->authorize('Edit KPIs');

        $request->validate([
            'cr_no' => ['required', 'integer'],
        ]);

        $crNo = $request->input('cr_no');

        $kpi = $this->KPI->find($kpiId);
        if (! $kpi) {
            return response()->json([
                'success' => false,
                'message' => 'KPI not found.',
            ], 404);
        }

        $cr = Change_request::with(['workflowType', 'currentStatusRel'])
            ->where('cr_no', $crNo)
            ->first();

        if (! $cr) {
            return response()->json([
                'success' => false,
                'message' => "Change Request #{$crNo} not found.",
            ], 404);
        }

        $alreadyLinked = $cr->kpis()->where('kpis.id', $kpi->id)->exists();

        $statusName = optional(optional($cr->currentStatusRel)->status)->status_name;
        $workflowName = $cr->workflowType->name ?? '';

        return response()->json([
            'success' => true,
            'already_linked' => $alreadyLinked,
            'data' => [
                'id' => $cr->id,
                'cr_no' => $cr->cr_no,
                'title' => $cr->title,
                'status' => $statusName,
                'workflow' => $workflowName,
                'show_url' => route('show.cr', $cr->id),
            ],
        ]);
    }

    /**
     * AJAX: attach a Change Request to KPI by cr_no.
     */
    public function attachChangeRequest(Request $request, $kpiId)
    {
        $this->authorize('Edit KPIs');

        $request->validate([
            'cr_no' => ['required', 'integer'],
        ]);

        $result = $this->KPI->attachChangeRequestByNumber($kpiId, $request->input('cr_no'));

        $statusCode = $result['success'] ?? false ? 200 : 422;

        return response()->json($result, $statusCode);
    }

    /**
     * AJAX: detach a Change Request from KPI.
     */
    public function detachChangeRequest($kpiId, $crId)
    {
        $this->authorize('Edit KPIs');

        $result = $this->KPI->detachChangeRequest($kpiId, $crId);

        $statusCode = $result['success'] ?? false ? 200 : 422;

        return response()->json($result, $statusCode);
    }
}
