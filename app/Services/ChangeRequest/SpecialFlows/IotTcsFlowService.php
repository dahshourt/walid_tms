<?php

namespace App\Services\ChangeRequest\SpecialFlows;

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use App\Models\NewWorkFlow;
use Illuminate\Support\Facades\Log;

/**
 * IotTcsFlowService
 *
 * Handles the IOT TCs parallel workflow merge logic:
 *
 * ┌─────────────────────────────────────┐
 * │  Pending IOT TCs Review QC  (active=1)│──► IOT TCs Review QC  (independent)
 * │  Pending IOT TCs Review SA  (active=1)│──► IOT TCs Review vendor ──► (merge) ──► IOT In Progress
 * └─────────────────────────────────────┘
 *
 * Rules:
 *  - Both "Pending IOT TCs Review QC" and "Pending IOT TCs Review SA" are active at the same time.
 *  - Transitioning QC branch (→ IOT TCs Review QC) is fully independent:
 *      it does NOT deactivate the SA branch record.
 *  - Transitioning SA branch (→ IOT TCs Review vendor) is the MERGE trigger:
 *      • If QC branch has already reached "IOT TCs Review QC" (active=2/completed),
 *        BOTH are done → create "IOT In Progress" with active=1.
 *      • If QC branch is still active (not yet moved), mark "IOT In Progress" as active=0
 *        (it will be activated later when the QC transition fires the merge check).
 *  - The QC transition also fires a post-merge check: once "IOT TCs Review QC" is completed,
 *    if SA has already created "IOT In Progress" with active=0, upgrade it to active=1.
 */
class IotTcsFlowService
{
    // ── Status name constants ────────────────────────────────────────────────
    private const STATUS_PENDING_QC  = 'Pending IOT TCs Review QC';
    private const STATUS_PENDING_SA  = 'Pending IOT TCs Review SA';
    private const STATUS_REVIEW_QC   = 'IOT TCs Review QC';
    private const STATUS_REVIEW_SA   = 'IOT TCs Review vendor';
    private const STATUS_IOT_IN_PROG = 'IOT In Progress';

    // ── Cached status IDs ────────────────────────────────────────────────────
    private ?int $pendingQcId  = null;
    private ?int $pendingSaId  = null;
    private ?int $reviewQcId   = null;
    private ?int $reviewSaId   = null;
    private ?int $iotInProgId  = null;

    public function __construct()
    {
        $this->pendingQcId  = $this->resolveStatusId(self::STATUS_PENDING_QC);
        $this->pendingSaId  = $this->resolveStatusId(self::STATUS_PENDING_SA);
        $this->reviewQcId   = $this->resolveStatusId(self::STATUS_REVIEW_QC);
        $this->reviewSaId   = $this->resolveStatusId(self::STATUS_REVIEW_SA);
        $this->iotInProgId  = $this->resolveStatusId(self::STATUS_IOT_IN_PROG);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PUBLIC API
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Check whether the incoming transition involves the IOT TCs parallel workflow.
     *
     * Called from ChangeRequestStatusService::processStatusUpdate() BEFORE the
     * normal workflow logic runs.
     *
     * Returns true  → caller should let this service handle the transition.
     * Returns false → normal workflow processing should continue.
     */
    public function isIotTcsTransition(int $crId, array $statusData): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $oldStatusId = (int) ($statusData['old_status_id'] ?? 0);
        $newStatusId = (int) ($statusData['new_status_id'] ?? 0);

        // We intercept transitions that originate from one of the two IOT pending statuses.
        // The "new_status_id" in statusData is the WORKFLOW ID, not the to-status-id, so
        // we resolve the to-status from the workflow record.
        $toStatusId = $this->resolveToStatusFromWorkflow($newStatusId);

        $isQcTransition = ($oldStatusId === $this->pendingQcId && $toStatusId === $this->reviewQcId);
        $isSaTransition = ($oldStatusId === $this->pendingSaId && $toStatusId === $this->reviewSaId);

        Log::info('IotTcsFlowService: isIotTcsTransition check', [
            'cr_id'          => $crId,
            'old_status_id'  => $oldStatusId,
            'to_status_id'   => $toStatusId,
            'is_qc'          => $isQcTransition,
            'is_sa'          => $isSaTransition,
        ]);

        return $isQcTransition || $isSaTransition;
    }

    /**
     * Handle the IOT TCs transition.
     *
     * Must be called only when isIotTcsTransition() returned true.
     * Applies the correct independence / merge logic and returns the active flag
     * value ('0' or '1') that was set on the newly created record.
     */
    public function handleIotTcsTransition(int $crId, array $statusData, array $context = []): string
    {
        $oldStatusId = (int) ($statusData['old_status_id'] ?? 0);
        $newWorkflowId = (int) ($statusData['new_status_id'] ?? 0);
        $toStatusId    = $this->resolveToStatusFromWorkflow($newWorkflowId);

        if ($oldStatusId === $this->pendingQcId && $toStatusId === $this->reviewQcId) {
            return $this->handleQcTransition($crId, $statusData, $context);
        }

        if ($oldStatusId === $this->pendingSaId && $toStatusId === $this->reviewSaId) {
            return $this->handleSaTransition($crId, $statusData, $context);
        }

        // Fallback – should not reach here if isIotTcsTransition() was checked first
        Log::warning('IotTcsFlowService: handleIotTcsTransition called for unrecognised transition', [
            'cr_id'          => $crId,
            'old_status_id'  => $oldStatusId,
            'to_status_id'   => $toStatusId,
        ]);
        return '1';
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PRIVATE HANDLERS
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Handle: Pending IOT TCs Review QC  →  IOT TCs Review QC
     *
     * This is the INDEPENDENT branch.
     *  1. Complete the current QC pending record (active → 2).
     *  2. Create IOT TCs Review QC with active=1 (always active, independent).
     *  3. Do NOT touch the SA branch record at all.
     *  4. Post-merge check: if SA branch already created "IOT In Progress" with active=0,
     *     now that QC is done too → upgrade it to active=1.
     */
    private function handleQcTransition(int $crId, array $statusData, array $context): string
    {
        Log::info('IotTcsFlowService: Handling QC branch transition', ['cr_id' => $crId]);

        // 1. Complete the Pending QC record
        $this->completeStatusRecord($crId, $this->pendingQcId);

        // 2. Create IOT TCs Review QC — always active=1 (independent)
        $this->createStatusRecord($crId, $this->pendingQcId, $this->reviewQcId, '1', $context);

        Log::info('IotTcsFlowService: QC branch → IOT TCs Review QC created (active=1)', ['cr_id' => $crId]);

        // 3. Post-merge check: activate IOT In Progress if SA already completed its side
        $this->tryActivateIotInProgress($crId);

        return '1';
    }

    /**
     * Handle: Pending IOT TCs Review SA  →  IOT TCs Review vendor  →  (merge) IOT In Progress
     *
     * This is the MERGE-TRIGGER branch.
     *  1. Complete the current SA pending record (active → 2).
     *  2. Create IOT TCs Review vendor with active=1 (it is the intermediate SA step).
     *  3. DO NOT deactivate the QC branch record.
     *  4. Complete IOT TCs Review vendor immediately (it is a pass-through in this merge model)
     *     and create IOT In Progress:
     *       - active=1 if QC branch already completed (reviewQcId record with active=2 exists)
     *       - active=0 otherwise (waiting for QC to finish)
     */
    private function handleSaTransition(int $crId, array $statusData, array $context): string
    {
        Log::info('IotTcsFlowService: Handling SA branch transition (merge trigger)', ['cr_id' => $crId]);

        // 1. Complete the Pending SA record
        $this->completeStatusRecord($crId, $this->pendingSaId);

        // 2. Create IOT TCs Review vendor as the SA intermediate step (active=1)
        $this->createStatusRecord($crId, $this->pendingSaId, $this->reviewSaId, '1', $context);

        Log::info('IotTcsFlowService: SA branch → IOT TCs Review vendor created (active=1)', ['cr_id' => $crId]);

        // 3. Immediately complete the IOT TCs Review vendor record
        //    (the merge happens AT this step — IOT TCs Review vendor is effectively
        //     completed as soon as it is created for the merge check)
        $this->completeStatusRecord($crId, $this->reviewSaId);

        // 4. Check whether QC branch is already done
        $qcDone = $this->isQcBranchComplete($crId);

        $iotActive = $qcDone ? '1' : '0';

        // 5. Create IOT In Progress
        $this->createStatusRecord($crId, $this->reviewSaId, $this->iotInProgId, $iotActive, $context);

        Log::info('IotTcsFlowService: IOT In Progress created', [
            'cr_id'      => $crId,
            'active'     => $iotActive,
            'qc_done'    => $qcDone,
        ]);

        return $iotActive;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // MERGE HELPER
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * After QC transition completes, check whether SA already created an "IOT In Progress"
     * record with active=0. If yes, both branches are now done → activate it.
     */
    private function tryActivateIotInProgress(int $crId): void
    {
        if (!$this->iotInProgId) {
            return;
        }

        // Find the pending (active=0) IOT In Progress record for this CR
        $pendingMerge = ChangeRequestStatus::where('cr_id', $crId)
            ->where('new_status_id', $this->iotInProgId)
            ->where('active', '0')
            ->orderBy('id', 'desc')
            ->first();

        if (!$pendingMerge) {
            Log::info('IotTcsFlowService: No pending IOT In Progress found — SA not yet done', [
                'cr_id' => $crId,
            ]);
            return;
        }

        // Both sides are now complete — activate the merge record
        $pendingMerge->update(['active' => '1']);

        Log::info('IotTcsFlowService: Both branches complete — activated IOT In Progress', [
            'cr_id'     => $crId,
            'record_id' => $pendingMerge->id,
        ]);
    }

    /**
     * Returns true if the QC branch has already moved past "Pending IOT TCs Review QC"
     * i.e. a completed (active=2) record for reviewQcId exists for this CR.
     */
    private function isQcBranchComplete(int $crId): bool
    {
        if (!$this->reviewQcId) {
            return false;
        }

        // A completed IOT TCs Review QC record means the QC branch moved on
        $exists = ChangeRequestStatus::where('cr_id', $crId)
            ->where('new_status_id', $this->reviewQcId)
            ->where('active', '2') // completed
            ->exists();

        Log::info('IotTcsFlowService: QC branch completion check', [
            'cr_id'    => $crId,
            'complete' => $exists,
        ]);

        return $exists;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // DB HELPERS
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Mark the most-recent active record for $statusId as completed (active=2).
     */
    private function completeStatusRecord(int $crId, int $statusId): void
    {
        $updated = ChangeRequestStatus::where('cr_id', $crId)
            ->where('new_status_id', $statusId)
            ->where('active', '1')
            ->orderBy('id', 'desc')
            ->limit(1)
            ->update(['active' => '2']);

        Log::info('IotTcsFlowService: completeStatusRecord', [
            'cr_id'     => $crId,
            'status_id' => $statusId,
            'updated'   => $updated,
        ]);
    }

    /**
     * Create a new change_request_statuses record.
     *
     * Copies group/user context from the existing record of $fromStatusId when available,
     * falling back to context values passed in.
     */
    private function createStatusRecord(
        int $crId,
        int $fromStatusId,
        int $toStatusId,
        string $active,
        array $context
    ): void {
        // Derive group information from the most-recent status record of fromStatus
        $template = ChangeRequestStatus::where('cr_id', $crId)
            ->where('new_status_id', $fromStatusId)
            ->orderBy('id', 'desc')
            ->first();

        $toStatus = Status::find($toStatusId);
        $sla      = $toStatus ? (int) $toStatus->sla : 0;

        $userId           = $context['user_id']           ?? ($template->user_id ?? \Auth::id());
        $currentGroupId   = $context['current_group_id']  ?? ($template->current_group_id ?? null);
        $previousGroupId  = $context['previous_group_id'] ?? ($template->current_group_id ?? null);
        $referenceGroupId = $context['reference_group_id'] ?? ($template->reference_group_id ?? null);

        // Resolve the view group for the target status if possible
        if (isset($context['application_id']) && $toStatus) {
            $viewGroup = $toStatus->GetViewGroup($context['application_id']);
            if ($viewGroup) {
                $currentGroupId = $viewGroup->id;
            }
        }

        ChangeRequestStatus::create([
            'cr_id'              => $crId,
            'old_status_id'      => $fromStatusId,
            'new_status_id'      => $toStatusId,
            'group_id'           => null,
            'reference_group_id' => $referenceGroupId,
            'previous_group_id'  => $previousGroupId,
            'current_group_id'   => $currentGroupId,
            'user_id'            => $userId,
            'sla'                => $sla,
            'active'             => $active,
            'created_at'         => now(),
            'updated_at'         => null,
        ]);

        Log::info('IotTcsFlowService: createStatusRecord', [
            'cr_id'         => $crId,
            'from_status'   => $fromStatusId,
            'to_status'     => $toStatusId,
            'active'        => $active,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // UTILITY
    // ═══════════════════════════════════════════════════════════════════════════

    private function isConfigured(): bool
    {
        return $this->pendingQcId  !== null
            && $this->pendingSaId  !== null
            && $this->reviewQcId   !== null
            && $this->reviewSaId   !== null
            && $this->iotInProgId  !== null;
    }

    private function resolveStatusId(string $statusName): ?int
    {
        $status = Status::where('status_name', $statusName)
            ->where('active', '1')
            ->first();

        if (!$status) {
            Log::warning('IotTcsFlowService: Status not found in DB', ['status_name' => $statusName]);
        }

        return $status?->id;
    }

    /**
     * Given a workflow ID (new_workflow_id / new_status_id from the request),
     * resolve the actual to_status_id from new_workflow_statuses.
     */
    private function resolveToStatusFromWorkflow(int $workflowId): ?int
    {
        $workflow = NewWorkFlow::with('workflowstatus')->find($workflowId);

        if (!$workflow || $workflow->workflowstatus->isEmpty()) {
            return null;
        }

        return (int) $workflow->workflowstatus->first()->to_status_id;
    }
}
