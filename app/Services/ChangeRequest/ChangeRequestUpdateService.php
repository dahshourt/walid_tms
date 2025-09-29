<?php
namespace App\Services\ChangeRequest;

use App\Models\{
    Change_request,
    User,
    CabCr,
    CabCrUser,
    TechnicalCr,
    TechnicalCrTeam,
    TechnicalCrTeamStatus,
    NewWorkFlow,
    CustomField,
    ChangeRequestCustomField,
    Change_request_statuse
};
use App\Http\Repository\{
    Logs\LogRepository,
    ChangeRequest\ChangeRequestStatusRepository,
    ChangeRequest\ChangeRequestRepository
};
use App\Services\ChangeRequest\{
    ChangeRequestEstimationService,
    ChangeRequestStatusService,
    ChangeRequestValidationService
};
use App\Traits\ChangeRequest\ChangeRequestConstants;
use Auth;
use Illuminate\Support\Arr;

class ChangeRequestUpdateService
{
    use ChangeRequestConstants;

    protected $logRepository;
    protected $statusRepository;
    protected $estimationService;
    protected $validationService;
    protected $statusService;
    private $changeRequest_old;

    public function __construct()
    {
        $this->logRepository     = new LogRepository();
        $this->statusRepository  = new ChangeRequestStatusRepository();
        $this->estimationService = new ChangeRequestEstimationService();
        $this->validationService = new ChangeRequestValidationService();
        $this->statusService     = new ChangeRequestStatusService();
    }

    public function update($id, $request)
    {
        $this->changeRequest_old = Change_request::find($id);

        // 1) CAB CR gate
        if ($this->handleCabCrValidation($id, $request)) {
            return true;
        }
        
        // 2) Per-process validators
        if ($this->handleTechnicalTeamValidation($id, $request)) {
            return true;
        }

        // 3) Assignments
        $this->handleUserAssignments($id, $request);

        // 4) CAB users (if any)
        $this->handleCabUsers($id, $request);

        // 5) Technical teams bootstrap (parallel streams)
        $this->handleTechnicalTeams($id, $request);

        // 6) Per-team technical statuses (non-blocking)
        //$this->handleTechnicalStatuses($id, $request);

        // 7) Estimations
        $this->handleEstimations($id, $request);

        // 8) Update CR data (custom fields + main cols)
        $this->updateCRData($id, $request);

        // 9) Update assignment on current CR status row
        $this->updateStatusAssignments($id, $request);

        // 10) CR-level status move (main workflow)
        if (isset($request->new_status_id)) {
            $this->statusService->updateChangeRequestStatus($id, $request);
        }

        

        // 12) Audit
        $this->logRepository->logCreate($id, $request, $this->changeRequest_old, 'update');

        return true;
    }

    public function updateTestableFlag($id, $request)
    {
        $this->changeRequest_old = Change_request::find($id);
        $this->updateCRData($id, $request);
        $this->logRepository->logCreate($id, $request, $this->changeRequest_old, 'update');
        return true;
    }

    /* ======================================================================
     |                          CAB CR VALIDATION
     * ====================================================================== */
    protected function handleCabCrValidation($id, $request): bool
    {
        if ($request->cab_cr_flag != '1') {
            return false;
        }

        $user_id = Auth::user()->id;
        $cabCr = CabCr::where("cr_id", $id)->where('status', '0')->first();
        $checkWorkflowType = NewWorkFlow::find($request->new_status_id)->workflow_type;

        unset($request['cab_cr_flag']);

        if ($checkWorkflowType) { // reject
            $cabCr->status = '2';
            $cabCr->save();
            $cabCr->cab_cr_user()->where('user_id', $user_id)->update(['status' => '2']);
        } else { // approve
            $cabCr->cab_cr_user()->where('user_id', $user_id)->update(['status' => '1']);

            $countAllUsers      = $cabCr->cab_cr_user->count();
            $countApprovedUsers = $cabCr->cab_cr_user->where('status', '1')->count();

            if ($countAllUsers > $countApprovedUsers) {
                $this->updateCRData($id, $request);
                return true;
            } else {
                $cabCr->status = '1';
                $cabCr->save();
            }
        }

        return false;
    }

    protected function handleTechnicalTeamValidation($id, $request): bool
    {
        return $this->validationService->handleTechnicalTeamValidation($id, $request);
    }

    /* ======================================================================
     |                          ASSIGNMENTS & CAB USERS
     * ====================================================================== */
    protected function handleUserAssignments($id, $request): void
    {
        $user = $request['assign_to'] ? User::find($request['assign_to']) : Auth::user();

        if ($this->needsAssignmentUpdate($request)) {
            $request['assignment_user_id'] = $user->id;
        }
    }

    protected function needsAssignmentUpdate($request): bool
    {
        return (isset($request['dev_estimation'])) ||
               (isset($request['testing_estimation'])) ||
               (isset($request['design_estimation'])) ||
               ($request['assign_to']) ||
               (isset($request['CR_estimation']));
    }

    protected function handleCabUsers($id, $request): void
    {
        if (empty($request->cap_users)) {
            return;
        }

        $record = CabCr::create([
            'cr_id'  => $id,
            'status' => "0",
        ]);

        foreach ($request->cap_users as $userId) {
            CabCrUser::create([
                'user_id'   => $userId,
                'cab_cr_id' => $record->id,
                'status'    => "0",
            ]);
        }
    }

    /* ======================================================================
     |                          TECHNICAL TEAMS (BOOTSTRAP)
     * ====================================================================== */
    protected function handleTechnicalTeams($id, $request): void
    {
        if (empty($request->technical_teams)) {
            return;
        }

        $newStatusId = $request->new_status_id ?? null;
        $workflow    = $newStatusId ? NewWorkFlow::find($newStatusId) : null;

        $record = TechnicalCr::create([
            'cr_id'  => $id,
            'status' => "0",
        ]);

        foreach ($request->technical_teams as $groupId) {
            TechnicalCrTeam::create([
                'group_id'          => $groupId,
                'technical_cr_id'   => $record->id,
                'current_status_id' => $workflow && isset($workflow->workflowstatus[0])
                    ? $workflow->workflowstatus[0]->to_status_id
                    : null,
                'status'            => "0",
            ]);
        }
		
		// 11) Auto-mirror CR status to tech stream(s) if no explicit tech params were sent
        if (isset($request->new_status_id)) {
            $new_status_id = $workflow && isset($workflow->workflowstatus[0])
            ? $workflow->workflowstatus[0]->to_status_id: null;
            // Scope 'actor': mirror only to the logged-in user's team on this CR.
            // Change to 'all' to mirror to all streams.
            $this->mirrorCrStatusToTechStreams($id, (int) $new_status_id, $request->tech_note ?? null, 'all');
        }
		
    }

    /* ======================================================================
     |                          ESTIMATION
     * ====================================================================== */
    protected function handleEstimations($id, $request): void
    {
        $changeRequest = Change_request::find($id);
        $user = $request['assign_to'] ? User::find($request['assign_to']) : Auth::user();

        if ($this->needsEstimationCalculation($request)) {
            $data = $this->estimationService->calculateEstimation($id, $changeRequest, $request, $user);
            $request->merge($data);
        }
    }

    protected function needsEstimationCalculation($request): bool
    {
        return (isset($request['CR_duration']) && $request['CR_duration'] != '') ||
               (isset($request['dev_estimation']) && $request['dev_estimation'] != '') ||
               (isset($request['design_estimation']) && $request['design_estimation'] != '') ||
               (isset($request['testing_estimation']) && $request['testing_estimation'] != '');
    }

    /* ======================================================================
     |                          CORE DATA UPDATE
     * ====================================================================== */
    public function updateCRData($id, $request)
    {
        $arr = Arr::only($request->all(), $this->getRequiredFields());
        $fileFields = ['technical_attachments', 'business_attachments', 'cap_users', 'technical_teams'];
        $data = Arr::except($request->all(), array_merge(['_method'], $fileFields));

        $this->handleCustomFieldUpdates($id, $data);

        return Change_request::where('id', $id)->update($arr);
    }

    protected function handleCustomFieldUpdates($id, $data): void
    {
        $testable = 0;
        if (request()->input('testable')) {
            $testable = (string) request()->input('testable') === '1' ? 1 : 0;
        }

        foreach ($data as $key => $value) {
            if ($key === 'testable' && request()->input('testable') !== null) {
                $customFieldId = CustomField::findId($key);
                if ($customFieldId && $value !== null) {
                    $changeRequestCustomField = [
                        "cr_id"              => $id,
                        "custom_field_id"    => $customFieldId->id,
                        "custom_field_name"  => $key,
                        "custom_field_value" => $testable,
                        "user_id"            => auth()->id(),
                    ];
                    $this->insertOrUpdateChangeRequestCustomField($changeRequestCustomField);
                }
            } else {
                if (($key != "_token" && $key != 'testable') || $key === 'cr') {
                    $customFieldId = CustomField::findId($key);
                    if ($customFieldId && $value !== null) {
                        $changeRequestCustomField = [
                            "cr_id"              => $id,
                            "custom_field_id"    => $customFieldId->id,
                            "custom_field_name"  => $key,
                            "custom_field_value" => $value,
                            "user_id"            => auth()->id(),
                        ];
                        $this->insertOrUpdateChangeRequestCustomField($changeRequestCustomField);
                    }
                }
            }
        }
    }

    protected function insertOrUpdateChangeRequestCustomField(array $data): void
    {
        if (in_array($data['custom_field_name'], ['technical_feedback', 'business_feedback'])) {
            ChangeRequestCustomField::create([
                'cr_id'              => $data['cr_id'],
                'custom_field_id'    => $data['custom_field_id'],
                'custom_field_name'  => $data['custom_field_name'],
                'custom_field_value' => $data['custom_field_value'],
                'user_id'            => $data['user_id']
            ]);
        } else {
            ChangeRequestCustomField::updateOrCreate(
                [
                    'cr_id'             => $data['cr_id'],
                    'custom_field_id'   => $data['custom_field_id'],
                    'custom_field_name' => $data['custom_field_name']
                ],
                [
                    'custom_field_value'=> $data['custom_field_value'],
                    'user_id'           => $data['user_id']
                ]
            );
        }
    }

    protected function updateStatusAssignments($id, $request): void
    {
        $oldStatusId = $request->old_status_id ?? null;

        if (isset($request->assignment_user_id) && $oldStatusId) {
            Change_request_statuse::where('cr_id', $id)
                ->where('new_status_id', $oldStatusId)
                ->where('active', '1')
                ->update(['assignment_user_id' => $request->assignment_user_id]);
        }

        $memberFields = ['cr_member', 'rtm_member', 'assignment_user_id', 'tester_id', 'developer_id', 'designer_id'];
        foreach ($memberFields as $field) {
            if (isset($request->$field) && $oldStatusId) {
                Change_request_statuse::where('cr_id', $id)
                    ->where('new_status_id', $oldStatusId)
                    ->where('active', '1')
                    ->update(['assignment_user_id' => $request->$field]);
            }
        }
    }

    private function shouldHandleCabApproval($request): bool
    {
        return isset($request->cab_cr_flag) && $request->cab_cr_flag == '1';
    }

    private function processCabApproval($id, $request): bool
    {
        $userId = Auth::user()->id ?? $request->user_id;
        $cabCr  = CabCr::where("cr_id", $id)->where('status', '0')->first();

        if (!$cabCr) {
            return false;
        }

        $checkWorkflowType = NewWorkFlow::find($request->new_status_id)->workflow_type;

        if ($checkWorkflowType) { // reject
            $cabCr->status = '2';
            $cabCr->save();
            $cabCr->cab_cr_user()->where('user_id', $userId)->update(['status' => '2']);
        } else { // approve
            $cabCr->cab_cr_user()->where('user_id', $userId)->update(['status' => '1']);

            $countAllUsers      = $cabCr->cab_cr_user->count();
            $countApprovedUsers = $cabCr->cab_cr_user->where('status', '1')->count();

            if ($countAllUsers > $countApprovedUsers) {
                return true;
            } else {
                $cabCr->status = '1';
                $cabCr->save();
            }
        }

        return false;
    }

    /* ======================================================================
     |                  TECHNICAL STREAM STATUS HANDLERS (NON-BLOCKING)
     * ====================================================================== */

    /**
     * Handle parallel technical stream status updates.
     * Supports:
     *  - $request->tech_statuses[technical_cr_team_id] = to_status_id
     *  - $request->tech_to_status_id for actor's own team on this CR
     */
    protected function handleTechnicalStatuses($id, $request): void
    {
        // A) explicit per-team map
        if (isset($request->tech_statuses) && is_array($request->tech_statuses)) {
            $notes = is_array($request->tech_notes ?? null) ? $request->tech_notes : [];
            foreach ($request->tech_statuses as $teamId => $toStatusId) {
                $this->advanceTeamStream((int) $teamId, (int) $toStatusId, $notes[$teamId] ?? null);
            }
        }

        // B) implicit for actor's team (single hop)
        if (isset($request->tech_to_status_id)) {
            $actor        = Auth::user();
            $actorGroupId = $actor->group_id ?? null;

            if ($actorGroupId) {
                $team = TechnicalCrTeam::query()
                    ->whereHas('technicalCr', fn($q) => $q->where('cr_id', $id))
                    ->where('group_id', $actorGroupId)
                    ->first();

                if ($team) {
                    $this->advanceTeamStream(
                        $team->id,
                        (int) $request->tech_to_status_id,
                        $request->tech_note ?? null
                    );
                }
            }
        }
    }

    /**
     * Advance a single technical stream (team) if the transition is allowed.
     * Writes TechnicalCrTeamStatus; model events or DB triggers will sync the snapshot.
     */
    protected function advanceTeamStream(int $technicalCrTeamId, int $toStatusId, ?string $note = null): void
    {
        
        $team = TechnicalCrTeam::find($technicalCrTeamId);
        if (!$team) return;
		$oldStatusId = request()->old_status_id ?? 0;
        $fromStatusId = (int) ($team->current_status_id ?? $oldStatusId);
        
        // Validate (from -> to) via workflow graph
        // if (!$this->isAllowedTeamTransition($fromStatusId, $toStatusId)) {
        //     return; // or throw new \RuntimeException('Transition not allowed for this stream.');
        // }

        TechnicalCrTeamStatus::create([
            'technical_cr_team_id' => $team->id,
            'old_status_id'        => $fromStatusId ?: null,
            'new_status_id'        => $toStatusId,
            'user_id'              => Auth::id(),
            'note'                 => $note,
        ]);
    }

    /**
     * Auto-mirror CR status to tech stream(s).
     * $scope: 'actor' (default) or 'all'
     */
    public function mirrorCrStatusToTechStreams(int $crId, int $toStatusId, ?string $note = null, string $scope = 'actor'): void
    {
        
        if ($scope === 'all') {
            $teams = TechnicalCrTeam::query()
                ->whereHas('technicalCr', fn($q) => $q->where('cr_id', $crId))
                ->get();
        } else { // actor
            $actorGroupId = session('default_group') ?: auth()->user()->default_group;
            if (!$actorGroupId) return;

            $teams = TechnicalCrTeam::query()
                ->whereHas('technicalCr', fn($q) => $q->where('cr_id', $crId))
                ->where('group_id', $actorGroupId)
                ->get();
        }
        foreach ($teams as $team) {
            $this->advanceTeamStream($team->id, $toStatusId, $note ?? 'auto: mirrored from CR status');
        }
    }

    /**
     * Check if a (from -> to) transition is allowed for a tech stream
     * using new_workflow/new_workflow_statuses edges.
     */
    protected function isAllowedTeamTransition(int $fromStatusId, int $toStatusId): bool
    {
        // If stream hasn't started yet, allow first hop (mirroring-friendly).
        if ($fromStatusId === 0) {
            return true;
        }

        return NewWorkFlow::query()
            ->where('from_status_id', $fromStatusId)
            ->where('active', '1')
            ->whereHas('workflowstatus', fn($q) => $q->where('to_status_id', $toStatusId))
            ->exists();
    }
}
