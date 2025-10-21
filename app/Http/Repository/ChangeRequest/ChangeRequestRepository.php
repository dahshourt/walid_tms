<?php
namespace App\Http\Repository\ChangeRequest;

use App\Contracts\ChangeRequest\ChangeRequestRepositoryInterface;
use App\Models\Change_request;
use App\Models\Change_request_statuse;
use App\Services\ChangeRequest\{
    ChangeRequestCreationService,
    ChangeRequestUpdateService,
    ChangeRequestStatusService,
    ChangeRequestSchedulingService,
    ChangeRequestSearchService,
    ChangeRequestValidationService
};
use Auth;

class ChangeRequestRepository implements ChangeRequestRepositoryInterface
{
    protected $creationService;
    protected $updateService;
    protected $statusService;
    protected $schedulingService;
    protected $searchService;
    protected $validationService;

    public function __construct() {
        $this->creationService = new ChangeRequestCreationService();
        $this->updateService = new ChangeRequestUpdateService();
        $this->statusService = new ChangeRequestStatusService();
        $this->schedulingService = new ChangeRequestSchedulingService();
        $this->searchService = new ChangeRequestSearchService();
        $this->validationService = new ChangeRequestValidationService();
    }

    // Basic CRUD Operations
    public function findById(int $id): ?Change_request
    {
        return Change_request::find($id);
    }

    public function ListCRsByWorkflowType($workflow_type_id)
    {
        return Change_request::where('workflow_type_id', $workflow_type_id)->get();
    }

    public function LastCRNo()
    {
        $ChangeRequest = Change_request::orderby('id', 'desc')->first();
        return isset($ChangeRequest) ? $ChangeRequest->cr_no + 1 : 1;
    }

    public function AddCrNobyWorkflow($workflow_type_id): int
    {
        return $this->creationService->generateCrNumber($workflow_type_id);
    }

    public function create(array $data): array
    {
        return $this->creationService->create($data);
    }

    public function update($id, $request)
    {

        return $this->updateService->update($id, $request);
    }

    public function updateTestableFlag($id, $request)
    {
        return $this->updateService->updateTestableFlag($id, $request);
    }

    public function delete($id)
    {
        return Change_request::destroy($id);
    }

    // Listing methods
    public function getAll($group = null)
    {
        return $this->searchService->getAll($group);
    }

    public function getAllWithoutPagination($group = null)
    {
        return $this->searchService->getAllWithoutPagination($group);
    }

    public function dvision_manager_cr($group = null)
    {
        return $this->searchService->divisionManagerCr($group);
    }

    public function my_assignments_crs()
    {
        return $this->searchService->myAssignmentsCrs();
    }

    public function my_crs()
    {
        return $this->searchService->myCrs();
    }

    // Search methods
    public function find($id)
    {
        return $this->searchService->find($id);
    }

    public function findCr($id)
    {
        return $this->searchService->findCr($id);
    }

    public function AdvancedSearchResult($getall = 0)
    {
        return $this->searchService->advancedSearch($getall);
    }

    public function searhchangerequest($id)
    {
        return $this->searchService->searchChangeRequest($id);
    }

    // Status methods
    public function ShowChangeRequestData($id, $group)
    {
        return $this->searchService->showChangeRequestData($id, $group);
    }

    public function findWithReleaseAndStatus($id)
    {
        return $this->searchService->findWithReleaseAndStatus($id);
    }

    // Scheduling methods
    public function reorderTimes($crId)
    {
        return $this->schedulingService->reorderTimes($crId);
    }

    public function reorderChangeRequests($crId)
    {
        return $this->schedulingService->reorderChangeRequests($crId);
    }

    public function reorderCRQueues(string $crNumber)
    {
        return $this->schedulingService->reorderCRQueues($crNumber);
    }

    // Status update methods
    public function UpateChangeRequestStatus($id, $request)
    {

        return $this->statusService->updateChangeRequestStatus($id, $request);
    }

    public function UpateChangeRequestReleaseStatus($id, $request)
    {
        return $this->statusService->updateChangeRequestReleaseStatus($id, $request);
    }

    public function updateChangeRequestStatusForFinalConfirmation($id, $request)
    {
        return $this->statusService->updateChangeRequestStatusForFinalConfirmation($id, $request);
    }

    // Statistics methods
    public function CountCrsPerSystem($workflow_type)
    {
        $collection = Change_request::groupBy('application_id')
            ->selectRaw('count(*) as total, application_id')
            ->where('workflow_type_id', $workflow_type)
            ->get();
        return $collection;
    }

    public function CountCrsPerStatus()
    {
        $collection = Change_request_statuse::groupBy('new_status_id')
            ->selectRaw('count(*) as total, new_status_id')
            ->where('active', '1')
            ->get();
        return $collection;
    }

    public function CountCrsPerSystemAndStatus($workflow_type)
    {
        $collection = Change_request_statuse::whereHas('ChangeRequest', function ($q) use ($workflow_type) {
            $q->where('workflow_type_id', $workflow_type);
        })
        ->groupBy('new_status_id')
        ->selectRaw('count(*) as total, new_status_id')
        ->where('active', '1')
        ->get();

        return $collection;
    }

    // Workflow methods
    public function getWorkFollowDependOnApplication($id)
    {
        $app = Application::where('id', $id)->first();
        return $app->workflow_type_id;
    }

    public function get_change_request_by_release($release_id)
    {
        return Change_request::with("CurrentRequestStatuses")
            ->where('release_name', $release_id)
            ->where("workflow_type_id", 5)
            ->get();
    }

    // Calendar methods
    public function update_to_next_status_calendar()
    {
        // This is now handled by ProcessCalendarStatusUpdatesJob
        dispatch(new \App\Jobs\ChangeRequest\ProcessCalendarStatusUpdatesJob());
    }
}
