<?php

namespace App\Services\ChangeRequest;

use App\Events\ChangeRequestStatusUpdated;
use App\Events\CrDeliveredEvent;
use App\Http\Controllers\Mail\MailController;
use App\Http\Repository\ChangeRequest\ChangeRequestStatusRepository;
use App\Models\Change_request as ChangeRequest;
use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Group;
use App\Models\GroupStatuses;
use App\Models\NewWorkFlow;
use App\Models\NewWorkFlowStatuses;
use App\Models\Status;
use App\Models\TechnicalCr;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class ChangeRequestStatusService
{
    private const TECHNICAL_REVIEW_STATUS = 0;

    private const WORKFLOW_NORMAL = 1;

    private const ACTIVE_STATUS = '1';

    private const INACTIVE_STATUS = '0';

    private const COMPLETED_STATUS = '2';

    public static array $ACTIVE_STATUS_ARRAY = [self::ACTIVE_STATUS, 1];

    public static array $INACTIVE_STATUS_ARRAY = [self::INACTIVE_STATUS, 0];

    public static array $COMPLETED_STATUS_ARRAY = [self::COMPLETED_STATUS, 2];

    // flag to determine if the workflow is active or not to send email to the dev team.
    private $active_flag = '0';

    // Status IDs for dependency checking
    private static ?int $PENDING_CAB_STATUS_ID = null;

    private static ?int $DELIVERED_STATUS_ID = null;

    private static ?int $PENDING_DESIGN_STATUS_ID = null;
    private static ?int $REJECTED_STATUS_ID = null;
    // private const PENDING_CAB_STATUS_ID = 38;
    // private const DELIVERED_STATUS_ID = 27;

    private $statusRepository;

    private $mailController;

    private ?CrDependencyService $dependencyService = null;

    public function __construct()
    {
        self::$PENDING_CAB_STATUS_ID = \App\Services\StatusConfigService::getStatusId('pending_cab');
        self::$DELIVERED_STATUS_ID = \App\Services\StatusConfigService::getStatusId('Delivered');
        self::$PENDING_DESIGN_STATUS_ID = \App\Services\StatusConfigService::getStatusId('pending_design');
        self::$REJECTED_STATUS_ID = \App\Services\StatusConfigService::getStatusId('Reject');
        $this->statusRepository = new ChangeRequestStatusRepository();
        $this->mailController = new MailController();
    }

    public function updateChangeRequestStatus(int $changeRequestId, $request): bool
    {
        try {
            DB::beginTransaction();

            $statusData = $this->extractStatusData($request);
            $workflow = $this->getWorkflow($statusData);
            $changeRequest = $this->getChangeRequest($changeRequestId);
            $userId = $this->getUserId($changeRequest, $request);

            Log::info('ChangeRequestStatusService: updateChangeRequestStatus', [
                'changeRequestId' => $changeRequestId,
                'statusData' => $statusData,
                'workflow' => $workflow,
                'changeRequest' => $changeRequest,
                'userId' => $userId,
            ]);

            if (!$workflow) {
                $newStatusId = $statusData['new_status_id'] ?? 'not set';
                throw new Exception("Workflow not found for status: {$newStatusId}");
            }

            // Check if status has changed
            $statusChanged = $this->validateStatusChange($changeRequest, $statusData, $workflow);

            // If status hasn't changed, just return true without throwing an error
            if (!$statusChanged) {
                DB::commit();

                return true;
            }

            // Check for dependency hold when transitioning from Pending CAB to pending design
            if ($this->isTransitionFromPendingCab($changeRequest, $statusData)) {
                $depService = $this->getDependencyService();
                if ($depService->shouldHoldCr($changeRequestId)) {
                    // Apply dependency hold instead of transitioning
                    $depService->applyDependencyHold($changeRequestId);
                    Log::info('CR held due to unresolved dependencies', [
                        'cr_id' => $changeRequestId,
                        'cr_no' => $changeRequest->cr_no,
                    ]);
                    DB::commit();

                    return true; // Block the transition
                }
            }

            $this->processStatusUpdate($changeRequest, $statusData, $workflow, $userId, $request);

            // Fire CrDeliveredEvent if CR reached Delivered status
            //$this->checkAndFireDeliveredEvent($changeRequest, $statusData);

            DB::commit();
            // Fire CrDeliveredEvent if CR reached Delivered status
            $this->checkAndFireDeliveredEvent($changeRequest, $statusData);

            return true;

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error updating change request status', [
                'change_request_id' => $changeRequestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function validateStatusChange($changeRequest, $statusData, $workflow)
    {
        $currentStatus = $changeRequest->status;
        $newStatus = $statusData['new_status_id'] ?? null;

        // Debug log to see what values we're working with
        \Log::debug('Status change validation', [
            'currentStatus' => $currentStatus,
            'newStatus' => $newStatus,
            'statusData' => $statusData,
        ]);

        // Return false if status hasn't changed (not an error condition)
        if ($currentStatus == $newStatus) {  // Using loose comparison in case of string vs int
            return false;
        }

        // Add other validation rules here if needed
        // Throw exceptions for actual validation failures

        return true;
    }

    /**
     * Extract status data from request
     */
    private function extractStatusData($request): array
    {
        $newStatusId = $request['new_status_id'] ?? $request->new_status_id ?? null;
        $oldStatusId = $request['old_status_id'] ?? $request->old_status_id ?? null;
        $newWorkflowId = $request['new_workflow_id'] ?? null;

        if (!$newStatusId || !$oldStatusId) {
            throw new InvalidArgumentException('Missing required status IDs');
        }

        return [
            'new_status_id' => $newStatusId,
            'old_status_id' => $oldStatusId,
            'new_workflow_id' => $newWorkflowId,
        ];
    }

    /**
     * Get workflow based on status data
     */
    private function getWorkflow(array $statusData): ?NewWorkFlow
    {
        $workflowId = $statusData['new_workflow_id'] ?: $statusData['new_status_id'];

        return NewWorkFlow::find($workflowId);
    }

    /**
     * Get change request by ID
     */
    private function getChangeRequest(int $id): ChangeRequest
    {
        $changeRequest = ChangeRequest::find($id);

        if (!$changeRequest) {
            throw new Exception("Change request not found: {$id}");
        }

        return $changeRequest;
    }

    /**
     * Determine user ID for the status update
     */
    private function getUserId(ChangeRequest $changeRequest, $request): int
    {
        if (Auth::check()) {
            return Auth::id();
        }
        // Try to get user from division manager email
        if ($changeRequest->division_manager) {
            $user = User::where('email', $changeRequest->division_manager)->first();
            if ($user) {
                return $user->id;
            }
        }

        // Fallback to assigned user
        $assignedTo = $request['assign_to'] ?? null;
        if (!$assignedTo) {
            throw new Exception('Unable to determine user for status update');
        }

        return $assignedTo;
    }

    /**
     * Process the main status update logic
     */
    private function processStatusUpdate(
        ChangeRequest $changeRequest,
        array $statusData,
        NewWorkFlow $workflow,
        int $userId,
        $request
    ): void {
        $technicalTeamCounts = $this->getTechnicalTeamCounts($changeRequest->id, $statusData['old_status_id']);

        $this->updateCurrentStatus($changeRequest->id, $statusData, $workflow, $technicalTeamCounts);

        $this->createNewStatuses($changeRequest, $statusData, $workflow, $userId, $request);

        // $this->handleNotifications($statusData, $changeRequest->id, $request);
        event(new ChangeRequestStatusUpdated($changeRequest, $statusData, $request, $this->active_flag));

    }

    /**
     * Get technical team approval counts
     */
    private function getTechnicalTeamCounts(int $changeRequestId, int $oldStatusId): array
    {
        $technicalCr = TechnicalCr::where('cr_id', $changeRequestId)
            // ->where('status', self::INACTIVE_STATUS)
            ->whereRaw('CAST(status AS CHAR) = ?', ['1'])
            ->first();

        if (!$technicalCr) {
            return ['total' => 0, 'approved' => 0];
        }

        $total = $technicalCr->technical_cr_team()
            ->where('current_status_id', $oldStatusId)
            ->count();

        $approved = $technicalCr->technical_cr_team()
            ->where('current_status_id', $oldStatusId)
            // ->where('status', self::ACTIVE_STATUS)
            // ->whereIN('status',self::$ACTIVE_STATUS_ARRAY)
            ->whereRaw('CAST(status AS CHAR) = ?', ['1'])
            ->count();

        return ['total' => $total, 'approved' => $approved];
    }

    /**
     * Update the current status record
     */
    private function updateCurrentStatus(
        int $changeRequestId,
        array $statusData,
        NewWorkFlow $workflow,
        array $technicalTeamCounts
    ): void {
        if (request()->reference_status) {
            $currentStatus = ChangeRequestStatus::find(request()->reference_status);
        } else {
            $currentStatus = ChangeRequestStatus::where('cr_id', $changeRequestId)
                ->where('new_status_id', $statusData['old_status_id'])
                //->where('active', self::ACTIVE_STATUS)
                //->whereIN('active',self::$ACTIVE_STATUS_ARRAY)
                ->whereRaw('CAST(active AS CHAR) = ?', ['1'])
                ->first();

            //to check all the active statuses for this CR
            $allActiveStatuses = ChangeRequestStatus::where('cr_id', $changeRequestId)
                ->whereRaw('CAST(active AS CHAR) = ?', ['1'])
                ->get(['id', 'new_status_id', 'old_status_id', 'active']);
            Log::debug('updateCurrentStatus: All active statuses for this CR', [
                'cr_id' => $changeRequestId,
                'active_statuses' => $allActiveStatuses->toArray()
            ]);
        }

        if (!$currentStatus) {
            Log::warning('Current status not found for update', [
                'cr_id' => $changeRequestId,
                'old_status_id' => $statusData['old_status_id'],
            ]);

            return;
        }

        // the current record
        Log::debug('updateCurrentStatus: Found current status', [
            'cr_id' => $changeRequestId,
            'status_record_id' => $currentStatus->id,
            'current_active_value' => $currentStatus->active,
            'new_status_id' => $currentStatus->new_status_id
        ]);

        $workflowActive = $workflow->workflow_type == self::WORKFLOW_NORMAL
            ? self::INACTIVE_STATUS
            : self::COMPLETED_STATUS;
        $slaDifference = $this->calculateSlaDifference($currentStatus->created_at);

        $shouldUpdate = $this->shouldUpdateCurrentStatus($statusData['old_status_id'], $technicalTeamCounts);

        // Only update if conditions are met
        if ($shouldUpdate) {
            $updateResult = $currentStatus->update([
                'sla_dif' => $slaDifference,
                'active' => self::COMPLETED_STATUS
            ]);

            // to check update result
            Log::debug('updateCurrentStatus: Update executed', [
                'cr_id' => $changeRequestId,
                'status_record_id' => $currentStatus->id,
                'update_result' => $updateResult,
                'new_active_value' => self::COMPLETED_STATUS,
                'verify_after_update' => $currentStatus->fresh()->active ?? 'failed'
            ]);

            $this->handleDependentStatuses($changeRequestId, $currentStatus, $workflowActive);
        } else {
            Log::warning('updateCurrentStatus: Skipped update due to shouldUpdateCurrentStatus=false', [
                'cr_id' => $changeRequestId,
                'status_record_id' => $currentStatus->id
            ]);
        }
    }

    /**
     * Check if current status should be updated
     */
    private function shouldUpdateCurrentStatus(int $oldStatusId, array $technicalTeamCounts): bool
    {
        if ($oldStatusId != self::TECHNICAL_REVIEW_STATUS) {
            return true;
        }

        return $technicalTeamCounts['total'] > 0 &&
            $technicalTeamCounts['total'] == $technicalTeamCounts['approved'];
    }

    /**
     * Calculate SLA difference in days
     */
    private function calculateSlaDifference(string $createdAt): int
    {
        return Carbon::parse($createdAt)->diffInDays(Carbon::now());
    }

    /**
     * Handle dependent statuses based on workflow type
     */
    private function handleDependentStatuses(
        int $changeRequestId,
        ChangeRequestStatus $currentStatus,
        string $workflowActive
    ): void {
        $dependentStatuses = ChangeRequestStatus::where('cr_id', $changeRequestId)
            ->where('old_status_id', $currentStatus->old_status_id)
            // ->where('active', self::ACTIVE_STATUS)
            // ->whereIN('active',self::$ACTIVE_STATUS_ARRAY)
            ->whereRaw('CAST(active AS CHAR) = ?', ['1'])
            ->get();
        // dd($dependentStatuses,$workflowActive);
        // if ($workflowActive == self::COMPLETED_STATUS) {
        if (!$workflowActive) {
            // Abnormal workflow - deactivate all dependent statuses
            $dependentStatuses->each(function ($status) {
                $status->update(['active' => self::INACTIVE_STATUS]);
            });
        }
    }

    /**
     * Create new status records based on workflow
     */
    private function createNewStatuses(
        ChangeRequest $changeRequest,
        array $statusData,
        NewWorkFlow $workflow,
        int $userId,
        $request
    ): void {

        if (request()->reference_status) {
            $currentStatus = ChangeRequestStatus::find(request()->reference_status);
        } else {
            $currentStatus = ChangeRequestStatus::where('cr_id', $changeRequest->id)->where(
                'new_status_id',
                $statusData['old_status_id']
            )->first();
        }

        foreach ($workflow->workflowstatus as $workflowStatus) {
            if ($this->shouldSkipWorkflowStatus($changeRequest, $workflowStatus, $statusData)) {
                continue;
            }

            $active = $this->determineActiveStatus(
                $changeRequest->id,
                $workflowStatus,
                $workflow,
                $statusData['old_status_id'],
                $statusData['new_status_id'],
                $changeRequest
            );

            $newStatusRow = Status::find($workflowStatus->to_status_id);
            $oldStatusRow = Status::find($statusData['old_status_id']);

            // $previous_group_id = session('current_group') ?: auth()->user()->default_group;
            $previous_group_id = session('current_group') ?: (auth()->check() ? auth()->user()->default_group : null);
            $viewTechFlag = $newStatusRow?->view_technical_team_flag ?? false;
            if ($viewTechFlag) {
                $previous_technical_teams = [];
                if ($changeRequest && $changeRequest->technical_Cr_first) {
                    $previous_technical_teams = $changeRequest->technical_Cr_first->technical_cr_team ? $changeRequest->technical_Cr_first->technical_cr_team->pluck('group_id')->toArray() : [];
                }
                $teams = $request->technical_teams ?? $request['technical_teams'] ?? $previous_technical_teams;
                if (!empty($teams) && is_iterable($teams)) {
                    foreach ($teams as $teamGroupId) {
                        $payload = $this->buildStatusData(
                            $changeRequest->id,
                            $statusData['old_status_id'],
                            (int) $workflowStatus->to_status_id,
                            (int) $teamGroupId,
                            (int) $teamGroupId,
                            (int) $previous_group_id,
                            (int) $teamGroupId,
                            $userId,
                            $active
                        );
                        $this->statusRepository->create($payload);
                    }
                }
            } else {
                $payload = $this->buildStatusData(
                    $changeRequest->id,
                    $statusData['old_status_id'],
                    (int) $workflowStatus->to_status_id,
                    null,
                    $currentStatus->reference_group_id,
                    $previous_group_id,
                    // $newStatusRow->group_statuses->where('type', '2')->pluck('group_id')->toArray()[0],
                    optional($newStatusRow->group_statuses)
                        ->where('type', '2')
                        ->pluck('group_id')
                        ->first(),
                    $userId,
                    $active
                );
                $this->statusRepository->create($payload);
            }
        }
    }

    /**
     * Check if workflow status should be skipped
     */
    private function shouldSkipWorkflowStatus(
        ChangeRequest $changeRequest,
        $workflowStatus,
        array $statusData
    ): bool {
        // Skip design status if design duration is 0
        return $changeRequest->design_duration == '0'
            && $workflowStatus->to_status_id == 40
            && $statusData['old_status_id'] == 74;
    }

    /**
     * Determine if new status should be active
     */
    private function determineActiveStatus(
        int $changeRequestId,
        $workflowStatus,
        NewWorkFlow $workflow,
        int $oldStatusId,
        int $newStatusId,
        ChangeRequest $changeRequest
    ): string {

        $active = self::INACTIVE_STATUS;
        $cr_status = ChangeRequestStatus::where('cr_id', $changeRequestId)->where('new_status_id', $oldStatusId)
            ->whereRaw('CAST(active AS CHAR) != ?', ['0'])->latest()->first();
        // ->where('active','!=', '0')->first();

        $parkedIds = array_values(config('change_request.promo_parked_status_ids', []));

        $all_depend_statuses = ChangeRequestStatus::where('cr_id', $changeRequestId)->where('old_status_id', $cr_status->old_status_id)->whereRaw('CAST(active AS CHAR) != ?', ['0'])->whereNULL('group_id')
            ->whereHas('change_request_data', function ($query) {
                $query->where('workflow_type_id', '!=', 9);
            })->get();

        $depend_statuses = ChangeRequestStatus::where('cr_id', $changeRequestId)->where('old_status_id', $cr_status->old_status_id)->whereRaw('CAST(active AS CHAR) = ?', ['2'])->whereNULL('group_id')->whereHas('change_request_data', function ($query) {
            $query->where('workflow_type_id', '!=', 9);
        })->get();

        $depend_active_statuses = ChangeRequestStatus::where('cr_id', $changeRequestId)->where(
            'old_status_id',
            $cr_status->old_status_id
        )->whereRaw(
                'CAST(active AS CHAR) = ?',
                ['1']
            )->whereNULL('group_id')->whereHas('change_request_data', function ($query) {
                $query->where('workflow_type_id', '!=', 9);
            })->get();

        /* if ($depend_statuses->count() == $all_depend_statuses->count()) {
            foreach ($depend_statuses as $status) {

                $get_next_workflow = ChangeRequestStatus::where('cr_id', $changeRequestId)->where('old_status_id',
                    $status->new_status_id)->first();

                if ($get_next_workflow) {
                    $check_special_workflow = NewWorkFlow::where('from_status_id',
                        $get_next_workflow->old_status_id)->where('type_id',
                        $workflow->type_id)->whereHas('workflowstatus', function ($query) use ($get_next_workflow) {
                        $query->where('to_status_id', $get_next_workflow->new_status_id);
                    })->first();
                    //dd($check_special_workflow->workflow_type);
                    if ($check_special_workflow->workflow_type == 1) {
                        $get_next_workflow->update(['active' => self::ACTIVE_STATUS]);
                        $active = 0;
                        return $active;
                    }
                }

            }
        } */

        if ($changeRequest->workflow_type_id == 9) {
            $NextStatusWorkflow = NewWorkFlow::find($newStatusId);
            if (in_array($NextStatusWorkflow->workflowstatus[0]->to_status_id, $parkedIds, true)) {
                $depend_active_statuses = ChangeRequestStatus::where(
                    'cr_id',
                    $changeRequestId
                )->whereRaw('CAST(active AS CHAR) = ?', ['1'])->count();
                $active = $depend_active_statuses > 0 ? self::INACTIVE_STATUS : self::ACTIVE_STATUS;
            } else {
                $active = self::ACTIVE_STATUS;
            }

        } else {
            $active = $depend_active_statuses->count() > 0 ? self::INACTIVE_STATUS : self::ACTIVE_STATUS;
        }
        $this->active_flag = $active;

        return $active;

    }

    /**
     * Check workflow dependencies
     */
    private function checkWorkflowDependencies(int $changeRequestId, $workflowStatus): bool
    {
        if (!$workflowStatus->dependency_ids) {
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
    private function isDependencyMet(int $changeRequestId, int $workflowId): bool
    {
        $dependentWorkflow = NewWorkFlow::find($workflowId);

        if (!$dependentWorkflow) {
            return false;
        }

        return ChangeRequestStatus::where('cr_id', $changeRequestId)
            ->where('new_status_id', $dependentWorkflow->from_status_id)
            ->where('old_status_id', $dependentWorkflow->previous_status_id)
            // ->where('active', self::COMPLETED_STATUS)
            // ->whereIN('active',self::$COMPLETED_STATUS_ARRAY)
            ->whereRaw('CAST(active AS CHAR) = ?', ['2'])
            ->exists();
    }

    /**
     * Check dependent workflows for normal workflow type
     */
    private function checkDependentWorkflows(int $changeRequestId, NewWorkFlow $workflow): string
    {
        $dependentStatuses = ChangeRequestStatus::where('cr_id', $changeRequestId)
            // ->where('active', self::ACTIVE_STATUS)
            // ->whereIN('active',self::$ACTIVE_STATUS_ARRAY)
            ->whereRaw('CAST(active AS CHAR) = ?', ['1'])
            ->get();

        if ($dependentStatuses->count() > 1) {
            return self::INACTIVE_STATUS;
        }

        $checkDependentWorkflow = NewWorkFlow::whereHas('workflowstatus', function ($query) use ($workflow) {
            $query->where('to_status_id', $workflow->workflowstatus[0]->to_status_id);
        })->pluck('from_status_id');

        $dependentCount = ChangeRequestStatus::where('cr_id', $changeRequestId)
            ->whereIn('new_status_id', $checkDependentWorkflow)
            // ->where('active', self::ACTIVE_STATUS)
            // ->whereIN('active',self::$ACTIVE_STATUS_ARRAY)
            ->whereRaw('CAST(active AS CHAR) = ?', ['1'])
            ->count();

        return $dependentCount > 0 ? self::INACTIVE_STATUS : self::ACTIVE_STATUS;
    }

    /**
     * Build status data array
     */
    private function buildStatusData(
        int $changeRequestId,
        int $oldStatusId,
        int $newStatusId,
        ?int $group_id,
        ?int $reference_group_id,
        ?int $previous_group_id,
        ?int $current_group_id,
        int $userId,
        string $active
    ): array {
        $status = Status::find($newStatusId);
        $sla = $status ? (int) $status->sla : 0;

        return [
            'cr_id' => $changeRequestId,
            'old_status_id' => $oldStatusId,
            'new_status_id' => $newStatusId,
            'group_id' => $group_id,
            'reference_group_id' => $reference_group_id,
            'previous_group_id' => $previous_group_id,
            'current_group_id' => $current_group_id,
            'user_id' => $userId,
            'sla' => $sla,
            'active' => $active, // '0' | '1' | '2'
        ];
    }

    /**
     * Handle email notifications
     */
    private function handleNotifications(array $statusData, int $changeRequestId, $request): void
    {
        // dd($request->all());
        // Notify CR Manager when status changes from 99 to 101
        if (
            $statusData['old_status_id'] == 99 &&
            $this->hasStatusTransition($changeRequestId, 101)
        ) {

            try {
                $this->mailController->notifyCrManager($changeRequestId);
            } catch (Exception $e) {
                Log::error('Failed to send CR Manager notification', [
                    'change_request_id' => $changeRequestId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        /*
        // Notify Dev Team When status changes to Technical Estimation or Pending Implementation or Technical Implementation
        $assigned_user_id = null;
        if(isset($request->assignment_user_id)){
             $assigned_user_id = $request->assignment_user_id;
        }
        $devTeamStatuses = [config('change_request.status_ids.technical_estimation'),config('change_request.status_ids.pending_implementation'),config('change_request.status_ids.technical_implementation')];
        $newStatusId = NewWorkFlowStatuses::where('new_workflow_id', $statusData['new_status_id'])->get()->pluck('to_status_id')->toArray();
        //dd($newStatusId);
        if (array_intersect($devTeamStatuses, $newStatusId) && $this->active_flag == '1') {
            try {
                 $this->mailController->notifyDevTeam($changeRequestId , $statusData['old_status_id'] , $newStatusId, $assigned_user_id);
             } catch (\Exception $e) {
                 Log::error('Failed to send Dev Team notification', [
                     'change_request_id' => $changeRequestId,
                     'error' => $e->getMessage()
                 ]);
             }
        }
        */

        // Notify group when status changes.
        // dd($request->all(), $statusData);
        $newStatusId = NewWorkFlowStatuses::where(
            'new_workflow_id',
            $request->new_status_id
        )->get()->pluck('to_status_id')->toArray();
        // dd($newStatusId);
        $userToNotify = [];
        if (in_array(\App\Services\StatusConfigService::getStatusId('pending_cd_analysis'), $newStatusId)) {
            if (!empty($request->cr_member)) {
                $userToNotify = [$request->cr_member];
            }
        }

        $cr = ChangeRequest::find($changeRequestId);
        $targetStatus = Status::with('group_statuses')->whereIn('id', $newStatusId)->first();
        // $group_id = $targetStatus->group_statuses->first()->group_id ?? null;
        $viewGroup = GroupStatuses::where('status_id', $targetStatus->id)->where(
            'type',
            '2'
        )->pluck('group_id')->toArray();
        $group_id = $cr->application->group_applications->first()->group_id ?? null;
        // will check if group_id is in viewGroup then we will send the notification to this group is only
        // dd($group_id,$viewGroup);
        $groupToNotify = [];
        if (in_array($group_id, $viewGroup)) {
            $recieveNotification = Group::where('id', $group_id)->where('recieve_notification', '1')->first();
            if ($recieveNotification) {
                $groupToNotify = [$group_id];
            } else {
                $groupToNotify = [];
            }
        } else {
            $groupToNotify = Group::whereIn('id', $viewGroup)
                ->where('recieve_notification', '1')
                ->pluck('id')
                ->toArray();
        }
        // dd($groupToNotify);

        if ($this->active_flag == '1' && !empty($groupToNotify)) {
            foreach ($groupToNotify as $groupId) {
                try {
                    $this->mailController->notifyGroup(
                        $changeRequestId,
                        $statusData['old_status_id'],
                        $newStatusId,
                        $groupId,
                        $userToNotify
                    );
                } catch (Exception $e) {
                    Log::error('Failed to send Group notification', [
                        'change_request_id' => $changeRequestId,
                        'group_id' => $groupId,
                        'error' => $e->getMessage(),
                    ]);

                    continue;
                }
            }
        }
    }

    /**
     * Check if status transition exists
     */
    private function hasStatusTransition(int $changeRequestId, int $toStatusId): bool
    {
        return ChangeRequestStatus::where('cr_id', $changeRequestId)
            ->where('new_status_id', $toStatusId)
            ->exists();
    }

    // Get the dependency service (lazy loaded)
    private function getDependencyService(): CrDependencyService
    {
        if (!$this->dependencyService) {
            $this->dependencyService = new CrDependencyService();
        }

        return $this->dependencyService;
    }

    // Check if this is a transition from Pending CAB status to pending design status workflow 160
    private function isTransitionFromPendingCab(ChangeRequest $changeRequest, array $statusData): bool
    {
        if (self::$PENDING_CAB_STATUS_ID === null) {
            return false;
        }

        $workflow = NewWorkFlow::where('from_status_id', self::$PENDING_CAB_STATUS_ID)
            ->where('type_id', $changeRequest->workflow_type_id)
            ->where('workflow_type', '0') // Normal workflow (not reject)
            ->whereRaw('CAST(active AS CHAR) = ?', ['1'])
            ->first();

        if (!$workflow) {
            return false;
        }

        /*return isset($statusData['old_status_id']) &&
               (int)$statusData['old_status_id'] === self::$PENDING_CAB_STATUS_ID;*/
        return isset($statusData['new_status_id']) &&
            (int) $statusData['new_status_id'] === $workflow->id;
    }

    // Check if CR has reached Delivered status and fire event
    private function checkAndFireDeliveredEvent(ChangeRequest $changeRequest, array $statusData): void
    {
        $newWorkflowId = $statusData['new_status_id'] ?? null;
        if (!$newWorkflowId) {
            return; // no workflow do nothing
        }
        Log::info('Checking for delivered event', [
            'change_request_id' => $changeRequest->id,
            'new_workflow_id' => $newWorkflowId,
        ]);

        $workflow = NewWorkFlow::with('workflowstatus')->find($newWorkflowId);
        if (!$workflow) {
            return; // no workflow do nothing
        }

        foreach ($workflow->workflowstatus as $wfStatus) {
            if (in_array((int) $wfStatus->to_status_id, [self::$DELIVERED_STATUS_ID, self::$REJECTED_STATUS_ID], true)) {
                // Refresh the CR to ensure we have the latest data
                $changeRequest->refresh();

                Log::info('Firing CrDeliveredEvent', [
                    'cr_id' => $changeRequest->id,
                    'cr_no' => $changeRequest->cr_no,
                ]);
                // the status delivered fire the event
                event(new CrDeliveredEvent($changeRequest));

                return;
            }
        }
    }
}
