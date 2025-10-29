<?php
namespace App\Services\ChangeRequest;

use App\Models\{Change_request, CustomField, ChangeRequestCustomField};
use App\Http\Repository\{
    Logs\LogRepository,
    ChangeRequest\ChangeRequestStatusRepository,
    NewWorkFlow\NewWorkflowRepository
};
use App\Http\Controllers\Mail\MailController;
use App\Traits\ChangeRequest\ChangeRequestConstants;
use Auth;
use Illuminate\Support\Arr;
use App\Events\ChangeRequestCreated;

class ChangeRequestCreationService
{
    use ChangeRequestConstants;

    protected $logRepository;
    protected $changeRequestStatusRepository;

    public function __construct()
    {
        $this->logRepository = new LogRepository();
        $this->changeRequestStatusRepository = new ChangeRequestStatusRepository();
        //$this->workflowRepository = new NewWorkflowRepository();
    }

    public function create(array $data): array
    {
        $customFieldData = $data;
		$workflow = new NewWorkflowRepository();
        $defaultStatus = $workflow->getFirstCreationStatus($data['workflow_type_id'])->from_status_id;
        
        $preparedData = $this->prepareCreateData($data, $defaultStatus);
        $changeRequest = Change_request::create($preparedData);

        $statusData = $this->prepareStatusData($customFieldData, $defaultStatus);
        
        $this->handleCustomFields($changeRequest->id, $statusData);
        $this->changeRequestStatusRepository->createInitialStatus($changeRequest->id, $statusData);
        
        $this->logRepository->logCreate($changeRequest->id, $statusData, null, 'create');
        
        //$this->sendCreationEmails($changeRequest, $statusData);
        event(new ChangeRequestCreated($changeRequest, $statusData));

        return [
            "id" => $changeRequest->id,
            "cr_no" => $changeRequest->cr_no,
        ];
    }

    protected function prepareCreateData(array $data, int $defaultStatus): array
    {
        $data['requester_id'] = Auth::id();
        $data['requester_name'] = Auth::user()->user_name;
        $data['requester_email'] = Auth::user()->email;
        $data['cr_no'] = $this->generateCrNumber($data['workflow_type_id']);
        
        return Arr::only($data, $this->getRequiredFields());
    }

    protected function prepareStatusData(array $data, int $defaultStatus): array
    {
        $data['requester_id'] = Auth::id();
        $data['requester_name'] = Auth::user()->user_name;
        $data['requester_email'] = Auth::user()->email;
        $data['cr_no'] = $this->getLastCrNo();
        $data['old_status_id'] = $defaultStatus;
        $data['new_status_id'] = $defaultStatus;
        
        return Arr::except($data, []);
    }

    public function generateCrNumber($workflowTypeId): int
    {
        $changeRequest = Change_request::where('workflow_type_id', $workflowTypeId)
            ->orderBy('cr_no', 'desc')
            ->first();

        $firstCrNo = config('change_request.default_values.first_cr_no.' . $workflowTypeId, 
                           config('change_request.default_values.first_cr_no.default'));
        
        return $changeRequest && $changeRequest->cr_no ? $changeRequest->cr_no + 1 : $firstCrNo;
    }

    protected function getLastCrNo(): int
    {
        $changeRequest = Change_request::orderBy('id', 'desc')->first();
        return $changeRequest ? $changeRequest->cr_no + 1 : 1;
    }

    protected function handleCustomFields(int $crId, array $data): void
    {
        $excludedKeys = ["_token", "business_attachments", "technical_attachments"];
        
        foreach ($data as $key => $value) {
            if (!in_array($key, $excludedKeys) && $value) {
                $customField = CustomField::findId($key);
                if ($customField) {
                    ChangeRequestCustomField::updateOrCreate(
                        [
                            'cr_id' => $crId,
                            'custom_field_id' => $customField->id,
                            'custom_field_name' => $key
                        ],
                        [
                            'custom_field_value' => $value,
                            'user_id' => Auth::user()->id
                        ]
                    );
                }
            }
        }
    }

    protected function sendCreationEmails($changeRequest, $statusData): void
    {
        if (!config('change_request.mail_notifications.creation')) {
            return;
        }

        $mailController = new MailController();

        // Send mail to requester
        $mailController->notifyRequesterCrCreated(
            $statusData['requester_email'], 
            $changeRequest->id, 
            $changeRequest->cr_no
        );

        // Send mail to division manager if exists
        if (isset($statusData['division_manager'])) {
            $mailController->notifyDivisionManager(
                $statusData['division_manager'],
                $statusData['requester_email'],
                $changeRequest->id,
                $statusData['title'],
                $statusData['description'],
                $statusData['requester_name'],
                $changeRequest->cr_no
            );
        }
    }
}