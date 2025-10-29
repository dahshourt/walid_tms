<?php
namespace App\Services\ChangeRequest;

use App\Models\{
    Change_request, 
    User, 
    TechnicalCr, 
    TechnicalCrTeam, 
    NewWorkFlow, 
    Status
};
use App\Models\Change_request_statuse as ChangeRequestStatus;
use Carbon\Carbon;
use App\Traits\ChangeRequest\ChangeRequestConstants;
use App\Services\ChangeRequest\{
    ChangeRequestUpdateService
};
use Auth;
use App\Http\Repository\ChangeRequest\ChangeRequestStatusRepository;
use Illuminate\Support\Facades\Log;

class ChangeRequestValidationService
{
    use ChangeRequestConstants;
	private const ACTIVE_STATUS = '1';
    private const INACTIVE_STATUS = '0';
    private const COMPLETED_STATUS = '2';
	
	public static array $ACTIVE_STATUS_ARRAY = [self::ACTIVE_STATUS,1];
	public static array $INACTIVE_STATUS_ARRAY = [self::INACTIVE_STATUS,0];
    public static array $COMPLETED_STATUS_ARRAY = [self::COMPLETED_STATUS,2];
    
    /**
     * Handle technical team validation workflow
     *
     * @param int $id
     * @param mixed $request
     * @return bool
     */
    public function handleTechnicalTeamValidation($id, $request): bool
    {
		
        $newStatusId = $request->new_status_id ?? null;
        $oldStatusId = $request->old_status_id ?? null;

        if (!$newStatusId || !$oldStatusId) {
            return false;
        }

        $workflow = NewWorkFlow::find($newStatusId);
        $oldStatusData = Status::find($oldStatusId);

        if (!$oldStatusData || !$oldStatusData->view_technical_team_flag) {
            return false;
        }

        $technicalDefaultGroup = session('default_group') ?: auth()->user()->default_group;
        $cr = Change_request::find($id);
        $technicalCr = TechnicalCr::where("cr_id", $id)->whereRaw('CAST(status AS CHAR) = ?', ['0'])->first();

        if (!$technicalCr) {
            return false;
        }
        $updateService = new ChangeRequestUpdateService();
		$updateService->mirrorCrStatusToTechStreams($id, (int) $workflow->workflowstatus[0]->to_status_id, null, 'actor');
        return $this->processTechnicalTeamStatus($technicalCr, $oldStatusData, $workflow, $technicalDefaultGroup, $request);
    }

    /**
     * Process technical team status based on workflow
     *
     * @param TechnicalCr $technicalCr
     * @param Status $oldStatusData
     * @param NewWorkFlow $workflow
     * @param int $group
     * @param mixed $request
     * @return bool
     */
    protected function processTechnicalTeamStatus($technicalCr, $oldStatusData, $workflow, $group, $request): bool
    {
        $statusIds = $this->getStatusIds();
        $toStatusData = NewWorkFlow::find($request->new_status_id);
        $parkedIds = array_values(config('change_request.parked_status_ids', []));
        $promo_unparked_ids = array_values(config('change_request.promo_unparked_ids', []));
        
        $this->updateCurrentStatusByGroup($technicalCr->cr_id,$oldStatusData->toArray(),$group);
        
        
        if (in_array($toStatusData->workflowstatus[0]->to_status->id, $parkedIds, true)) {
            $checkWorkflowType = NewWorkFlow::find($request->new_status_id)->workflow_type;
            
            if ($checkWorkflowType) { // reject
                $technicalCr->status = '2';
                $technicalCr->save();
                $technicalCr->technical_cr_team()->where('group_id', $group)->update(['status' => '2']);
                foreach($technicalCr->technical_cr_team->pluck('group_id')->toArray() as $key=>$groupId)
                {
                    //$this->updateCurrentStatusByGroup($technicalCr->cr_id,$oldStatusData->toArray(),$groupId);
					if($groupId != $group) $this->updateCurrentStatusByGroup($technicalCr->cr_id,$oldStatusData->toArray(),$groupId);
                }
            } else { // approve
                $technicalCr->technical_cr_team()->where('group_id', $group)->update(['status' => '1']);
    
                $countAllTeams = $technicalCr->technical_cr_team->count();
                $countApprovedTeams = $technicalCr->technical_cr_team()->whereRaw('CAST(status AS CHAR) = ?', ['1'])->count();
    
                if ($countAllTeams > $countApprovedTeams) {
                    return true; // Still waiting for other teams
                } else {
                    $technicalCr->status = '1';
                    $technicalCr->save();
                }
            }
    
            return false;
        }
        else
        {
			
            // handle if next status is also technical flag
            if($toStatusData->workflowstatus[0]->to_status->view_technical_team_flag)
            {
				$technicalCr->technical_cr_team()->where('group_id', $group)->update(['status' => '1']);
            
                TechnicalCrTeam::create([
                    'group_id' => $group,
                    'technical_cr_id' => $technicalCr->id,
                    'current_status_id' => $workflow->workflowstatus[0]->to_status_id,
                    'status' => "0",
                ]);
                $newStatusRow = Status::find($workflow->workflowstatus[0]->to_status_id);
                $previous_group_id = session('current_group') ?: auth()->user()->default_group;
				
                $payload = $this->buildStatusData(
                    $technicalCr->cr_id,
                    $request->old_status_id,
                    (int) $workflow->workflowstatus[0]->to_status_id,
                    $group,
                    (int) $group,
                    (int) $previous_group_id,
                    //(int) $newStatusRow->group_statuses->where('type','2')->pluck('group_id')->toArray()[0],
                    (int) $group,
                    Auth::id(),
                    '1'
                );
                $statusRepository = new ChangeRequestStatusRepository();
                $statusRepository->create($payload);
                return true;
            }
            else // no need to wait other teams
            {
				
                $checkWorkflowType = NewWorkFlow::find($request->new_status_id)->workflow_type;
				if ($checkWorkflowType) { // reject
					$technicalCr->status = '2';
                    $technicalCr->save();
                    $technicalCr->technical_cr_team()->where('group_id', $group)->update(['status' => '2']);
                    foreach($technicalCr->technical_cr_team->pluck('group_id')->toArray() as $key=>$groupId)
                    {
						if($groupId != $group) $this->updateCurrentStatusByGroup($technicalCr->cr_id,$oldStatusData->toArray(),$groupId);
                    }
                }
                else
                {
					
					if (in_array($toStatusData->workflowstatus[0]->to_status->id, $promo_unparked_ids, true)) 
					{
						$technicalCr->technical_cr_team()->where('group_id', $group)->update(['status' => '1']);
						$newStatusRow = Status::find($workflow->workflowstatus[0]->to_status_id);
						$previous_group_id = session('current_group') ?: auth()->user()->default_group;
						$payload = $this->buildStatusData(
							$technicalCr->cr_id,
							$request->old_status_id,
							(int) $workflow->workflowstatus[0]->to_status_id,
							NULL,
							(int) $group,
							(int) $previous_group_id,
							(int) $newStatusRow->group_statuses->where('type','2')->pluck('group_id')->toArray()[0],
							Auth::id(),
							'1'
						);
						$statusRepository = new ChangeRequestStatusRepository();
						$statusRepository->create($payload);
						return true;
					}
					else
					{
						$technicalCr->technical_cr_team()->where('group_id', $group)->update(['status' => '1']);
						$countAllTeams = $technicalCr->technical_cr_team->count();
						$countApprovedTeams = $technicalCr->technical_cr_team()->whereRaw('CAST(status AS CHAR) = ?', ['1'])->count();
						if ($countAllTeams == $countApprovedTeams) {
							$technicalCr->status = '1';
							$technicalCr->save();
						}	
					}
                    
                }
                
                return false;
            }
        }
        // Handle pending production deployment case
        if ($oldStatusData->id == $statusIds['pending_production_deployment']) {
            $technicalCr->technical_cr_team()->where('group_id', $group)->update(['status' => '1']);
            
            TechnicalCrTeam::create([
                'group_id' => $group,
                'technical_cr_id' => $technicalCr->id,
                'current_status_id' => $workflow->workflowstatus[0]->to_status_id,
                'status' => "0",
            ]);

           

            
            return true;
        }

        // Handle production deployment case
        if ($oldStatusData->id == $statusIds['production_deployment']) {
            $technicalCr->technical_cr_team()->where('group_id', $group)->update(['status' => '1']);
            
            $countAllTeams = $technicalCr->technical_cr_team->count();
            $countApprovedTeams = $technicalCr->technical_cr_team()->whereRaw('CAST(status AS CHAR) = ?', ['1'])->count();
            
            if ($countAllTeams == $countApprovedTeams) {
                $technicalCr->status = '1';
                $technicalCr->save();
            }
            
            return true;
        }

        // Handle approve/reject workflow
        $checkWorkflowType = NewWorkFlow::find($request->new_status_id)->workflow_type;

        if ($checkWorkflowType) { // reject
            $technicalCr->status = '2';
            $technicalCr->save();
            $technicalCr->technical_cr_team()->where('group_id', $group)->update(['status' => '2']);
            foreach($technicalCr->technical_cr_team->pluck('group_id')->toArray() as $key=>$groupId)
            {
				if($groupId != $group) $this->updateCurrentStatusByGroup($technicalCr->cr_id,$oldStatusData->toArray(),$groupId);
				//$this->updateCurrentStatusByGroup($technicalCr->cr_id,$oldStatusData->toArray(),$groupId);
            }
        } else { // approve
            $technicalCr->technical_cr_team()->where('group_id', $group)->update(['status' => '1']);

            $countAllTeams = $technicalCr->technical_cr_team->count();
            $countApprovedTeams = $technicalCr->technical_cr_team()->whereRaw('CAST(status AS CHAR) = ?', ['1'])->count();

            if ($countAllTeams > $countApprovedTeams) {
                return true; // Still waiting for other teams
            } else {
                $technicalCr->status = '1';
                $technicalCr->save();
            }
        }

        return false;
    }


    /**
     * Update the current status record
     */
    private function updateCurrentStatusByGroup(int $changeRequestId, array $statusData , int $groupId ): void {
        $currentStatus = ChangeRequestStatus::where('cr_id', $changeRequestId)
            ->where('new_status_id', $statusData['id'])
            ->where('group_id', $groupId)
            //->where('active','1')
			//->whereIN('active',self::$ACTIVE_STATUS_ARRAY)
			->whereRaw('CAST(active AS CHAR) = ?', ['1'])
            ->first();

        if (!$currentStatus) {
            Log::warning('Current status not found for update', [
                'cr_id' => $changeRequestId,
                'old_status_id' => $statusData['old_status_id']
            ]);
            return;
        }

        $workflowActive = '2';

        $slaDifference = $this->calculateSlaDifference($currentStatus->created_at);
        // Only update if conditions are met
            $currentStatus->update([
                'sla_dif' => $slaDifference,
                'active' => $workflowActive
            ]);

    }
	
	
    /**
     * Calculate SLA difference in days
     */
    private function calculateSlaDifference(string $createdAt): int
    {
        return Carbon::parse($createdAt)->diffInDays(Carbon::now());
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
        $sla    = $status ? (int) $status->sla : 0;

        return [
            'cr_id'                     => $changeRequestId,
            'old_status_id'             => $oldStatusId,
            'new_status_id'             => $newStatusId,
            'group_id'                  => $group_id,
            'reference_group_id'        => $reference_group_id,
            'previous_group_id'         => $previous_group_id,
            'current_group_id'          => $current_group_id,
            'user_id'                   => $userId,
            'sla'                       => $sla,
            'active'                    => $active, // '0' | '1' | '2'
        ];
    }



    /**
     * Validate user permissions for change request operations
     *
     * @param int $crId
     * @param int $userId
     * @param string $operation
     * @return bool
     */
    public function validateUserPermissions($crId, $userId, $operation = 'update'): bool
    {
        $cr = Change_request::find($crId);
        $user = User::find($userId);

        if (!$cr || !$user) {
            return false;
        }

        switch ($operation) {
            case 'create':
                return $this->canCreateChangeRequest($user);
            case 'update':
                return $this->canUpdateChangeRequest($cr, $user);
            case 'delete':
                return $this->canDeleteChangeRequest($cr, $user);
            case 'assign':
                return $this->canAssignChangeRequest($cr, $user);
            case 'approve':
                return $this->canApproveChangeRequest($cr, $user);
            case 'reject':
                return $this->canRejectChangeRequest($cr, $user);
            default:
                return false;
        }
    }

    /**
     * Check if user can create change requests
     *
     * @param User $user
     * @return bool
     */
    protected function canCreateChangeRequest(User $user): bool
    {
        // Basic permission check - all authenticated users can create CRs
        return true;
    }

    /**
     * Check if user can update a change request
     *
     * @param Change_request $cr
     * @param User $user
     * @return bool
     */
    protected function canUpdateChangeRequest(Change_request $cr, User $user): bool
    {
        // Requester can always update their own CR
        if ($cr->requester_id == $user->id) {
            return true;
        }

        // Division manager can update CRs under their division
        if (strtolower($cr->division_manager) === strtolower($user->email)) {
            return true;
        }

        // Assigned users can update the CR
        if (in_array($user->id, [$cr->developer_id, $cr->tester_id, $cr->designer_id])) {
            return true;
        }

        // Users with admin privileges
        $adminGroups = [
            $this->getGroupIds()['admin'],
            $this->getGroupIds()['management']
        ];

        $userGroups = $user->user_groups->pluck('group_id')->toArray();
        
        return !empty(array_intersect($adminGroups, $userGroups));
    }

    /**
     * Check if user can delete a change request
     *
     * @param Change_request $cr
     * @param User $user
     * @return bool
     */
    protected function canDeleteChangeRequest(Change_request $cr, User $user): bool
    {
        // Only requester and admins can delete
        if ($cr->requester_id == $user->id) {
            return true;
        }

        $adminGroups = [$this->getGroupIds()['admin']];
        $userGroups = $user->user_groups->pluck('group_id')->toArray();
        
        return !empty(array_intersect($adminGroups, $userGroups));
    }

    /**
     * Check if user can assign change requests
     *
     * @param Change_request $cr
     * @param User $user
     * @return bool
     */
    protected function canAssignChangeRequest(Change_request $cr, User $user): bool
    {
        // Team leads and managers can assign
        $managerGroups = [
            $this->getGroupIds()['admin'],
            $this->getGroupIds()['management']
        ];

        $userGroups = $user->user_groups->pluck('group_id')->toArray();
        
        return !empty(array_intersect($managerGroups, $userGroups));
    }

    /**
     * Check if user can approve change requests
     *
     * @param Change_request $cr
     * @param User $user
     * @return bool
     */
    protected function canApproveChangeRequest(Change_request $cr, User $user): bool
    {
        // Division manager can approve business requirements
        if (strtolower($cr->division_manager) === strtolower($user->email)) {
            return true;
        }

        // Technical team members can approve technical aspects
        $technicalGroups = [
            $this->getGroupIds()['technical_team'],
            $this->getGroupIds()['admin']
        ];

        $userGroups = $user->user_groups->pluck('group_id')->toArray();
        
        return !empty(array_intersect($technicalGroups, $userGroups));
    }

    /**
     * Check if user can reject change requests
     *
     * @param Change_request $cr
     * @param User $user
     * @return bool
     */
    protected function canRejectChangeRequest(Change_request $cr, User $user): bool
    {
        // Same permissions as approve
        return $this->canApproveChangeRequest($cr, $user);
    }

    /**
     * Validate workflow transition
     *
     * @param int $fromStatus
     * @param int $toStatus
     * @param int $workflowType
     * @return bool
     */
    public function validateWorkflowTransition($fromStatus, $toStatus, $workflowType): bool
    {
        $workflow = NewWorkFlow::where('from_status_id', $fromStatus)
            ->where('type_id', $workflowType)
            ->whereHas('workflowstatus', function ($q) use ($toStatus) {
                $q->where('to_status_id', $toStatus);
            })
            ->exists();

        return $workflow;
    }

    /**
     * Validate change request data before creation/update
     *
     * @param array $data
     * @param int|null $crId
     * @return array
     */
    public function validateChangeRequestData(array $data, $crId = null): array
    {
        $errors = [];

        // Validate required fields
        $requiredFields = ['title', 'description', 'priority_id', 'category_id', 'application_id', 'workflow_type_id'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "The {$field} field is required.";
            }
        }

        // Validate field formats and constraints
        $errors = array_merge($errors, $this->validateFieldConstraints($data));

        // Validate business rules
        $errors = array_merge($errors, $this->validateBusinessRules($data, $crId));

        return $errors;
    }

    /**
     * Validate field constraints
     *
     * @param array $data
     * @return array
     */
    protected function validateFieldConstraints(array $data): array
    {
        $errors = [];
        $validationRules = $this->getValidationRules();

        // Validate title
        if (isset($data['title'])) {
            if (strlen($data['title']) < 10) {
                $errors['title'] = 'Title must be at least 10 characters long.';
            }
            if (strlen($data['title']) > 255) {
                $errors['title'] = 'Title must not exceed 255 characters.';
            }
        }

        // Validate description
        if (isset($data['description'])) {
            if (strlen($data['description']) < 20) {
                $errors['description'] = 'Description must be at least 20 characters long.';
            }
        }

        // Validate workflow type
        if (isset($data['workflow_type_id'])) {
            if (!$this->isValidWorkflowType($data['workflow_type_id'])) {
                $errors['workflow_type_id'] = 'Invalid workflow type selected.';
            }
        }

        // Validate estimations
        $estimationFields = ['design_duration', 'develop_duration', 'test_duration', 'CR_duration'];
        foreach ($estimationFields as $field) {
            if (isset($data[$field]) && $data[$field] !== null) {
                if (!is_numeric($data[$field]) || $data[$field] < 0) {
                    $errors[$field] = "The {$field} must be a positive number.";
                }
                if ($data[$field] > 2000) {
                    $errors[$field] = "The {$field} cannot exceed 2000 hours.";
                }
            }
        }

        // Validate email format for division manager
        if (isset($data['division_manager']) && !filter_var($data['division_manager'], FILTER_VALIDATE_EMAIL)) {
            $errors['division_manager'] = 'Division manager must be a valid email address.';
        }

        // Validate mobile number format
        if (isset($data['creator_mobile_number']) && !empty($data['creator_mobile_number'])) {
            if (!preg_match('/^[0-9\-\+\s\(\)]+$/', $data['creator_mobile_number'])) {
                $errors['creator_mobile_number'] = 'Invalid mobile number format.';
            }
        }

        return $errors;
    }

    /**
     * Validate business rules
     *
     * @param array $data
     * @param int|null $crId
     * @return array
     */
    protected function validateBusinessRules(array $data, $crId = null): array
    {
        $errors = [];

        // Check for duplicate titles in the same application (optional business rule)
        if (isset($data['title']) && isset($data['application_id'])) {
            $query = Change_request::where('title', $data['title'])
                ->where('application_id', $data['application_id']);

            if ($crId) {
                $query->where('id', '!=', $crId);
            }

            if ($query->exists()) {
                $errors['title'] = 'A change request with this title already exists for this application.';
            }
        }

        // Validate user assignments exist
        $userFields = ['developer_id', 'tester_id', 'designer_id'];
        foreach ($userFields as $field) {
            if (isset($data[$field]) && $data[$field]) {
                if (!User::where('id', $data[$field])->where('active', 1)->exists()) {
                    $errors[$field] = 'Selected user does not exist or is inactive.';
                }
            }
        }

        // Validate dependency chain (prevent circular dependencies)
        if (isset($data['depend_cr_id']) && $data['depend_cr_id'] && $crId) {
            if ($this->hasCircularDependency($crId, $data['depend_cr_id'])) {
                $errors['depend_cr_id'] = 'This dependency would create a circular reference.';
            }
        }

        // Validate release name format for release workflow
        if (isset($data['workflow_type_id']) && $data['workflow_type_id'] == $this->getWorkflowTypes()['release']) {
            if (empty($data['release_name'])) {
                $errors['release_name'] = 'Release name is required for release workflow.';
            }
        }

        // Validate calendar date for scheduled CRs
        if (isset($data['calendar']) && !empty($data['calendar'])) {
            $calendarDate = \Carbon\Carbon::parse($data['calendar']);
            if ($calendarDate->isPast()) {
                $errors['calendar'] = 'Calendar date cannot be in the past.';
            }
        }

        return $errors;
    }

    /**
     * Check for circular dependencies
     *
     * @param int $crId
     * @param int $dependCrId
     * @param array $visited
     * @return bool
     */
    protected function hasCircularDependency($crId, $dependCrId, $visited = []): bool
    {
        if ($crId == $dependCrId) {
            return true;
        }

        if (in_array($dependCrId, $visited)) {
            return true;
        }

        $visited[] = $dependCrId;

        $dependentCr = Change_request::find($dependCrId);
        if ($dependentCr && $dependentCr->depend_cr_id) {
            return $this->hasCircularDependency($crId, $dependentCr->depend_cr_id, $visited);
        }

        return false;
    }

    /**
     * Validate file uploads
     *
     * @param array $files
     * @return array
     */
    public function validateFileUploads(array $files): array
    {
        $errors = [];
        $uploadConfig = $this->getUploadConfiguration();

        foreach ($files as $key => $fileArray) {
            if (is_array($fileArray)) {
                foreach ($fileArray as $index => $file) {
                    $fileErrors = $this->validateSingleFile($file, $uploadConfig);
                    if (!empty($fileErrors)) {
                        $errors["{$key}.{$index}"] = $fileErrors;
                    }
                }
            } else {
                $fileErrors = $this->validateSingleFile($fileArray, $uploadConfig);
                if (!empty($fileErrors)) {
                    $errors[$key] = $fileErrors;
                }
            }
        }

        return $errors;
    }

    /**
     * Validate a single file upload
     *
     * @param mixed $file
     * @param array $config
     * @return array
     */
    protected function validateSingleFile($file, array $config): array
    {
        $errors = [];

        if (!$file || !$file->isValid()) {
            $errors[] = 'Invalid file upload.';
            return $errors;
        }

        // Check file size
        if ($file->getSize() > ($config['max_file_size'] * 1024)) {
            $errors[] = "File size exceeds maximum allowed size of {$config['max_file_size']}KB.";
        }

        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $config['allowed_extensions'])) {
            $errors[] = "File type '{$extension}' is not allowed. Allowed types: " . implode(', ', $config['allowed_extensions']);
        }

        // Check MIME type for additional security
        $mimeType = $file->getMimeType();
        if (!$this->isAllowedMimeType($mimeType, $extension)) {
            $errors[] = 'File type does not match its content.';
        }

        return $errors;
    }

    /**
     * Check if MIME type is allowed for the given extension
     *
     * @param string $mimeType
     * @param string $extension
     * @return bool
     */
    protected function isAllowedMimeType(string $mimeType, string $extension): bool
    {
        $allowedMimes = [
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xls' => ['application/vnd.ms-excel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'txt' => ['text/plain'],
            'zip' => ['application/zip'],
            'rar' => ['application/x-rar-compressed'],
        ];

        return isset($allowedMimes[$extension]) && in_array($mimeType, $allowedMimes[$extension]);
    }

    /**
     * Validate custom field values
     *
     * @param array $customFields
     * @return array
     */
    public function validateCustomFields(array $customFields): array
    {
        $errors = [];
        $customFieldConfig = $this->getCustomFieldConfiguration();

        if (!$customFieldConfig['enabled']) {
            return ['custom_fields' => 'Custom fields are not enabled.'];
        }

        if (count($customFields) > $customFieldConfig['max_per_request']) {
            $errors['custom_fields'] = "Maximum {$customFieldConfig['max_per_request']} custom fields allowed per request.";
        }

        foreach ($customFields as $fieldName => $fieldValue) {
            // Skip system fields
            if (in_array($fieldName, ['_token', '_method'])) {
                continue;
            }

            // Validate field value based on type (if type information is available)
            $fieldErrors = $this->validateCustomFieldValue($fieldName, $fieldValue);
            if (!empty($fieldErrors)) {
                $errors[$fieldName] = $fieldErrors;
            }
        }

        return $errors;
    }

    /**
     * Validate custom field value
     *
     * @param string $fieldName
     * @param mixed $fieldValue
     * @return array
     */
    protected function validateCustomFieldValue(string $fieldName, $fieldValue): array
    {
        $errors = [];

        // Basic validation - ensure value is not too long
        if (is_string($fieldValue) && strlen($fieldValue) > 1000) {
            $errors[] = 'Field value is too long (maximum 1000 characters).';
        }

        // Validate specific field types based on field name patterns
        if (strpos($fieldName, 'email') !== false && !empty($fieldValue)) {
            if (!filter_var($fieldValue, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format.';
            }
        }

        if (strpos($fieldName, 'url') !== false && !empty($fieldValue)) {
            if (!filter_var($fieldValue, FILTER_VALIDATE_URL)) {
                $errors[] = 'Invalid URL format.';
            }
        }

        if (strpos($fieldName, 'phone') !== false && !empty($fieldValue)) {
            if (!preg_match('/^[0-9\-\+\s\(\)]+$/', $fieldValue)) {
                $errors[] = 'Invalid phone number format.';
            }
        }

        return $errors;
    }

    /**
     * Get status IDs for workflow validation
     *
     * @return array
     */
    protected function getStatusIds(): array
    {
        return [
            'pending_production_deployment' => config('change_request.status_ids.pending_production_deployment', 6),
            'production_deployment' => config('change_request.status_ids.production_deployment', 7),
            'approved' => config('change_request.status_ids.approved', 8),
            'rejected' => config('change_request.status_ids.rejected', 9),
            'in_progress' => config('change_request.status_ids.in_progress', 3),
            'completed' => config('change_request.status_ids.completed', 10),
            'cancelled' => config('change_request.status_ids.cancelled', 11),
            'approved_implementation_plan' => config('change_request.status_ids.approved_implementation_plan', 116),
            'pending_uat' => config('change_request.parked_status_ids.pending_uat', 78),
            'promo_closure' => config('change_request.parked_status_ids.cancelled', 129),
        ];
    }

    /**
     * Get validation rules configuration
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'title' => [
                'min_length' => 10,
                'max_length' => 255,
                'required' => true,
            ],
            'description' => [
                'min_length' => 20,
                'max_length' => 5000,
                'required' => true,
            ],
            'estimation_fields' => [
                'max_hours' => 2000,
                'min_hours' => 0,
            ],
        ];
    }

    /**
     * Check if workflow type is valid
     *
     * @param int $workflowTypeId
     * @return bool
     */
    protected function isValidWorkflowType($workflowTypeId): bool
    {
        $validWorkflowTypes = array_values($this->getWorkflowTypes());
        return in_array($workflowTypeId, $validWorkflowTypes);
    }

    /**
     * Get workflow type constants
     *
     * @return array
     */
    protected function getWorkflowTypes(): array
    {
        return [
            'normal' => config('change_request.workflow_types.normal', 1),
            'emergency' => config('change_request.workflow_types.emergency', 2),
            'release' => config('change_request.workflow_types.release', 3),
            'maintenance' => config('change_request.workflow_types.maintenance', 4),
            'hotfix' => config('change_request.workflow_types.hotfix', 5),
        ];
    }

    /**
     * Get file upload configuration
     *
     * @return array
     */
    protected function getUploadConfiguration(): array
    {
        return [
            'max_file_size' => config('change_request.file_upload.max_file_size', 10240), // KB
            'max_files_per_request' => config('change_request.file_upload.max_files_per_request', 10),
            'allowed_extensions' => config('change_request.file_upload.allowed_extensions', [
                'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'
            ]),
            'upload_path' => config('change_request.file_upload.upload_path', 'uploads/change_requests/'),
        ];
    }

    /**
     * Get custom field configuration
     *
     * @return array
     */
    protected function getCustomFieldConfiguration(): array
    {
        return [
            'enabled' => config('change_request.custom_fields.enabled', true),
            'max_per_request' => config('change_request.custom_fields.max_per_request', 20),
            'max_field_length' => config('change_request.custom_fields.max_field_length', 1000),
            'allowed_field_types' => config('change_request.custom_fields.allowed_types', [
                'text', 'number', 'email', 'url', 'phone', 'date', 'textarea'
            ]),
        ];
    }

    /**
     * Get group IDs from configuration
     *
     * @return array
     */
    protected function getGroupIds(): array
    {
        return [
            'admin' => config('change_request.group_ids.admin', 1),
            'management' => config('change_request.group_ids.management', 2),
            'technical_team' => config('change_request.group_ids.technical_team', 3),
            'business_analyst' => config('change_request.group_ids.business_analyst', 4),
            'developer' => config('change_request.group_ids.developer', 5),
            'tester' => config('change_request.group_ids.tester', 6),
            'designer' => config('change_request.group_ids.designer', 7),
        ];
    }
}