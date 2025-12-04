<?php

namespace App\Services\ChangeRequest;

use App\Factories\Applications\ApplicationFactory;
use App\Factories\ChangeRequest\AttachmetsCRSFactory;
use App\Factories\ChangeRequest\ChangeRequestFactory;
use App\Factories\CustomField\CustomFieldGroupTypeFactory;
use App\Factories\Defect\DefectFactory;
use App\Factories\Groups\GroupFactory;
use App\Factories\Users\UserFactory;
use App\Http\Controllers\Mail\MailController;
use App\Http\Repository\RejectionReasons\RejectionReasonsRepository;
use App\Models\ApplicationImpact;
use App\Models\Change_request;
use App\Models\ChangeRequestTechnicalTeam;
use App\Models\Group;
use App\Models\ManDaysLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ChangeRequestService
{
    private const ATTACHMENT_TYPE_TECHNICAL = 1;
    private const ATTACHMENT_TYPE_BUSINESS = 2;
    private const FORM_TYPE_EDIT = 2;

    private $changerequest;
    private $attachments;
    private $custom_field_group_type;
    private $defects;

    public function __construct(
        ChangeRequestFactory $changerequest,
        AttachmetsCRSFactory $attachments,
        CustomFieldGroupTypeFactory $custom_field_group_type,
        DefectFactory $defect
    ) {
        $this->changerequest = $changerequest::index();
        $this->attachments = $attachments::index();
        $this->custom_field_group_type = $custom_field_group_type::index();
        $this->defects = $defect::index();
    }

    public function handleFileUploads(Request $request, int $cr_id): void
    {
        if ($request->hasFile('technical_attachments')) {
            $this->attachments->add_files(
                $request->file('technical_attachments'),
                $cr_id,
                self::ATTACHMENT_TYPE_TECHNICAL
            );
        }

        if ($request->hasFile('business_attachments')) {
            $this->attachments->add_files(
                $request->file('business_attachments'),
                $cr_id,
                self::ATTACHMENT_TYPE_BUSINESS
            );
        }
    }

    public function assignTechnicalTeams(Request $request, int $id): void
    {
        if (!isset($request->technical_teams) || empty($request->technical_teams)) {
            return;
        }

        foreach ($request->technical_teams as $teamId) {
            DB::table('change_request_technical_team')->insert([
                'cr_id' => $id,
                'technical_team_id' => $teamId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function handleCapUsersNotification(Request $request, int $id): void
    {
        if (empty($request->cap_users)) {
            return;
        }

        $emails = [];
        foreach ($request->cap_users as $userId) {
            $user = User::find($userId);
            if ($user) {
                $emails[] = $user->email;
            }
        }
        
        $cr = Change_request::find($id);

        if (!empty($emails)) {
            $mail = new MailController();
            $mail->send_mail_to_cap_users($emails, $id, $cr->cr_no);
        }
    }

    public function prepareEditData($cr, int $id): array
    {
        // Get users by workflow type
        $developer_users = $this->getDeveloperUsers($cr);
        $technical_groups = $this->getTechnicalGroups($cr);
        $sa_users = UserFactory::index()->get_user_by_department_id(6);
        $testing_users = UserFactory::index()->get_user_by_department_id(3);
        $cap_users = UserFactory::index()->get_users_cap($cr->application_id);
        $rtm_members = UserFactory::index()->get_user_by_group_id(23);

        // Get technical teams and related data
        $technical_teams = Group::where('technical_team', '1')->get();
        $technical_team_disabled = ChangeRequestTechnicalTeam::where('cr_id', $id)->get();

        // Get custom fields and other data
        $workflow_type_id = $cr->workflow_type_id;
        $status_id = $cr->getCurrentStatus()->status->id;
        $status_name = $cr->getCurrentStatus()->status->name;
        $CustomFields = $this->custom_field_group_type->CustomFieldsByWorkFlowTypeAndStatus(
            $workflow_type_id,
            self::FORM_TYPE_EDIT,
            $status_id
        );
        
        $logs_ers = $cr->logs;
        $all_defects = $this->defects->all_defects($id);
        $ApplicationImpact = ApplicationImpact::where('application_id', $cr->application_id)
            ->select('impacts_id')
            ->get();

        // Get technical team data
        $selected_technical_teams = $this->getSelectedTechnicalTeams($cr);
        $reminder_promo_tech_teams = $this->getReminderPromoTechTeams($cr);
        $reminder_promo_tech_teams_text = implode(',', $reminder_promo_tech_teams);
        
        // Get assignment users
        $view_by_groups = $cr->getCurrentStatus()->status->group_statuses
            ->where('type', '2')
            ->pluck('group_id')
            ->toArray();
        $assignment_users = UserFactory::index()->GetAssignmentUsersByViewGroups($view_by_groups);

        $man_day = $cr->change_request_custom_fields
            ->where('custom_field_name', 'man_days')
            ->values()
            ->toArray();
            
        $reject = new RejectionReasonsRepository();
        $rejects = $reject->workflows($workflow_type_id);
        
        $form_title = (!empty($workflow_type_id) && $workflow_type_id == 9)
            ? "Promo"
            : view()->shared('form_title');

        $title = (!empty($workflow_type_id) && $workflow_type_id == 9)
            ? "List Promo"
            : view()->shared('title');
            
        $man_days = ManDaysLog::where('cr_id', $id)->with('user')->get();
        
        // Relevant CRs logic
        $relevantField = $cr->change_request_custom_fields
            ->where('custom_field_name', 'relevant')
            ->first();
    
        $selectedRelevant = [];
        if ($relevantField && !empty($relevantField->custom_field_value)) {
            $decoded = json_decode($relevantField->custom_field_value, true);
            if (is_array($decoded)) {
                $selectedRelevant = array_map('intval', $decoded);
            }
        }
    
        $relevantCrsData = Change_request::whereIn('id', $selectedRelevant)
            ->with('CurrentRequestStatuses')
            ->get(['id', 'cr_no', 'title']);

        $pendingProductionId = config('change_request.status_ids.pending_production_deployment');
        $relevantNotPending = $relevantCrsData->filter(function ($item) use ($pendingProductionId) {
            return $item->CurrentRequestStatuses_last?->new_status_id != $pendingProductionId;
        })->count();

        return compact(
            'rejects', 'form_title', 'title',
            'selected_technical_teams', 'man_day', 'technical_team_disabled', 'status_name',
            'ApplicationImpact', 'cap_users', 'CustomFields', 'cr', 'workflow_type_id',
            'logs_ers', 'developer_users', 'sa_users', 'testing_users', 'technical_teams',
            'all_defects', 'reminder_promo_tech_teams', 'rtm_members', 'assignment_users',
            'reminder_promo_tech_teams_text', 'technical_groups', 'man_days',
            'relevantCrsData', 'relevantNotPending', 'pendingProductionId'
        );
    }

    public function getDeveloperUsers($cr)
    {
        if ($cr->workflow_type_id == 13) {
            $parentCR = DB::table('parents_crs')
                ->where('id', $cr->change_request_custom_fields
                    ->where('custom_field_name', 'parent_id')
                    ->values()
                    ->toArray()[0]['custom_field_value'] ?? null)
                ->value('application_name');

            $res = ApplicationFactory::index()->get_app_id_by_name($parentCR);
            return $res
                ? UserFactory::index()->get_user_by_group($res->id)
                : UserFactory::index()->get_user_by_group($cr->application_id);
        }
        
        $tech_group = $cr->change_request_custom_fields->where('custom_field_name','tech_group_id')->first();
        $tech_group_id = $tech_group ? $tech_group->custom_field_value : null;
        
        if($tech_group_id) {
            return UserFactory::index()->get_user_by_group_id($tech_group_id);
        } else {
            return UserFactory::index()->get_user_by_group($cr->application_id);
        }
    }

    public function getTechnicalGroups($cr)
    {
        return GroupFactory::index()->get_tech_groups_by_application($cr->application_id);
    }

    public function getSelectedTechnicalTeams($cr): array
    {
        try {
            return $cr->technical_Cr_first->technical_cr_team->pluck('group')->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getReminderPromoTechTeams($cr): array
    {
        return $cr->technical_Cr
            ? $cr->technical_Cr->technical_cr_team
                ->where('status', '0')
                ->pluck('group')
                ->pluck('title')
                ->toArray()
            : [];
    }
    
    public function logManDays(Request $request, int $id): void
    {
        if ($request->man_days && !empty($request->man_days)) {
            ManDaysLog::create([
                'group_id' => Session::get('current_group'),
                'user_id' => auth()->user()->id,
                'cr_id' => $id,
                'man_day' => $request->man_days,
            ]);
        }
    }
}
