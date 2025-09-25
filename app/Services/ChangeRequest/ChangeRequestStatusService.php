<?php

namespace App\Services\ChangeRequest;

use App\Models\Change_request as ChangeRequest;
use App\Models\Group;
use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\NewWorkFlow;
use App\Models\TechnicalCr;
use App\Models\Status;
use App\Models\NewWorkFlowStatuses;
use App\Models\User;
use App\Http\Controllers\Mail\MailController;
use App\Http\Repository\ChangeRequest\ChangeRequestStatusRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ChangeRequestStatusService
{
    private const TECHNICAL_REVIEW_STATUS = 127;
    private const WORKFLOW_NORMAL = 1;
    private const ACTIVE_STATUS = '1';
    private const INACTIVE_STATUS = '0';
    private const COMPLETED_STATUS = '2';

    // flag to determine if the workflow is active or not to send email to the dev team.
    private $active_flag = '0';

    private $statusRepository;
    private $mailController;

    public function __construct() {
        $this->statusRepository = new ChangeRequestStatusRepository();
        $this->mailController = new MailController();
    }

    /**
     * Update change request status with proper workflow validation
     *
     * @param int $changeRequestId
     * @param array|object $request
     * @return bool
     * @throws \Exception
     */
    public function updateChangeRequestStatus(int $changeRequestId, $request): bool
    {
        try {
            DB::beginTransaction();

            $statusData = $this->extractStatusData($request);
            $workflow = $this->getWorkflow($statusData);
            $changeRequest = $this->getChangeRequest($changeRequestId);
            $userId = $this->getUserId($changeRequest, $request);

            if (!$workflow) {
                throw new \Exception("Workflow not found for status: {$statusData['new_status_id']}");
            }

            $this->processStatusUpdate($changeRequest, $statusData, $workflow, $userId, $request);
            
            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating change request status', [
                'change_request_id' => $changeRequestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
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
            throw new \InvalidArgumentException('Missing required status IDs');
        }

        return [
            'new_status_id' => $newStatusId,
            'old_status_id' => $oldStatusId,
            'new_workflow_id' => $newWorkflowId
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
            throw new \Exception("Change request not found: {$id}");
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
            throw new \Exception('Unable to determine user for status update');
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
        
        $this->handleNotifications($statusData, $changeRequest->id, $request);
    }

    /**
     * Get technical team approval counts
     */
    private function getTechnicalTeamCounts(int $changeRequestId, int $oldStatusId): array
    {
        $technicalCr = TechnicalCr::where('cr_id', $changeRequestId)
            ->where('status', self::INACTIVE_STATUS)
            ->first();

        if (!$technicalCr) {
            return ['total' => 0, 'approved' => 0];
        }

        $total = $technicalCr->technical_cr_team()
            ->where('current_status_id', $oldStatusId)
            ->count();

        $approved = $technicalCr->technical_cr_team()
            ->where('current_status_id', $oldStatusId)
            ->where('status', self::ACTIVE_STATUS)
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
        $currentStatus = ChangeRequestStatus::where('cr_id', $changeRequestId)
            ->where('new_status_id', $statusData['old_status_id'])
            ->where('active', self::ACTIVE_STATUS)
            ->first();

        if (!$currentStatus) {
            Log::warning('Current status not found for update', [
                'cr_id' => $changeRequestId,
                'old_status_id' => $statusData['old_status_id']
            ]);
            return;
        }

        $workflowActive = $workflow->workflow_type == self::WORKFLOW_NORMAL 
            ? self::INACTIVE_STATUS 
            : self::COMPLETED_STATUS;

        $slaDifference = $this->calculateSlaDifference($currentStatus->created_at);

        // Only update if conditions are met
        if ($this->shouldUpdateCurrentStatus($statusData['old_status_id'], $technicalTeamCounts)) {
            $currentStatus->update([
                'sla_dif' => $slaDifference,
                'active' => $workflowActive
            ]);

            $this->handleDependentStatuses($changeRequestId, $currentStatus, $workflowActive);
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
            ->where('active', self::ACTIVE_STATUS)
            ->get();
		//dd($dependentStatuses,$workflowActive);
        //if ($workflowActive == self::COMPLETED_STATUS) {
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
        foreach ($workflow->workflowstatus as $workflowStatus) {
            if ($this->shouldSkipWorkflowStatus($changeRequest, $workflowStatus, $statusData)) {
                continue;
            }
            //dd($statusData);
            $active = $this->determineActiveStatus(
                $changeRequest->id, 
                $workflowStatus, 
                $workflow, 
                $statusData['old_status_id']
            );

            $statusData = $this->buildStatusData(
                $changeRequest->id,
                $statusData['old_status_id'],
                $workflowStatus->to_status_id,
                $userId,
                $active
            );

            $this->statusRepository->create($statusData);
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
        return $changeRequest->design_duration == "0" 
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
        int $oldStatusId
    ): string {
		
		$active = self::INACTIVE_STATUS;
		$cr_status = ChangeRequestStatus::where('cr_id', $changeRequestId)->where('new_status_id',  $oldStatusId)->where('active','!=', 0)->first();
		//dd($cr_status);
		$all_depend_statuses = ChangeRequestStatus::where('cr_id', $changeRequestId)->where('old_status_id', $cr_status->old_status_id)->get();
		$depend_statuses = ChangeRequestStatus::where('cr_id', $changeRequestId)->where('old_status_id', $cr_status->old_status_id)->where('active','2')->get();
		
		$depend_active_statuses = ChangeRequestStatus::where('cr_id', $changeRequestId)->where('old_status_id', $cr_status->old_status_id)->where('active', '1')->get();
        //dd($cr_status,$all_depend_statuses->toArray(),$depend_active_statuses->toArray());
		if($depend_statuses->count() == $all_depend_statuses->count())
		{
			foreach($depend_statuses as $status)
			{
				
				$get_next_workflow = ChangeRequestStatus::where('cr_id', $changeRequestId)->where('old_status_id', $status->new_status_id)->first();
				if($get_next_workflow)
				{
					$check_special_workflow = NewWorkFlow::where('from_status_id', $get_next_workflow->old_status_id)->where('type_id',$workflow->type_id)->whereHas('workflowstatus', function ($query) use ($get_next_workflow) {
						$query->where('to_status_id', $get_next_workflow->new_status_id);
					})->first();
					 if($check_special_workflow->workflow_type == 1)
					{
						$get_next_workflow->update(['active' => self::ACTIVE_STATUS]);
						$active = 0;
						return $active;
					} 
				}
				
			}
		}
		$active = $depend_active_statuses->count() > 0 ? self::INACTIVE_STATUS : self::ACTIVE_STATUS;
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
            ->where('active', self::COMPLETED_STATUS)
            ->exists();
    }

    /**
     * Check dependent workflows for normal workflow type
     */
    private function checkDependentWorkflows(int $changeRequestId, NewWorkFlow $workflow): string
    {
        $dependentStatuses = ChangeRequestStatus::where('cr_id', $changeRequestId)
            ->where('active', self::ACTIVE_STATUS)
            ->get();

        if ($dependentStatuses->count() > 1) {
            return self::INACTIVE_STATUS;
        }

        $checkDependentWorkflow = NewWorkFlow::whereHas('workflowstatus', function ($query) use ($workflow) {
            $query->where('to_status_id', $workflow->workflowstatus[0]->to_status_id);
        })->pluck('from_status_id');

        $dependentCount = ChangeRequestStatus::where('cr_id', $changeRequestId)
            ->whereIn('new_status_id', $checkDependentWorkflow)
            ->where('active', self::ACTIVE_STATUS)
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
        int $userId,
        string $active
    ): array {
        $status = Status::find($newStatusId);
        $sla = $status ? $status->sla : 0;

        return [
            'cr_id' => $changeRequestId,
            'old_status_id' => $oldStatusId,
            'new_status_id' => $newStatusId,
            'user_id' => $userId,
            'sla' => $sla,
            'active' => $active,
        ];
    }

    /**
     * Handle email notifications
     */
    private function handleNotifications(array $statusData, int $changeRequestId, $request): void
    {
        //dd($request->all());
        // Notify CR Manager when status changes from 99 to 101
        if ($statusData['old_status_id'] == 99 && 
            $this->hasStatusTransition($changeRequestId, 101)) {
            
            try {
                $this->mailController->notifyCrManager($changeRequestId);
            } catch (\Exception $e) {
                Log::error('Failed to send CR Manager notification', [
                    'change_request_id' => $changeRequestId,
                    'error' => $e->getMessage()
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
       
       $newStatusId = NewWorkFlowStatuses::where('new_workflow_id', $statusData['new_status_id'])->get()->pluck('to_status_id')->toArray();
       $cr = ChangeRequest::find($changeRequestId);
       $group_id = $cr->application->group_applications->first()->group_id;
       $recieve_notification = Group::where('id', $group_id)->value('recieve_notification');
       
       if($this->active_flag == '1' && $recieve_notification == '1'){       
           try {
                $this->mailController->notifyGroup($changeRequestId , $statusData['old_status_id'] , $newStatusId);
            } catch (\Exception $e) {
                Log::error('Failed to send Group notification', [
                    'change_request_id' => $changeRequestId,
                    'error' => $e->getMessage()
                ]);
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

    
}
