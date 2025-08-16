<?php

namespace App\Http\Controllers\Sla;

use App\Http\Controllers\Controller;
use App\Models\SlaCalculation;
use App\Models\Status;
use App\Models\Group;
use Illuminate\Http\Request;

class SlaCalculationController extends Controller
{
     public function __construct()
    {
        
    }

    public function index()
    {
        view()->share('title', 'List');
        //view()->share('form_title', 'SLA');
        view()->share('route', 'sla-calculations');
        $calculations = SlaCalculation::with('status')->get();
        return view('sla.calculations.index', compact('calculations'));
    }

    public function create()
    {
        view()->share('title', 'Create');
        view()->share('form_title', 'SLA');
        view()->share('route', 'sla-calculations');
        $statuses = Status::all();
        $groups = Group::all();
        return view('sla.calculations.create', compact('statuses', 'groups'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'sla_time'  => 'required|integer',
        'type'      => 'required|in:day,hour',
        'status_id' => 'required|exists:statuses,id',
        'group_id'  => 'required|exists:groups,id',
    ]);

    SlaCalculation::create($validated);

    return redirect()->route('sla-calculations.index')
                     ->with('success', 'SLA Calculation created successfully.');
}

    public function show(SlaCalculation $slaCalculation)
    {
        return view('sla.calculations.show', compact('slaCalculation'));
    }

    public function edit(SlaCalculation $slaCalculation)
    { 
        view()->share('title', 'Edit');
        view()->share('form_title', 'Edit SLA');
        view()->share('route', 'sla-calculations');
         $statuses = Status::all();
         $groups = Group::all();
         $row = SlaCalculation::with('status')->find($slaCalculation->id);
        return view('sla.calculations.edit', compact('slaCalculation', 'row', 'statuses', 'groups'));
    }

    public function update(Request $request, SlaCalculation $slaCalculation)
    {
        $validated = $request->validate([
            'sla_time'  => 'required|integer',
            'type'      => 'required|in:day,hour',
            'status_id' => 'required|exists:statuses,id',
            'group_id'  => 'required|exists:groups,id',
        ]);

        $slaCalculation->update($validated);

        return redirect()->route('sla-calculations.index')
                        ->with('success', 'SLA Calculation updated successfully.');
    }

    public function destroy(SlaCalculation $slaCalculation)
    {
        $slaCalculation->delete();

        return redirect()->route('sla-calculations.index')
                         ->with('success', 'SLA Calculation deleted successfully.');
    }
}
