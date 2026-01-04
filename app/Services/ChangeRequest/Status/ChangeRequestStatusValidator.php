<?php

namespace App\Services\ChangeRequest\Status;

use App\Models\Change_request as ChangeRequest;
use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\NewWorkFlow;
use Exception;

class ChangeRequestStatusValidator
{
    /**
     * Validate the status change
     */
    /**
     * Validate the status change
     */
    public function validateStatusChange(ChangeRequestStatusContext $context): bool
    {
        // 1. Check if status is actually changing
        if ($context->statusData['old_status_id'] == $context->statusData['new_status_id']) {
            return false;
        }

        // 2. Check workflow dependencies
        if (!$this->checkWorkflowDependencies($context->changeRequest->id, $context->workflow->workflowstatus->first())) {
            // ...
        }

        return true;
    }

    /**
     * Check workflow dependencies
     */
    public function checkWorkflowDependencies(int $changeRequestId, $workflowStatus): bool
    {
        if (!$workflowStatus || !$workflowStatus->dependency_ids) {
            return true;
        }

        $dependencyIds = array_diff(
            $workflowStatus->dependency_ids,
            [$workflowStatus->new_workflow_id]
        );

        foreach ($dependencyIds as $workflowId) {
            if (!$this->isDependencyMet($changeRequestId, $workflowId)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a specific dependency is met
     */
    public function isDependencyMet(int $changeRequestId, int $workflowId): bool
    {
        $dependentWorkflow = NewWorkFlow::find($workflowId);

        if (!$dependentWorkflow) {
            return false;
        }

        return ChangeRequestStatus::where('cr_id', $changeRequestId)
            ->where('new_status_id', $dependentWorkflow->from_status_id)
            ->where('old_status_id', $dependentWorkflow->previous_status_id)
            ->where('active', '2') // Completed
            ->exists();
    }

    public function isTransitionFromPendingCab(ChangeRequestStatusContext $context): bool
    {
        // 75 is Pending CAB, 74 is Pending Design (usually)
        return $context->statusData['old_status_id'] == 75
            && $context->statusData['new_status_id'] == 74;
    }
}
