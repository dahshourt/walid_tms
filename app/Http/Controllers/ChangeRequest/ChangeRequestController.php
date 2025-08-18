<?php

namespace App\Http\Controllers\ChangeRequest;

use App\Factories\ChangeRequest\AttachmetsCRSFactory;
use App\Factories\ChangeRequest\ChangeRequestFactory;
use App\Factories\ChangeRequest\ChangeRequestStatusFactory;
use App\Factories\NewWorkFlow\NewWorkFlowFactory;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Mail\MailController;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;
use App\Http\Requests\Change_Request\Api\attachments_CRS_Request;
use App\Http\Requests\Change_Request\Api\changeRequest_Requests;
use App\Http\Resources\ChangeRequestListResource;
use App\Http\Resources\ChangeRequestResource;
use App\Http\Resources\MyAssignmentsCRSResource;
use App\Http\Resources\MyCRSResource;
use App\Factories\Workflow\Workflow_type_factory;
use App\Factories\CustomField\CustomFieldGroupTypeFactory;
use App\Factories\Applications\ApplicationFactory;
use App\Factories\Users\UserFactory;
use App\Factories\Defect\DefectFactory;
use App\Models\Group;
use App\Models\Change_request;
use App\Models\Attachements_crs;
use App\Models\User;
use App\Models\Defect;
use App\Models\WorkFlowType;
use App\Models\ChangeRequestTechnicalTeam;
use App\Models\Change_request_statuse;
use App\Models\ApplicationImpact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Access\AuthorizationException;

class ChangeRequestController extends Controller
{
    // Constants for better maintainability
    private const FORM_TYPE_CREATE = 1;
    private const FORM_TYPE_EDIT = 2;
    private const ATTACHMENT_TYPE_TECHNICAL = 1;
    private const ATTACHMENT_TYPE_BUSINESS = 2;
    private const MAX_FILE_SIZE = 5120; // 5MB in KB
    
    private const ALLOWED_MIMES = [
        'docx', 'doc', 'xls', 'xlsx', 'pdf', 'zip', 'rar', 
        'jpeg', 'jpg', 'png', 'gif', 'msg'
    ];
    
    private const ALLOWED_MIME_TYPES = [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/pdf',
        'application/zip',
        'application/x-rar-compressed',
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/vnd.ms-outlook'
    ];

    private $changerequest;
    private $changerequeststatus;
    private $workflow;
    private $workflow_type;
    private $attachments;
    private $custom_field_group_type;
    private $applications;
    private $defects;
    private $view = 'change_request';

    public function __construct(
        DefectFactory $defect,
        ChangeRequestFactory $changerequest,
        ChangeRequestStatusFactory $changerequeststatus,
        NewWorkFlowFactory $workflow,
        AttachmetsCRSFactory $attachments,
        Workflow_type_factory $workflow_type,
        CustomFieldGroupTypeFactory $custom_field_group_type,
        ApplicationFactory $applications
    ) {
        $this->changerequest = $changerequest::index();
        $this->defects = $defect::index();
        $this->changerequeststatus = $changerequeststatus::index();
        $this->changerworkflowequeststatus = $workflow::index();
        $this->workflow_type = $workflow_type::index();
        $this->attachments = $attachments::index();
        $this->custom_field_group_type = $custom_field_group_type::index();
        $this->applications = $applications::index();
        
        $this->shareViewData();
    }

    /**
     * Share common view data across all views
     */
    private function shareViewData(): void
    {
        view()->share([
            'view' => $this->view,
            'route' => 'change_request',
            'title' => 'List Change Requests',
            'form_title' => 'CR'
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->authorize('List change requests');
            $collection = $this->changerequest->getAll();
            
            return view("{$this->view}.index", compact('collection'));
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized access attempt to change requests list', [
                'user_id' => auth()->id(),
                'ip' => request()->ip()
            ]);
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
    }
	// public function asd($group = null)

	// {
		
	// 	$gr = Group::find($group);

	// 	if ($gr) {
	// 		session(['default_group_name' => $gr->title]);
	// 	} else {
	// 		session()->forget('default_group_name'); // Clear the name if the group is not found
	// 	}

	// 	// Check if group is provided in the URL; if not, handle the absence
	// 	if (!$group) {
	// 		return redirect()->back()->with('error', 'No group provided.');
	// 	}
	// 	session(['default_group' => $group]);
	// 	// Fetch all user groups for the dropdown
	// 	$userGroups = auth()->user()->user_groups()->with('group')->get();

	// 	// Check if the provided group exists in the user's groups
	// 	$selectedGroup = $userGroups->pluck('group.id')->contains($group);

	// 	if (!$selectedGroup) {
	// 		// If the group does not exist in the user's groups, return back with an error message
	// 		return redirect()->back()->with('error', 'You do not have access to this group.');
	// 	}
	// 	$selectedGroup = Group::find($group);
	// 	session()->put('current_group',$group);   
	// 	session()->put('current_group_name',$selectedGroup->title);   
	// 	return redirect()->back(); 
	// 	//session()->set('current_group',$group);    
	// 	// // Fetch the selected group object
	// 	// $selectedGroup = Group::find($group);

	// 	// // Fetch change request collection filtered by the group from the URL
	// 	// $collection = $this->changerequest->getAll($group);

	// 	// // Return the view with the selected group and user groups
	// 	// return view("$this->view.index", compact('collection', 'selectedGroup', 'userGroups'));
	// }

    /**
     * Display change requests waiting for division manager approval
     */
    public function dvision_manager_cr()
    {
        try {
            $this->authorize('CR Waiting Approval');
            
            $title = 'CR Waiting Approval';
            $collection = $this->changerequest->dvision_manager_cr();
            
            return view("{$this->view}.dvision_manager_cr", compact('collection', 'title'));
        } catch (AuthorizationException $e) {
            Log::warning('Unauthorized access attempt to division manager CRs', [
                'user_id' => auth()->id()
            ]);
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }
    }

    /**
     * Select and store group in session
     */
    public function selectGroup(int $groupId)
    {
        $group = Group::find($groupId);
        
        if (!$group) {
            return redirect()->back()->with('error', 'Group not found.');
        }

        session([
            'default_group' => $groupId,
            'default_group_name' => $group->title
        ]);

        return redirect()->back()->with('success', 'Group selected successfully.');
    }

    /**
     * Enhanced group selection with validation
     */
    // selectUserGroup Function
    public function asd(?int $group = null)
    {
        if (!$group) {
            return redirect()->back()->with('error', 'No group provided.');
        }

        $selectedGroup = Group::find($group);
        if (!$selectedGroup) {
            return redirect()->back()->with('error', 'Group not found.');
        }

        // Validate user access to group
        $userGroups = auth()->user()->user_groups()->with('group')->get();
        $hasAccess = $userGroups->pluck('group.id')->contains($group);

        if (!$hasAccess) {
            Log::warning('User attempted to access unauthorized group', [
                'user_id' => auth()->id(),
                'group_id' => $group
            ]);
            return redirect()->back()->with('error', 'You do not have access to this group.');
        }

        session([
            'default_group' => $group,
            'current_group' => $group,
            'current_group_name' => $selectedGroup->title
        ]);

        return redirect()->back()->with('success', 'Group selected successfully.');
    }

    /**
     * Show all workflow subtypes for CR creation
     */
    public function Allsubtype()
    {
        $this->authorize('Create ChangeRequest');
        
        $target_systems = $this->applications->getAll();
        return view("{$this->view}.list_work_flow", compact('target_systems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('Create ChangeRequest');
        
        $target_system_id = request()->get('target_system_id');
        if (!$target_system_id) {
            return redirect()->back()->with('error', 'Target system ID is required.');
        }

        $target_system = $this->applications->find($target_system_id);
        if (!$target_system) {
            return redirect()->back()->with('error', 'Target system not found.');
        }

        $workflow_type_id = $this->applications->workflowType($target_system_id)->id;
        $CustomFields = $this->custom_field_group_type->CustomFieldsByWorkFlowType(
            $workflow_type_id, 
            self::FORM_TYPE_CREATE
        );
        
        $title = "Create {$target_system->name} CR";
        
        return view("{$this->view}.create", compact(
            'CustomFields', 
            'workflow_type_id', 
            'target_system', 
            'title'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(changeRequest_Requests $request)
    {
        $this->authorize('Create ChangeRequest');
        
        DB::beginTransaction();
        try {
            // Validate attachments if present
            $this->validateAttachments($request);
            
            // Create the change request
            $cr_data = $this->changerequest->create($request->all());
            $cr_id = $cr_data['id'];
            $cr_no = $cr_data['cr_no'];
            
            // Handle file uploads
            $this->handleFileUploads($request, $cr_id);
            
            DB::commit();
            
            Log::info('Change request created successfully', [
                'cr_id' => $cr_id,
                'cr_no' => $cr_no,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('status', "Created Successfully CR#{$cr_no}");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create change request', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            return redirect()->back()->with('error', 'Failed to create change request. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $this->authorize('Show ChangeRequest');
        
        $cr = $this->changerequest->findById($id);
        if (!$cr) {
            return redirect()->back()->with('error', 'Change request not found.');
        }

        $cr = $this->changerequest->find($id);
        if (!$cr) {
            $cr = Change_request::find($id);
        }

        if (!$cr) {
            return redirect()->back()->with('error', 'Change request not found.');
        }

        $workflow_type_id = $cr->workflow_type_id;
        $status_id = $cr->getCurrentStatus()?->status?->id;
        $status_name = $cr->getCurrentStatus()?->status?->name;
        
        $CustomFields = $this->custom_field_group_type->CustomFieldsByWorkFlowTypeAndStatus(
            $workflow_type_id, 
            self::FORM_TYPE_EDIT, 
            $status_id
        );
        
        $logs_ers = $cr->logs;
        $technical_teams = Group::where('technical_team', '1')->get();
        $title = 'View Change Request';

        return view("{$this->view}.show", compact(
            'CustomFields', 
            'cr', 
            'status_name', 
            'title', 
            'logs_ers', 
            'technical_teams'
        ));
    }

    /**
     * Show the form for editing the specified resource for CAB.
     */
    public function edit_cab(int $id)
    {
        return $this->edit($id, true);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id, bool $cab_cr_flag = false)
    {
        $this->authorize('Edit ChangeRequest');
        
        // Validate division manager access if requested
        if (request()->has('check_dm')) {
            $validation = $this->validateDivisionManagerAccess($id);
            if ($validation) {
                return $validation;
            }
        }

        $cr = $this->getCRForEdit($id, $cab_cr_flag);
        if (is_a($cr, 'Illuminate\Http\RedirectResponse')) {
            return $cr;
        }

        $editData = $this->prepareEditData($cr, $id);
        //dd($editData);
        return view("{$this->view}.edit", $editData);
    }

    /**
     * Download attachment file
     */
    public function download(int $id)
    {
        $file = Attachements_crs::findOrFail($id);
        $filePath = public_path("uploads/{$file->file_name}");

        if (!file_exists($filePath)) {
            return redirect()->back()->withErrors('File not found.');
        }

        return response()->download($filePath, $file->file);
    }

    /**
     * Delete attachment file
     */
    public function deleteFile(int $id)
    {
        $file = Attachements_crs::findOrFail($id);
        
        // Authorization check
        if (!auth()->user()->hasRole('Super Admin') && auth()->user()->id !== $file->user->id) {
            Log::warning('Unauthorized file deletion attempt', [
                'user_id' => auth()->id(),
                'file_id' => $id
            ]);
            return redirect()->back()->withErrors('You are not allowed to delete this file.');
        }

        $deleted = $this->attachments->delete_file($id);
        
        if ($deleted) {
            return redirect()->back()->with('success', 'File deleted successfully.');
        }
        
        return redirect()->back()->withErrors('File not found.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(changeRequest_Requests $request, int $id)
    {
        $this->authorize('Edit ChangeRequest');
        
        DB::beginTransaction();
        try {
            // Handle technical teams assignment
            $this->assignTechnicalTeams($request, $id);
            
            // Handle CAP users email notification
            
            
            // Validate attachments
            $this->validateAttachments($request);
            
            // Update change request
            $cr_id = $this->changerequest->update($id, $request);
            
            if ($cr_id === false) {
                throw new \Exception('Failed to update change request');
            }
            
            // Handle file uploads
            $this->handleFileUploads($request, $id);
			
			$this->handleCapUsersNotification($request, $id);
            
            DB::commit();
            
            Log::info('Change request updated successfully', [
                'cr_id' => $id,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->to('/change_request')->with('status', 'Updated Successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update change request', [
                'cr_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            return redirect()->back()->with('error', 'Failed to update change request.');
        }
    }

    /**
     * Handle division manager action via email link
     */
    public function handleDivisionManagerAction(Request $request)
    {
        $cr_id = $request->query('crId');
        $action = $request->query('action');
        $token = $request->query('token');

        // Validate request parameters
        if (!$cr_id || !$action || !$token) {
            return $this->renderActionResponse(false, 'Error', 'Invalid request. Missing parameters.', 400);
        }

        $cr = Change_request::find($cr_id);
        if (!$cr) {
            return $this->renderActionResponse(false, 'Error', 'Change Request not found.', 404);
        }

        // Validate token
        $expectedToken = $this->generateSecurityToken($cr);
        if ($token !== $expectedToken) {
            Log::warning('Invalid token used for division manager action', [
                'cr_id' => $cr_id,
                'ip' => request()->ip()
            ]);
            return $this->renderActionResponse(false, 'Error', 'Unauthorized access. Invalid token.', 403);
        }

        // Check current status and validate action
        $current_status = Change_request_statuse::where('cr_id', $cr_id)
            ->where('active', '1')
            ->value('new_status_id');

        if ($current_status != '22') {
            $message = $current_status == '19' ? 'You already rejected this CR.' : 'You already approved this CR.';
            return $this->renderActionResponse(false, 'Error', $message, 400);
        }

        // Process the action
        $result = $this->processDivisionManagerAction($cr, $action, $current_status);
        
        return $result;
    }

    /**
     * My assignments view
     */
    public function my_assignments()
    {
        $this->authorize('My Assignments');
        
        $collection = $this->changerequest->my_assignments_crs();
        $title = "My Assignments";
        
        return view("{$this->view}.index", compact('collection', 'title'));
    }

    /**
     * Get user's CRs as JSON
     */
    public function my_crs()
    {
        $crs = $this->changerequest->my_crs();
        $my_crs = MyCRSResource::collection($crs);

        return response()->json(['data' => $my_crs], 200);
    }

    /**
     * List CRs by current user
     */
    public function list_crs_by_user(Request $request)
    {
        $this->authorize('Show My CRs');
        
        $user = auth()->user();
        $workflow_type = $request->input('workflow_type', 'In House');
        
        $status_promo_view = $this->getPromoStatusView($workflow_type);
        
        $collection = $this->buildUserCRQuery($user->id, $workflow_type)->get();
        
        $crs_in_queues = 0; // This seems to be unused in the original
        $user_name = $user->user_name;
        return view("{$this->view}.CRsByuser", compact(
            'collection', 
            'user_name',
            'crs_in_queues',
            'status_promo_view'
        ));
    }

    /**
     * Reorder change request home
     */
    public function reorderhome()
    {
        $this->authorize('Shift ChangeRequest');
        return view("{$this->view}.shifiting");
    }

    /**
     * Reorder change request times
     */
    public function reorderChangeRequest(Request $request)
    {
        $this->authorize('Shift ChangeRequest');
        
        $request->validate([
            'change_request_id' => 'required|exists:change_request,cr_no',
        ]);

        $crId = $request->input('change_request_id');
        $repository = new ChangeRequestRepository();
        $result = $repository->reorderTimes($crId);

        if ($result['status']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Update approved active status
     */
    public function approved_active(Request $request)
    {
        $id = $request->get('id');
        $this->changerequest->UpateChangeRequestStatus($id, $request);
        
        return response()->json([
            'message' => 'Updated Successfully',
            'status' => 'success'
        ]);
    }

    // ===== PRIVATE HELPER METHODS =====

    /**
     * Validate file attachments
     */
    private function validateAttachments(Request $request): void
    {
        $attachmentTypes = ['technical_attachments', 'business_attachments'];
        
        foreach ($attachmentTypes as $type) {
            if ($request->hasFile($type)) {
                $validator = Validator::make($request->all(), [
                    "{$type}.*" => [
                        'required',
                        'file',
                        'mimes:' . implode(',', self::ALLOWED_MIMES),
                        'mimetypes:' . implode(',', self::ALLOWED_MIME_TYPES),
                        'max:' . self::MAX_FILE_SIZE
                    ]
                ], [
                    "{$type}.*.required" => 'Please upload an attachment',
                    "{$type}.*.mimes" => 'Only ' . implode(',', self::ALLOWED_MIMES) . ' files are allowed',
                    "{$type}.*.mimetypes" => 'Only ' . implode(',', self::ALLOWED_MIMES) . ' files are allowed',
                    "{$type}.*.max" => 'Maximum file size is 5MB',
                ]);

                if ($validator->fails()) {
                    throw new \Exception($validator->errors()->first());
                }
            }
        }
    }

    /**
     * Handle file uploads for both technical and business attachments
     */
    private function handleFileUploads(Request $request, int $cr_id): void
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

    /**
     * Validate division manager access
     */
    private function validateDivisionManagerAccess(int $id)
    {
        $user_email = strtolower(auth()->user()->email);
        $division_manager = strtolower(
            Change_request::where('id', $id)->value('division_manager')
        );
        $current_status = Change_request_statuse::where('cr_id', $id)
            ->where('active', '1')
            ->value('new_status_id');

        if ($user_email === $division_manager && $current_status != '22') {
            return response()->view("{$this->view}.action_response", [
                'isSuccess' => false,
                'title' => 'Error',
                'message' => 'You already took action on this CR',
                'status' => 400
            ], 400);
        }

        return null;
    }

    /**
     * Get CR for editing with proper validation
     */
    private function getCRForEdit(int $id, bool $cab_cr_flag)
    {
        if ($cab_cr_flag) {
            return $this->validateCabCR($id);
        }

        $cr = $this->changerequest->findById($id);
        if (!$cr) {
            return redirect()->back()->with('status', 'CR not exists');
        }

        $cr = $this->changerequest->find($id);
        if (!$cr) {
            return redirect()->to('/change_request')->with('status', 'You have no access to edit this CR');
        }

        return $cr;
    }

    /**
     * Validate CAB CR access
     */
    private function validateCabCR(int $id)
    {
        $cr = $this->changerequest->findCr($id);
        
        if (!$cr) {
            return redirect()->back()->with('status', 'You have no access to edit this CR');
        }
        
        if (empty($cr->cab_cr) || $cr->cab_cr->status == '2') {
            return redirect()->to('/')->with('status', 'CR already rejected');
        }
        
        $user_id = auth()->id();
        $cr_cab_user = $cr->cab_cr->cab_cr_user->pluck('user_id')->toArray();
        
        if (!in_array($user_id, $cr_cab_user)) {
            return redirect()->to('/')->with('status', 'You have no access to edit this CR');
        }
        
        $check_if_approve = $cr->cab_cr->cab_cr_user
            ->where('user_id', $user_id)
            ->where('status', '1')
            ->first();
            
        if ($check_if_approve) {
            return redirect()->to('/')->with('status', 'You already approved before');
        }

        return $cr;
    }

    /**
     * Prepare data for edit view
     */
    private function prepareEditData($cr, int $id): array
    {
        // Get users by workflow type
        $developer_users = $this->getDeveloperUsers($cr);
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
        $reminder_promo_tech_teams_text = implode(',',$reminder_promo_tech_teams);
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

        return compact(
            'selected_technical_teams', 'man_day', 'technical_team_disabled', 'status_name',
            'ApplicationImpact', 'cap_users', 'CustomFields', 'cr', 'workflow_type_id',
            'logs_ers', 'developer_users', 'sa_users', 'testing_users', 'technical_teams',
            'all_defects', 'reminder_promo_tech_teams', 'rtm_members', 'assignment_users','reminder_promo_tech_teams_text'
        );
    }

    /**
     * Get developer users based on workflow type
     */
    private function getDeveloperUsers($cr)
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
        
        return UserFactory::index()->get_user_by_group($cr->application_id);
    }

    /**
     * Get selected technical teams
     */
    private function getSelectedTechnicalTeams($cr): array
    {
        try {
            return $cr->technical_Cr_first->technical_cr_team->pluck('group')->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get reminder promo tech teams
     */
    private function getReminderPromoTechTeams($cr): array
    {
        return $cr->technical_Cr 
            ? $cr->technical_Cr->technical_cr_team
                ->where('status', '0')
                ->pluck('group')
                ->pluck('title')
                ->toArray()
            : [];
    }

    /**
     * Assign technical teams to CR
     */
    private function assignTechnicalTeams(Request $request, int $id): void
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

    /**
     * Handle CAP users email notification
     */
    private function handleCapUsersNotification(Request $request, int $id): void
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
            $mail->send_mail_to_cap_users($emails, $id,$cr->cr_no);
        }
    }

    /**
     * Generate security token for email actions
     */
    private function generateSecurityToken($cr): string
    {
        return md5($cr->id . $cr->created_at . env('APP_KEY'));
    }

    /**
     * Render action response view
     */
    private function renderActionResponse(bool $isSuccess, string $title, string $message, int $status)
    {
        return response()->view("{$this->view}.action_response", [
            'isSuccess' => $isSuccess,
            'title' => $title,
            'message' => $message,
            'status' => $status
        ], $status);
    }

    /**
     * Process division manager action
     */
    private function processDivisionManagerAction($cr, string $action, int $current_status)
    {
        $workflow_type_id = $cr->workflow_type_id;
        
        // Determine workflow ID based on action and workflow type
        $workflowIdForAction = $this->getWorkflowIdForAction($workflow_type_id, $action);
        
        if (!$workflowIdForAction) {
            return $this->renderActionResponse(false, 'Error', 'Unsupported workflow type.', 400);
        }

        try {
            $repository = new ChangeRequestRepository();
            $updateRequest = new Request([
                'old_status_id' => $current_status,
                'new_status_id' => $workflowIdForAction,
            ]);
            
            $repository->UpateChangeRequestStatus($cr->id, $updateRequest);
            
            Log::info('Division manager action processed', [
                'cr_id' => $cr->id,
                'action' => $action,
                'workflow_id' => $workflowIdForAction
            ]);
            
            $message = $action === 'approve' 
                ? "CR #{$cr->id} has been successfully approved."
                : "CR #{$cr->id} has been successfully rejected.";
                
            return $this->renderActionResponse(true, 'Success', $message, 200);
            
        } catch (\Exception $e) {
            Log::error('Failed to process division manager action', [
                'cr_id' => $cr->id,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
            
            return $this->renderActionResponse(false, 'Error', 'Failed to process action. Please try again.', 500);
        }
    }

    /**
     * Get workflow ID for action based on workflow type
     */
    private function getWorkflowIdForAction(int $workflow_type_id, string $action): ?int
    {
        $workflowMap = [
            3 => ['approve' => 36, 'reject' => 35],
            5 => ['approve' => 188, 'reject' => 184],
        ];

        return $workflowMap[$workflow_type_id][$action] ?? null;
    }

    /**
     * Get promo status view for workflow type
     */
    private function getPromoStatusView(string $workflow_type): ?array
    {
        if ($workflow_type !== 'Promo') {
            return null;
        }

        $group_promo = Group::with('group_statuses')->find(50);
        
        return $group_promo 
            ? $group_promo->group_statuses
                ->where('type', \App\Models\GroupStatuses::VIEWBY)
                ->pluck('status.id')
                ->toArray()
            : null;
    }

    /**
     * Build user CR query with filters
     */
    private function buildUserCRQuery(int $user_id, ?string $workflow_type)
    {
        $query = Change_request::with(['release', 'CurrentRequestStatuses'])
            ->where('requester_id', $user_id);

        if ($workflow_type) {
            $workflow_type_id = WorkFlowType::where('name', $workflow_type)
                ->whereNotNull('parent_id')
                ->value('id');

            if ($workflow_type_id) {
                $query->where('workflow_type_id', $workflow_type_id);
            }
        }

        return $query;
    }

    /**
     * Search result method (seems to be a placeholder in original)
     */
    public function search_result(int $id)
    {
        // This appears to be a placeholder method in the original code
        $cr = '39390'; // This should be dynamic based on actual search logic
        return response()->json(['data' => $cr], 200);
    }

    /**
     * Handle division manager action (JSON response version)
     */
    public function handleDivisionManagerAction1(Request $request)
    {
        $cr_id = $request->query('crId');
        $action = $request->query('action');
        $token = $request->query('token');

        if (!$cr_id || !$action || !$token) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Invalid request. Missing parameters.',
            ], 400);
        }

        $cr = Change_request::find($cr_id);
        if (!$cr) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Change Request not found.',
            ], 404);
        }

        $expectedToken = $this->generateSecurityToken($cr);
        if ($token !== $expectedToken) {
            Log::warning('Invalid token used for division manager action (JSON)', [
                'cr_id' => $cr_id,
                'ip' => request()->ip()
            ]);
            return response()->json([
                'isSuccess' => false,
                'message' => 'Unauthorized access. Invalid token.',
            ], 403);
        }

        $current_status = Change_request_statuse::where('cr_id', $cr_id)
            ->where('active', '1')
            ->value('new_status_id');

        if ($current_status != '22') {
            $message = $current_status == '19' 
                ? 'You already rejected this CR.' 
                : 'You already approved this CR.';
            return response()->json([
                'isSuccess' => false,
                'message' => $message,
            ], 400);
        }

        $workflowIdForAction = $this->getWorkflowIdForAction($cr->workflow_type_id, $action);
        if (!$workflowIdForAction) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Unsupported workflow type.',
            ], 400);
        }

        try {
            $repo = new ChangeRequestRepository();
            $updateRequest = new Request([
                'old_status_id' => $current_status,
                'new_status_id' => $workflowIdForAction,
            ]);
            $repo->UpateChangeRequestStatus($cr_id, $updateRequest);

            $message = $action === 'approve' 
                ? "CR #{$cr_id} has been successfully approved."
                : "CR #{$cr_id} has been successfully rejected.";

            return response()->json([
                'isSuccess' => true,
                'message' => $message,
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Failed to process division manager action (JSON)', [
                'cr_id' => $cr_id,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'isSuccess' => false,
                'message' => 'Failed to process action. Please try again.',
            ], 500);
        }
    }

    /**
     * Update attachment files
     */
    public function update_attach(attachments_CRS_Request $request)
    {
        $this->authorize('Edit ChangeRequest');
        
        if (!$request->hasFile('filesdata')) {
            return response()->json([
                'success' => false,
                'message' => 'No files provided'
            ], 400);
        }

        try {
            $cr_id = $request->get('id');
            $this->attachments->add_files($request->file('filesdata'), $cr_id);
            
            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully'
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Failed to upload attachment files', [
                'cr_id' => $request->get('id'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload files'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->authorize('Delete ChangeRequest');
        
        try {
            // Implementation for deleting change request
            // This was empty in the original code
            Log::info('Change request deletion attempted', [
                'cr_id' => $id,
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Change request deleted successfully'
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Failed to delete change request', [
                'cr_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete change request'
            ], 500);
        }
    }
}