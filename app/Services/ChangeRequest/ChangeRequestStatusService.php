<?php
namespace App\Services\ChangeRequest;

use App\Models\{
    Change_request_statuse, 
    NewWorkFlow, 
    Status, 
    TechnicalCr, 
    Change_request,
    User
};
use App\Http\Repository\ChangeRequest\ChangeRequestStatusRepository;
use App\Http\Controllers\Mail\MailController;
use App\Traits\ChangeRequest\ChangeRequestConstants;
use Carbon\Carbon;
use Auth;

class ChangeRequestStatusService
{
    use ChangeRequestConstants;

    protected $statusRepository;

    public function __construct()
    {
        $this->statusRepository = new ChangeRequestStatusRepository();
    }

    /**
     * Update change request status based on workflow
     *
     * @param int $id
     * @param mixed $request
     * @return bool
     */
    public function updateChangeRequestStatus($id, $request): bool
    {
        $newStatusId = $request['new_status_id'] ?? $request->new_status_id ?? null;
        $oldStatusId = $request['old_status_id'] ?? $request->old_status_id ?? null;
        $newWorkflowId = $request['new_workflow_id'] ?? null;

        /* if (!$newStatusId) {
            return false;
        } */

        $workflow = $newWorkflowId 
            ? NewWorkFlow::find($newWorkflowId)
            : NewWorkFlow::find($newStatusId);

        if (!$workflow) {
            return false;
        }

        $userId = Auth::user()->id ?? $this->resolveUserId($id, $request);

        return $this->processStatusUpdate($id, $workflow, $oldStatusId, $userId, $request);
    }

    /**
     * Update change request release status
     *
     * @param int $id
     * @param mixed $request
     * @return bool
     */
    public function updateChangeRequestReleaseStatus($id, $request): bool
    {
        // Handle assignment updates without status change
        if (!isset($request->new_status) && isset($request->assignment_user_id)) {
            Change_request_statuse::where('cr_id', $id)
                ->where('new_status_id', $request->old_status_id)
                ->where('active', '1')
                ->update(['assignment_user_id' => $request->assignment_user_id]);
        }

        $newStatusId = $request['new_status_id'] ?? $request->new_status_id ?? null;
        $oldStatusId = $request['old_status_id'] ?? $request->old_status_id ?? null;

        if (!$newStatusId || !$oldStatusId) {
            return false;
        }

        $workflow = NewWorkFlow::find($request['new_workflow_id']);
        if (!$workflow) {
            return false;
        }

        $userId = Auth::user()->id ?? ($request['assign_to'] ?? null);

        return $this->processReleaseStatusUpdate($id, $workflow, $oldStatusId, $userId, $request);
    }

    /**
     * Process the main status update logic
     *
     * @param int $id
     * @param NewWorkFlow $workflow
     * @param int $oldStatusId
     * @param int $userId
     * @param mixed $request
     * @return bool
     */
    protected function processStatusUpdate($id, $workflow, $oldStatusId, $userId, $request): bool
    {
        $countAllTechnicalTeams = $countApprovedTechnicalTeams = 0;
        $technicalCr = TechnicalCr::where("cr_id", $id)->where('status', '0')->first();

        if ($technicalCr) {
            $countAllTechnicalTeams = $technicalCr->technical_cr_team()
                ->where('current_status_id', $oldStatusId)->count();
            $countApprovedTechnicalTeams = $technicalCr->technical_cr_team()
                ->where('current_status_id', $oldStatusId)
                ->where('status', '1')->count();
        }

        $workflowActive = $workflow->workflow_type == 1 ? '0' : '2';
        $crStatus = Change_request_statuse::where('cr_id', $id)
            ->where('new_status_id', $oldStatusId)
            ->where('active', '1')
            ->first();

        if (!$crStatus) {
            return false;
        }

        $this->updateCurrentStatus($crStatus, $workflowActive, $oldStatusId, $countAllTechnicalTeams, $countApprovedTechnicalTeams);
        $this->createNewStatuses($id, $workflow, $oldStatusId, $userId, $request);
        $this->handleMailNotifications($oldStatusId, $id);

        return true;
    }

    /**
     * Process release status update
     *
     * @param int $id
     * @param NewWorkFlow $workflow
     * @param int $oldStatusId
     * @param int $userId
     * @param mixed $request
     * @return bool
     */
    protected function processReleaseStatusUpdate($id, $workflow, $oldStatusId, $userId, $request): bool
    {
        $workflowActive = $workflow->workflow_type == 1 ? '0' : '2';
        $crStatus = Change_request_statuse::where('cr_id', $id)
            ->where('new_status_id', $oldStatusId)
            ->where('active', '1')
            ->first();

        if (!$crStatus) {
            return false;
        }

        $date = Carbon::parse($crStatus->created_at);
        $now = Carbon::now();
        $diff = $date->diffInDays($now);

        $crStatus->sla_dif = $diff;
        $crStatus->active = $workflowActive;
        $crStatus->save();

        $dependStatuses = Change_request_statuse::where('cr_id', $id)
            ->where('old_status_id', $crStatus->old_status_id)
            ->where('active', '1')
            ->get();

        $active = '1';

        if ($workflowActive) { // normal workflow
            $checkDependWorkflow = NewWorkFlow::whereHas('workflowstatus', function ($q) use ($workflow) {
                $q->where('to_status_id', $workflow->workflowstatus[0]->to_status_id);
            })->pluck('from_status_id');
            
            $active = $dependStatuses->count() > 0 ? '0' : '1';
            $checkDependStatus = Change_request_statuse::where('cr_id', $id)
                ->whereIn('new_status_id', $checkDependWorkflow)
                ->where('active', '1')
                ->count();
            
            if ($checkDependStatus > 0) {
                $active = '0';
            }
        } else { // abnormal workflow
            foreach ($dependStatuses as $item) {
                Change_request_statuse::where('id', $item->id)->update(['active' => '0']);
            }
        }

        $this->createReleaseWorkflowStatuses($id, $workflow, $oldStatusId, $userId, $active);

        return true;
    }

    /**
     * Update the current status record
     *
     * @param Change_request_statuse $crStatus
     * @param string $workflowActive
     * @param int $oldStatusId
     * @param int $countAllTechnicalTeams
     * @param int $countApprovedTechnicalTeams
     * @return void
     */
    protected function updateCurrentStatus($crStatus, $workflowActive, $oldStatusId, $countAllTechnicalTeams, $countApprovedTechnicalTeams): void
    {
        $date = Carbon::parse($crStatus->created_at);
        $now = Carbon::now();
        $diff = $date->diffInDays($now);

        $crStatus->sla_dif = $diff;
        $crStatus->active = $workflowActive;

        $statusIds = $this->getStatusIds();

        // Handle special cases for production deployment
        if ($oldStatusId != $statusIds['pending_production_deployment']) {
            $crStatus->save();
        } elseif ($oldStatusId == $statusIds['pending_production_deployment'] && 
                  $countAllTechnicalTeams == $countApprovedTechnicalTeams) {
            $crStatus->save();
        }
    }

    /**
     * Create new status records based on workflow
     *
     * @param int $id
     * @param NewWorkFlow $workflow
     * @param int $oldStatusId
     * @param int $userId
     * @param mixed $request
     * @return void
     */
    protected function createNewStatuses($id, $workflow, $oldStatusId, $userId, $request): void
    {
        $dependStatuses = Change_request_statuse::where('cr_id', $id)
            ->where('old_status_id', $oldStatusId)
            ->where('active', '1')
            ->get();

        $active = '1';

        if ($workflow->workflow_type != 1) { // abnormal workflow
            foreach ($dependStatuses as $item) {
                Change_request_statuse::where('id', $item->id)->update(['active' => '0']);
            }
        } else { // normal workflow
            $checkDependWorkflow = NewWorkFlow::whereHas('workflowstatus', function ($q) use ($workflow) {
                $q->where('to_status_id', $workflow->workflowstatus[0]->to_status_id);
            })->pluck('from_status_id');

            $active = $dependStatuses->count() > 1 ? '0' : '1';
            $checkDependStatus = Change_request_statuse::where('cr_id', $id)
                ->whereIn('new_status_id', $checkDependWorkflow)
                ->where('active', '1')
                ->count();

            if ($checkDependStatus > 0) {
                $active = '0';
            }
        }

        $this->createWorkflowStatuses($id, $workflow, $oldStatusId, $userId, $active, $request);
    }

    /**
     * Create workflow status records
     *
     * @param int $id
     * @param NewWorkFlow $workflow
     * @param int $oldStatusId
     * @param int $userId
     * @param string $active
     * @param mixed $request
     * @return void
     */
    protected function createWorkflowStatuses($id, $workflow, $oldStatusId, $userId, $active, $request): void
    {
        $changeRequest = Change_request::find($id);

        foreach ($workflow->workflowstatus as $key => $item) {
            $workflowCheckActive = 0;

            // Check dependencies
            if ($item->dependency_ids) {
                $dependencyIds = $item->dependency_ids;
                $toRemove = [$item->new_workflow_id];
                $result = array_diff($dependencyIds, $toRemove);

                foreach ($result as $workflowStatus) {
                    $dependWorkflow = NewWorkFlow::find($workflowStatus);
                    $checkDependWorkflowStatus = Change_request_statuse::where('cr_id', $id)
                        ->where('new_status_id', $dependWorkflow->from_status_id)
                        ->where('old_status_id', $dependWorkflow->previous_status_id)
                        ->where('active', '2')
                        ->first();

                    if (!$checkDependWorkflowStatus) {
                        $active = '0';
                        break;
                    }
                }
            }

            if (!$workflowCheckActive) {
                $statusSla = Status::find($item->to_status_id);
                $statusSla = $statusSla ? $statusSla->sla : 0;

                $statusActive = $this->determineStatusActive($oldStatusId, $workflow, $changeRequest, $item, $request, $active);

                if ($statusActive !== '0') { // Don't create status if it should be skipped
                    $data = [
                        'cr_id' => $id,
                        'old_status_id' => $oldStatusId,
                        'new_status_id' => $item->to_status_id,
                        'user_id' => $userId,
                        'sla' => $statusSla,
                        'active' => $statusActive,
                    ];

                    $this->statusRepository->create($data);
                }
            }
        }
    }

    /**
     * Create release workflow status records
     *
     * @param int $id
     * @param NewWorkFlow $workflow
     * @param int $oldStatusId
     * @param int $userId
     * @param string $active
     * @return void
     */
    protected function createReleaseWorkflowStatuses($id, $workflow, $oldStatusId, $userId, $active): void
    {
        foreach ($workflow->workflowstatus as $key => $item) {
            $workflowCheckActive = 0;

            if ($workflow->workflow_type != 1) {
                $workflowCheckActive = Change_request_statuse::where('cr_id', $id)
                    ->where('new_status_id', $item->to_status_id)
                    ->where('active', '2')
                    ->first();
            }

            if (!$workflowCheckActive) {
                $statusSla = Status::find($item->to_status_id);
                $statusSla = $statusSla ? $statusSla->sla : 0;

                $data = [
                    'cr_id' => $id,
                    'old_status_id' => $oldStatusId,
                    'new_status_id' => $item->to_status_id,
                    'user_id' => $userId,
                    'sla' => $statusSla,
                    'active' => $active,
                ];

                $this->statusRepository->create($data);
            }
        }
    }

    /**
     * Determine if status should be active based on business rules
     *
     * @param int $oldStatusId
     * @param NewWorkFlow $workflow
     * @param Change_request $changeRequest
     * @param mixed $item
     * @param mixed $request
     * @param string $active
     * @return string
     */
    protected function determineStatusActive($oldStatusId, $workflow, $changeRequest, $item, $request, $active): string
    {
        $statusIds = $this->getStatusIds();

        if ($oldStatusId == $statusIds['pending_production_deployment']) {
            return '1';
        }

        if ($workflow->same_time == "1") {
            if ($changeRequest->design_duration == "0" && 
                $item->to_status_id == $statusIds['design_phase'] && 
                $request['old_status_id'] == $statusIds['development_ready']) {
                return '0'; // Skip this status
            }
        }

        return $active;
    }

    /**
     * Handle mail notifications for status changes
     *
     * @param int $oldStatusId
     * @param int $crId
     * @return void
     */
    protected function handleMailNotifications($oldStatusId, $crId): void
    {
        //if (!$this->getMailNotificationSettings()['status_change']) {
        if (!config('change_request.mail_notifications.status_change')) {
            return;
        }

        $statusIds = $this->getStatusIds();

        // Send mail to CrManager for specific status changes
        if ($oldStatusId == $statusIds['cr_manager_review']) {
            $mailController = new MailController();
            $mailController->notifyCrManager($crId);
        }
		//return true;
        // Add other notification rules here
        //$this->sendAdditionalNotifications($oldStatusId, $crId);
    }

    /**
     * Send additional notifications based on status
     *
     * @param int $oldStatusId
     * @param int $crId
     * @return void
     */
    protected function sendAdditionalNotifications($oldStatusId, $crId): void
    {
        $statusIds = $this->getStatusIds();
        $changeRequest = Change_request::find($crId);

        if (!$changeRequest) {
            return;
        }

        $mailController = new MailController();

        // Notify on specific status transitions
        switch ($oldStatusId) {
            case $statusIds['business_approval']:
                // Notify technical team
                if ($changeRequest->developer_id) {
                    $developer = User::find($changeRequest->developer_id);
                    if ($developer) {
                        $mailController->notifyUserStatusChange($developer->email, $crId, 'Development Ready');
                    }
                }
                break;

            case $statusIds['development_in_progress']:
                // Notify tester
                if ($changeRequest->tester_id) {
                    $tester = User::find($changeRequest->tester_id);
                    if ($tester) {
                        $mailController->notifyUserStatusChange($tester->email, $crId, 'Ready for Testing');
                    }
                }
                break;

            case $statusIds['testing_phase']:
                // Notify requester
                $mailController->notifyUserStatusChange($changeRequest->requester_email, $crId, 'Ready for UAT');
                break;
        }
    }

    /**
     * Resolve user ID for status updates
     *
     * @param int $id
     * @param mixed $request
     * @return int|null
     */
    protected function resolveUserId($id, $request)
    {
        $changeRequest = Change_request::find($id);
        if (!$changeRequest) {
            return null;
        }

        $divisionManager = $changeRequest->division_manager;
        $user = User::where('email', $divisionManager)->first();

        return $user ? $user->id : ($request['assign_to'] ?? null);
    }

    /**
     * Store change request status record
     *
     * @param int $crId
     * @param mixed $request
     * @return bool
     */
    public function storeChangeRequestStatus($crId, $request): bool
    {
        $statusSla = Status::find($request['new_status_id']);
        $statusSla = $statusSla ? $statusSla->sla : 0;

        $userId = Auth::user()->id;
        $data = [
            'cr_id' => $crId,
            'old_status_id' => $request['old_status_id'],
            'new_status_id' => $request['new_status_id'],
            'sla' => $statusSla,
            'user_id' => $userId,
            'active' => '1',
        ];

        $this->statusRepository->create($data);
        return true;
    }

    /**
     * Get status transition history for a change request
     *
     * @param int $crId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStatusHistory($crId)
    {
        return Change_request_statuse::where('cr_id', $crId)
            ->with(['status', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get current active status for a change request
     *
     * @param int $crId
     * @return Change_request_statuse|null
     */
    public function getCurrentStatus($crId)
    {
        return Change_request_statuse::where('cr_id', $crId)
            ->where('active', '1')
            ->with(['status', 'user'])
            ->first();
    }

    /**
     * Check if status transition is valid
     *
     * @param int $fromStatusId
     * @param int $toStatusId
     * @param int $workflowTypeId
     * @return bool
     */
    public function isValidStatusTransition($fromStatusId, $toStatusId, $workflowTypeId): bool
    {
        return NewWorkFlow::where('from_status_id', $fromStatusId)
            ->where('type_id', $workflowTypeId)
            ->whereHas('workflowstatus', function ($q) use ($toStatusId) {
                $q->where('to_status_id', $toStatusId);
            })
            ->exists();
    }

    /**
     * Get available status transitions for a change request
     *
     * @param int $crId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableTransitions($crId)
    {
        $changeRequest = Change_request::find($crId);
        if (!$changeRequest) {
            return collect();
        }

        $currentStatus = $this->getCurrentStatus($crId);
        if (!$currentStatus) {
            return collect();
        }

        return NewWorkFlow::where('from_status_id', $currentStatus->new_status_id)
            ->where('type_id', $changeRequest->workflow_type_id)
            ->where('active', '1')
            ->with(['workflowstatus.to_status'])
            ->get();
    }

    /**
     * Bulk update status for multiple change requests
     *
     * @param array $crIds
     * @param int $newStatusId
     * @param int $userId
     * @return array
     */
    public function bulkUpdateStatus(array $crIds, int $newStatusId, int $userId): array
    {
        $results = [];

        foreach ($crIds as $crId) {
            try {
                $currentStatus = $this->getCurrentStatus($crId);
                if (!$currentStatus) {
                    $results[$crId] = ['success' => false, 'message' => 'No current status found'];
                    continue;
                }

                $changeRequest = Change_request::find($crId);
                if (!$changeRequest) {
                    $results[$crId] = ['success' => false, 'message' => 'Change request not found'];
                    continue;
                }

                if (!$this->isValidStatusTransition($currentStatus->new_status_id, $newStatusId, $changeRequest->workflow_type_id)) {
                    $results[$crId] = ['success' => false, 'message' => 'Invalid status transition'];
                    continue;
                }

                $request = [
                    'old_status_id' => $currentStatus->new_status_id,
                    'new_status_id' => $newStatusId
                ];

                $success = $this->updateChangeRequestStatus($crId, $request);
                $results[$crId] = ['success' => $success, 'message' => $success ? 'Updated successfully' : 'Update failed'];

            } catch (\Exception $e) {
                $results[$crId] = ['success' => false, 'message' => $e->getMessage()];
            }
        }

        return $results;
    }
}