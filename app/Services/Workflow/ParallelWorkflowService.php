<?php

namespace App\Services\Workflow;

use App\Models\ChangeRequestStatus;
use App\Models\Change_request;
use App\Models\Group;
use App\Models\ParallelWorkflow;
use App\Models\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ParallelWorkflowService
{
    /**
     * Initialize a new parallel workflow for a change request
     *
     * @param Change_request $changeRequest
     * @param string $splitStatusName
     * @param string $joinStatusName
     * @return ParallelWorkflow
     * @throws \Exception
     */
    public function initiateParallelWorkflow(Change_request $changeRequest, string $splitStatusName, string $joinStatusName): ParallelWorkflow
    {
        return DB::transaction(function () use ($changeRequest, $splitStatusName, $joinStatusName) {
            // Get the required statuses from the database
            $splitStatus = Status::where('status_name', $splitStatusName)->firstOrFail();
            $joinStatus = Status::where('status_name', $joinStatusName)->firstOrFail();
            $draftCrDocStatus = Status::where('status_name', 'Request Draft CR Doc')->firstOrFail();

            // Check if UI/UX custom field is set to 1
            $uiUxField = $changeRequest->changeRequestCustomFields()
                ->where('custom_field_name', 'ui_ux')
                ->first();

            if (!$uiUxField || $uiUxField->custom_field_value != '1') {
                throw new \Exception('UI/UX custom field is not set to 1');
            }

            // Update the change request status to Request Draft CR Doc
            $changeRequest->update(['status_id' => $draftCrDocStatus->id]);
            
            // Create status record for the transition to Request Draft CR Doc
            ChangeRequestStatus::create([
                'cr_id' => $changeRequest->id,
                'old_status_id' => $splitStatus->id,
                'new_status_id' => $draftCrDocStatus->id,
                'group_id' => null,
                'active' => true,
            ]);

            // Create the workflow tracking record
            $workflow = ParallelWorkflow::create([
                'cr_id' => $changeRequest->id,
                'split_status_id' => $splitStatus->id,
                'join_status_id' => $joinStatus->id,
                'workflow_instance_id' => (string) Str::uuid(),
                'required_completions' => 2, // Two parallel paths
                'completed_workflows' => 0,
                'is_completed' => false,
            ]);

            // Create first parallel branch
            $firstPathStartStatus = Status::where('status_name', 'Pending First Path Approval')->firstOrFail();
            $firstPathEndStatus = Status::where('status_name', 'First Path Completed')->firstOrFail();
            
            $firstBranch = $workflow->branches()->create([
                'group_id' => null, // No group association
                'start_status_id' => $firstPathStartStatus->id,
                'end_status_id' => $firstPathEndStatus->id,
                'current_status_id' => $firstPathStartStatus->id,
                'is_completed' => false,
            ]);

            // Create second parallel branch
            $secondPathStartStatus = Status::where('status_name', 'Pending Second Path Approval')->firstOrFail();
            $secondPathEndStatus = Status::where('status_name', 'Second Path Completed')->firstOrFail();
            
            $secondBranch = $workflow->branches()->create([
                'group_id' => null, // No group association
                'start_status_id' => $secondPathStartStatus->id,
                'end_status_id' => $secondPathEndStatus->id,
                'current_status_id' => $secondPathStartStatus->id,
                'is_completed' => false,
            ]);

            // Create initial status records for each branch
            $this->createStatusRecord($changeRequest, $splitStatus->id, $firstPathStartStatus->id, null);
            $this->createStatusRecord($changeRequest, $splitStatus->id, $secondPathStartStatus->id, null);

            return $workflow;
        });
    }

    /**
     * Handle status update for a change request
     *
     * @param Change_request $changeRequest
     * @param int $oldStatusId
     * @param int $newStatusId
     * @param int|null $groupId
     * @return void
     */
    public function handleStatusUpdate(Change_request $changeRequest, int $oldStatusId, int $newStatusId, ?int $groupId = null): void
    {
        // Check if this change request is part of an active parallel workflow
        $workflow = ParallelWorkflow::where('cr_id', $changeRequest->id)
            ->where('is_completed', false)
            ->first();

        if (!$workflow) {
            return; // Not part of an active parallel workflow
        }

        // Find the branch that has the current status
        $branch = $workflow->branches()
            ->where('current_status_id', $oldStatusId)
            ->where('is_completed', false)
            ->first();

        if (!$branch) {
            return; // No active branch found with the current status
        }

        // Update the branch status
        $branch->update(['current_status_id' => $newStatusId]);

        // Check if this branch is now complete
        if ($newStatusId === $branch->end_status_id) {
            $branch->update(['is_completed' => true]);
            $workflow->increment('completed_workflows');

            // Check if all branches are complete
            if ($workflow->completed_workflows >= $workflow->required_completions) {
                $workflow->update(['is_completed' => true]);
                
                // Update the change request to the join status
                $changeRequest->update(['status_id' => $workflow->join_status_id]);
                
                // Create a status record for the join
                ChangeRequestStatus::create([
                    'cr_id' => $changeRequest->id,
                    'old_status_id' => $oldStatusId,
                    'new_status_id' => $workflow->join_status_id,
                    'group_id' => $groupId,
                    'active' => true,
                ]);
            }
        }
    }

    /**
     * Create a status record for a branch
     *
     * @param ChangeRequest $changeRequest
     * @param int $oldStatusId
     * @param int $newStatusId
     * @param int $groupId
     * @return ChangeRequestStatus
     */
    protected function createStatusRecord(Change_request $changeRequest, int $oldStatusId, int $newStatusId, int $groupId): ChangeRequestStatus
    {
        return ChangeRequestStatus::create([
            'cr_id' => $changeRequest->id,
            'old_status_id' => $oldStatusId,
            'new_status_id' => $newStatusId,
            'group_id' => $groupId,
            'active' => true,
        ]);
    }

    public function isWorkflowCompleted(ParallelWorkflow $workflow): bool
    {
        return $workflow->is_completed;
    }
}