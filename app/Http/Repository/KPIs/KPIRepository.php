<?php

namespace App\Http\Repository\KPIs;

use App\Contracts\KPIs\KPIRepositoryInterface;
use App\Models\Kpi;
use App\Models\Change_request;
use Illuminate\Support\Facades\DB;

class KPIRepository implements KPIRepositoryInterface
{
    Public $finalStatuses;

    public function __construct()
    {
        $this->finalStatuses = [config('change_request.status_ids.Delivered'), config('change_request.status_ids.Closed'), config('change_request.status_ids.Cancel'), config('change_request.status_ids.Reject')];
    }
    
    public function getAll()
    {
        
        return Kpi::orderByDesc('created_at')->paginate(10);
    } // end method

    public function create($request)
    {
        return DB::transaction(function () use ($request) {
            $data = collect($request);

            // create KPI record (exclude comment-only)
            $kpiData = $data->except(['kpi_comment'])->all();
            if (! isset($kpiData['status'])) {
                $kpiData['status'] = 'Open';
            }
            $kpi = Kpi::create($kpiData);

            // base log entry
            $kpi->logs()->create([
                'user_id' => auth()->id(),
                'log_text' => "KPI was created with status < {$kpi->status} >",
            ]);

            // optional first comment
            if (! empty($request['kpi_comment'] ?? null)) {
                $kpi->comments()->create([
                    'user_id' => auth()->id(),
                    'comment' => $request['kpi_comment'],
                ]);

                $kpi->logs()->create([
                    'user_id' => auth()->id(),
                    'log_text' => "Comment < {$request['kpi_comment']} > was added",
                ]);
            }

            return $kpi;
        });
    }  // end method

    public function find($id)
    {
        return Kpi::with(['creator', 'comments.user', 'logs.user', 'changeRequests.workflowType', 'changeRequests.currentStatusRel'])
            ->find($id);
    } // end method

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $kpi = Kpi::findOrFail($id);
            $oldData = $kpi->replicate(); // Keep a copy of old data

            $data = collect($request);

            // Update the KPI
            $kpi->update($data->except(['kpi_comment'])->all());

            // Track changes
            $changes = $kpi->getChanges();
            $ignoreFields = ['updated_at', 'created_at', 'id'];

            foreach ($changes as $field => $newValue) {
                if (in_array($field, $ignoreFields)) {
                    continue;
                }

                $oldValue = $oldData->$field;
                
                $oldValStr = $oldValue ?? 'Empty';
                $newValStr = $newValue ?? 'Empty';

                $kpi->logs()->create([
                    'user_id' => auth()->id(),
                    'log_text' => ucfirst(str_replace('_', ' ', $field)) . " changed from < {$oldValStr} > to < {$newValStr} >",
                ]);
            }

            // add comment if provided
            if (! empty($request['kpi_comment'] ?? null)) {
                $kpi->comments()->create([
                    'user_id' => auth()->id(),
                    'comment' => $request['kpi_comment'],
                ]);

                $kpi->logs()->create([
                    'user_id' => auth()->id(),
                    'log_text' => "Comment added",
                ]);
            }

            return $kpi;
        });
    } // end method

    public function delete($id)
    {
        return Kpi::find($id)->delete();
    } // end method

    // attach kpi to cr (from change request page)
    public function attachKpiToChangeRequest($kpiId, $crNo)
    {
        return DB::transaction(function () use ($kpiId, $crNo) {

            $kpi = Kpi::findOrFail($kpiId);
            $cr = Change_request::where('cr_no', $crNo)->firstOrFail();

            //check if already linked to this cr
            $alreadyLinked = $kpi->changeRequests()
                                ->where('change_request.id', $cr->id)
                                ->exists();

            if ($alreadyLinked) {
                return [
                    'success' => true,
                    'kpi_status' => $kpi->status,
                    'cr' => $cr,
                ];
            } else {

                // check if this cr already linked to another kpi
                $crAlreadyLinkedToAnotherKpi = $cr->kpis()->exists();
                if ($crAlreadyLinkedToAnotherKpi) {

                    // get the kpi id of the cr
                    $kpiIdOfCr = $cr->kpis()->first()->id;

                    $this->detachChangeRequest($kpiIdOfCr, $cr->id);
                    $kpi->changeRequests()->attach($cr->id);

                    $this->recalculateStatusFromChangeRequests($kpi);

                    $kpi->logs()->create([
                        'user_id' => auth()->id(),
                        'log_text' => "Change Request #{$cr->cr_no} was linked to this KPI.",
                    ]);

                    return [
                        'success' => true,
                        'kpi_status' => $kpi->status,
                        'cr' => $cr,
                    ];
                } else {

                    $kpi->changeRequests()->attach($cr->id);

                    $this->recalculateStatusFromChangeRequests($kpi);

                    $kpi->logs()->create([
                        'user_id' => auth()->id(),
                        'log_text' => "Change Request #{$cr->cr_no} was linked to this KPI.",
                    ]);
                } 
            }
            return [
                'success' => true,
                'kpi_status' => $kpi->status,
                'cr' => $cr,
            ];
        });
    }


    // attach cr to kpi (from kpi page)
    public function attachChangeRequestByNumber($kpiId, $crNo)
    {
        return DB::transaction(function () use ($kpiId, $crNo) {
            $kpi = Kpi::findOrFail($kpiId);

            $cr = Change_request::where('cr_no', $crNo)->first();

            if (! $cr) {
                return [
                    'success' => false,
                    'message' => "Change Request #{$crNo} not found.",
                ];
            }

            // Check if CR is already linked to THIS KPI
            if ($kpi->changeRequests()->where('change_request.id', $cr->id)->exists()) {
                return [
                    'success' => false,
                    'message' => "Change Request #{$crNo} is already linked to this KPI.",
                ];
            }

            // Check if CR is already linked to ANY OTHER KPI
            $existingKpi = $cr->kpis()->first();
            if ($existingKpi) {
                return [
                    'success' => false,
                    'message' => "Change Request #{$crNo} is already linked to KPI: {$existingKpi->name}",
                ];
            }

            $kpi->changeRequests()->attach($cr->id);

            $this->recalculateStatusFromChangeRequests($kpi);

            $kpi->logs()->create([
                'user_id' => auth()->id(),
                'log_text' => "Change Request #{$cr->cr_no} was linked to this KPI.",
            ]);

            return [
                'success' => true,
                'message' => "Change Request #{$crNo} has been linked to this KPI.",
                'kpi_status' => $kpi->status,
                'cr' => $cr,
            ];
        });
    }

    public function detachChangeRequest($kpiId, $crId)
    {
        return DB::transaction(function () use ($kpiId, $crId) {
            $kpi = Kpi::findOrFail($kpiId);

            $cr = Change_request::findOrFail($crId);

            $kpi->changeRequests()->detach($cr->id);

            $this->recalculateStatusFromChangeRequests($kpi);

            $kpi->logs()->create([
                'user_id' => auth()->id(),
                'log_text' => "Change Request #{$cr->cr_no} was unlinked from this KPI.",
            ]);

            return [
                'success' => true,
                'message' => "Change Request #{$cr->cr_no} has been removed from this KPI.",
                'kpi_status' => $kpi->status,
            ];
        });
    }

    private function recalculateStatusFromChangeRequests(KPI $kpi): void
    {
        $kpi->loadMissing(['changeRequests.currentStatusRel']);

        if ($kpi->changeRequests->isEmpty()) {
            $newStatus = 'Open';
        } else{
            $newStatus = 'Delivered';

            foreach ($kpi->changeRequests as $cr) {
                if (! in_array($cr->currentStatusRel->new_status_id, $this->finalStatuses)) {
                    $newStatus = 'In Progress';
                    break;
                }
            }
        }

        if ($kpi->status !== $newStatus) {
            $oldStatus = $kpi->status;
            $kpi->update(['status' => $newStatus]);

            $kpi->logs()->create([
                'user_id' => auth()->id(),
                'log_text' => "Status recalculated from < {$oldStatus} > to < {$newStatus} > based on related Change Requests.",
            ]);
        }
    }

    /* private function isChangeRequestFinalForKpi(Change_request $cr): bool
    {
        if ($cr->isAlreadyCancelledOrRejected()) {
            return true;
        }

        if ($cr->inFinalState()) {
            return true;
        }

        $current = $cr->currentStatusRel;

        if (! $current || ! $current->status) {
            return false;
        }

        $highLevel = $current->status->high_level;

        if (! $highLevel) {
            return false;
        }

        return in_array($highLevel->name, ['Delivered', 'Closed', 'Canceled', 'Rejected'], true);
    } */
}
