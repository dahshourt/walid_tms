<?php

namespace App\Services\ChangeRequest\SpecialFlows;

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use App\Models\NewWorkFlow;
use Illuminate\Support\Facades\Log;

/**
 * IOT TCs Review Parallel Workflow Service
 * 
 * Handles the parallel workflow where "Vendor Internal Test" creates 3 statuses:
 * - Pending IOT TCs Review QC  → IOT TCs Review QC     ─┐
 * - Pending IOT TCs Review SA  → IOT TCs Review vendor  ─┤── merge → IOT In Progress
 * - Pending IOT TCs Review     → (next status)          ─┘
 *
 * Each branch must be able to transition INDEPENDENTLY without deactivating siblings.
 * Only when ALL branches complete do they merge into "IOT In Progress".
 */
class IotTcsFlowService
{
    // All IOT TCs "Pending" parallel statuses (the ones created together)
    private const IOT_PENDING_STATUS_NAMES = [
        'Pending IOT TCs Review QC',
        'Pending IOT TCs Review SA',
        'Pending IOT TCs Review',
    ];

    // The "in-progress" statuses each pending transitions to
    private const IOT_INPROGRESS_STATUS_NAMES = [
        'IOT TCs Review QC',
        'IOT TCs Review vendor',
    ];

    // The merge point status name
    private const IOT_MERGE_STATUS_NAME = 'IOT In progress';

    // The parent status name
    private const IOT_PARENT_STATUS_NAME = 'Vendor Internal Test';

    /** @var array|null Cached pending status IDs */
    private ?array $pendingStatusIds = null;

    /** @var array|null Cached in-progress status IDs */
    private ?array $inProgressStatusIds = null;

    /** @var int|null Cached merge status ID */
    private ?int $mergeStatusId = null;

    /**
     * Check if a status ID is one of the IOT TCs parallel "Pending" statuses
     */
    public function isIOTTcsPendingStatus(int $statusId): bool
    {
        return in_array($statusId, $this->getPendingStatusIds());
    }

    /**
     * Check if a status ID is one of the IOT TCs "in-progress" statuses
     */
    public function isIOTTcsInProgressStatus(int $statusId): bool
    {
        return in_array($statusId, $this->getInProgressStatusIds());
    }

    /**
     * Check if two statuses are IOT TCs siblings that should preserve each other
     * 
     * @param int $currentStatusId The status being transitioned FROM
     * @param int $otherStatusId The other active status to check
     * @return bool True if otherStatusId should be preserved (not deactivated)
     */
    public function shouldPreserveSibling(int $currentStatusId, int $otherStatusId): bool
    {
        $pendingIds = $this->getPendingStatusIds();

        // Both must be IOT TCs pending statuses and they must be different
        if (in_array($currentStatusId, $pendingIds) && in_array($otherStatusId, $pendingIds)) {
            return $currentStatusId !== $otherStatusId;
        }

        return false;
    }

    /**
     * Handle the IOT TCs parallel status activation/deactivation after a transition.
     * 
     * Called from processStatusUpdate() after creating new statuses.
     * Ensures that when transitioning FROM one IOT TCs pending status,
     * the sibling pending statuses remain active.
     *
     * @param int $crId Change Request ID
     * @param array $statusData Current status transition data
     * @return string|null Returns the active flag if modified, null otherwise
     */
    public function handleIOTTcsTransition(int $crId, array $statusData): ?string
    {
        $oldStatusId = $statusData['old_status_id'] ?? null;

        if (!$oldStatusId || !$this->isIOTTcsPendingStatus($oldStatusId)) {
            return null;
        }

        Log::info('IOT TCs parallel transition detected', [
            'cr_id' => $crId,
            'from_status_id' => $oldStatusId,
            'from_status_name' => $this->getStatusNameById($oldStatusId),
        ]);

        // Ensure sibling pending statuses remain active
        $siblingIds = $this->getSiblingPendingIds($oldStatusId);
        
        foreach ($siblingIds as $siblingId) {
            // Re-activate sibling if it was wrongly deactivated by normal workflow logic
            $siblingRecord = ChangeRequestStatus::where('cr_id', $crId)
                ->where('new_status_id', $siblingId)
                ->orderBy('id', 'desc')
                ->first();

            if ($siblingRecord && $siblingRecord->active === '0') {
                $siblingRecord->update(['active' => '1']);

                Log::info('IOT TCs sibling RE-ACTIVATED (was wrongly deactivated)', [
                    'cr_id' => $crId,
                    'sibling_status_id' => $siblingId,
                    'sibling_status_name' => $this->getStatusNameById($siblingId),
                    'record_id' => $siblingRecord->id,
                ]);
            } elseif ($siblingRecord && $siblingRecord->active === '1') {
                Log::info('IOT TCs sibling confirmed still active', [
                    'cr_id' => $crId,
                    'sibling_status_id' => $siblingId,
                    'sibling_status_name' => $this->getStatusNameById($siblingId),
                    'record_id' => $siblingRecord->id,
                ]);
            }
        }

        return '1'; // Current transition should be active
    }

    /**
     * Check if CR has reached IOT TCs merge point.
     * All IOT TCs in-progress branches must be completed (active=2)
     * before "IOT In Progress" can become active.
     *
     * @param int $crId
     * @param int $toStatusId The status being transitioned TO
     * @return string '1' if all branches complete, '0' if still waiting
     */
    public function determineMergeActiveStatus(int $crId, int $toStatusId): string
    {
        $mergeStatusId = $this->getMergeStatusId();

        // Only apply merge logic if transitioning TO the merge status
        if ($mergeStatusId === null || $toStatusId !== $mergeStatusId) {
            return '1'; // Not a merge transition, default active
        }

        // Check if this CR used IOT TCs parallel workflows
        if (!$this->didUseIOTTcsParallel($crId)) {
            return '1'; // No parallel workflow, default active
        }

        // Count how many unique paths have reached the merge point
        $mergeRecords = ChangeRequestStatus::where('cr_id', $crId)
            ->where('new_status_id', $mergeStatusId)
            ->get();

        $uniquePaths = $mergeRecords->pluck('old_status_id')->unique()->count();

        // We need records from all IOT branches that were created
        $totalBranches = $this->countActiveBranches($crId);

        $allComplete = $uniquePaths >= $totalBranches && $totalBranches > 1;

        Log::info('IOT TCs merge point check', [
            'cr_id' => $crId,
            'unique_paths_completed' => $uniquePaths,
            'total_branches' => $totalBranches,
            'all_complete' => $allComplete,
        ]);

        return $allComplete ? '1' : '0';
    }

    /**
     * Re-activate "IOT In Progress" if it was set to active=0 and now all branches are done
     */
    public function activateMergeIfReady(int $crId, array $statusData): void
    {
        $mergeStatusId = $this->getMergeStatusId();
        if ($mergeStatusId === null) {
            return;
        }

        // Check if there's an inactive merge record waiting
        $pendingMerge = ChangeRequestStatus::where('cr_id', $crId)
            ->where('new_status_id', $mergeStatusId)
            ->where('active', '0')
            ->first();

        if (!$pendingMerge) {
            return;
        }

        // Check if all branches have now reached merge
        $activeStatus = $this->determineMergeActiveStatus($crId, $mergeStatusId);

        if ($activeStatus === '1') {
            $pendingMerge->update(['active' => '1']);

            Log::info('IOT TCs merge point activated - all branches complete', [
                'cr_id' => $crId,
                'merge_record_id' => $pendingMerge->id,
                'merge_status_id' => $mergeStatusId,
            ]);
        }
    }

    /**
     * Check if a CR used the IOT TCs parallel workflow
     */
    private function didUseIOTTcsParallel(int $crId): bool
    {
        $parentStatusId = $this->getStatusIdByName(self::IOT_PARENT_STATUS_NAME);
        if (!$parentStatusId) {
            return false;
        }

        $pendingIds = $this->getPendingStatusIds();
        if (empty($pendingIds)) {
            return false;
        }

        // Check if at least 2 of the IOT TCs pending statuses were created from the parent
        $branchCount = ChangeRequestStatus::where('cr_id', $crId)
            ->where('old_status_id', $parentStatusId)
            ->whereIn('new_status_id', $pendingIds)
            ->distinct('new_status_id')
            ->count('new_status_id');

        return $branchCount >= 2;
    }

    /**
     * Count how many IOT TCs branches were created for this CR
     */
    private function countActiveBranches(int $crId): int
    {
        $parentStatusId = $this->getStatusIdByName(self::IOT_PARENT_STATUS_NAME);
        if (!$parentStatusId) {
            return 0;
        }

        $pendingIds = $this->getPendingStatusIds();

        return ChangeRequestStatus::where('cr_id', $crId)
            ->where('old_status_id', $parentStatusId)
            ->whereIn('new_status_id', $pendingIds)
            ->distinct('new_status_id')
            ->count('new_status_id');
    }

    /**
     * Get sibling pending status IDs (all except the given one)
     */
    private function getSiblingPendingIds(int $excludeStatusId): array
    {
        return array_values(array_filter(
            $this->getPendingStatusIds(),
            fn($id) => $id !== $excludeStatusId
        ));
    }

    /**
     * Get all IOT TCs pending status IDs (cached)
     */
    public function getPendingStatusIds(): array
    {
        if ($this->pendingStatusIds === null) {
            $this->pendingStatusIds = Status::whereIn('status_name', self::IOT_PENDING_STATUS_NAMES)
                ->where('active', '1')
                ->pluck('id')
                ->toArray();
        }
        return $this->pendingStatusIds;
    }

    /**
     * Get all IOT TCs in-progress status IDs (cached)
     */
    public function getInProgressStatusIds(): array
    {
        if ($this->inProgressStatusIds === null) {
            $this->inProgressStatusIds = Status::whereIn('status_name', self::IOT_INPROGRESS_STATUS_NAMES)
                ->where('active', '1')
                ->pluck('id')
                ->toArray();
        }
        return $this->inProgressStatusIds;
    }

    /**
     * Get the merge status ID (cached)
     */
    public function getMergeStatusId(): ?int
    {
        if ($this->mergeStatusId === null) {
            $this->mergeStatusId = $this->getStatusIdByName(self::IOT_MERGE_STATUS_NAME);
        }
        return $this->mergeStatusId;
    }

    private function getStatusIdByName(string $name): ?int
    {
        $status = Status::where('status_name', $name)->where('active', '1')->first();
        return $status?->id;
    }

    private function getStatusNameById(?int $id): ?string
    {
        if (!$id) return null;
        return Status::find($id)?->status_name;
    }
}
