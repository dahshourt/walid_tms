<?php
namespace App\Services\ChangeRequest;

use App\Models\{
    Change_request, 
    Group, 
    GroupStatuses, 
    Change_request_statuse, 
    User, 
    TechnicalCr,
    NewWorkFlow
};
use Carbon\Carbon;
use Auth;

class ChangeRequestSearchService
{
    public function getAll($group = null)
    {
        $group = $this->resolveGroup($group);
        $groupData = Group::find($group);
        $groupApplications = $groupData->group_applications->pluck('application_id')->toArray();
        $viewStatuses = $this->getViewStatuses($group);

        $changeRequests = Change_request::with('RequestStatuses.status');
        
        if ($groupApplications) {
            $changeRequests = $changeRequests->whereIn('application_id', $groupApplications);
        }
        
        $changeRequests = $changeRequests->whereHas('RequestStatuses', function ($query) use ($group, $viewStatuses) {
            $query->where('active', '1')
                  ->whereIn('new_status_id', $viewStatuses)
                  ->whereHas('status.group_statuses', function ($query) use ($group) {
                      $query->where('group_id', $group)
                            ->where('type', 2);
                  });
        })->orderBy('id', 'DESC')->paginate(20);

        return $changeRequests;
    }

    public function getAllWithoutPagination($group = null)
    {
        $group = $this->resolveGroup($group);
        $groupData = Group::find($group);
        $groupApplications = $groupData->group_applications->pluck('application_id')->toArray();
        $viewStatuses = $this->getViewStatuses($group);

        $changeRequests = Change_request::with('RequestStatuses.status');
        
        if ($groupApplications) {
            $changeRequests = $changeRequests->whereIn('application_id', $groupApplications);
        }
        
        $changeRequests = $changeRequests->whereHas('RequestStatuses', function ($query) use ($group, $viewStatuses) {
            $query->where('active', '1')
                  ->whereIn('new_status_id', $viewStatuses)
                  ->whereHas('status.group_statuses', function ($query) use ($group) {
                      $query->where('group_id', $group)
                            ->where('type', 2);
                  });
        })->orderBy('id', 'DESC')->get();

        return $changeRequests;
    }

    public function divisionManagerCr($group = null)
    {
        $userEmail = auth()->user()->email;
        $group = $this->resolveGroup($group);

        // Load up to 50 requests for manual filtering
        $allRequests = Change_request::with(['RequestStatuses.status'])
            ->where('division_manager', $userEmail)
            ->orderBy('id', 'DESC')
            ->limit(50)
            ->get();

        // Use full PHP-based filtering with getCurrentStatus()
        $filtered = $allRequests->filter(function ($item) {
            $status = $item->getCurrentStatus();
            return $status && $status->status && $status->status->id == config('change_request.status_ids.business_approval');
        });

        // Manual pagination
        $perPage = request()->get('per_page', 10);
        $page = request()->get('page', 1);
        
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $filtered->forPage($page, $perPage),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginated;
    }

    public function myAssignmentsCrs()
    {
        $userId = Auth::user()->id;
        $group = $this->resolveGroup();
        $viewStatuses = $this->getViewStatuses();
        $viewStatuses[] = config('change_request.status_ids.cr_manager_review');

        $crs = Change_request::with('Req_status.status')
            ->whereHas('Req_status', function ($query) use ($userId, $viewStatuses) {
                $query->where('assignment_user_id', $userId)
                      ->whereIn('new_status_id', $viewStatuses);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->whereHas('CurrentRequestStatuses', function ($q) {
                    $q->where('new_status_id', config('change_request.status_ids.cr_manager_review'))
                      ->where('active', 1);
                })->orWhere('change_request.chnage_requester_id', $userId);
            })
            ->paginate(50);

        return $crs;
    }

    public function myCrs()
    {
        $userId = Auth::user()->id;
        return Change_request::where('requester_id', $userId)->get();
    }

    public function find($id)
    {
        $userEmail = strtolower(auth()->user()->email);
        $divisionManager = strtolower(Change_request::where('id', $id)->value('division_manager'));

        $groups = ($userEmail === $divisionManager) 
            ? Group::pluck('id')->toArray()
            : auth()->user()->user_groups->pluck('group_id')->toArray();

        $promoGroups = [50];
        $groups = array_merge($groups, $promoGroups);

        $groupPromo = Group::with('group_statuses')->find(50);
        $statusPromoView = $groupPromo->group_statuses->where('type', \App\Models\GroupStatuses::VIEWBY)->pluck('status.id');

        $viewStatuses = $this->getViewStatuses($groups, $id);
        $viewStatuses = $statusPromoView->merge($viewStatuses)->unique();
        $viewStatuses->push(config('change_request.status_ids.cr_manager_review'));

        $changeRequest = Change_request::with('category')
            ->with('attachments', function ($q) use ($groups) {
                $q->with('user');
                if (!in_array(8, $groups)) {
                    $q->whereHas('user', function ($q) {
                        if (Auth::user()->flag == '0') {
                            $q->where('flag', Auth::user()->flag);
                        }
                        $q->where('visible', 1);
                    });
                }
            })
            ->whereHas('RequestStatuses', function ($query) use ($groups, $viewStatuses) {
                $query->where('active', '1')
                      ->whereIn('new_status_id', $viewStatuses)
                      ->whereHas('status.group_statuses', function ($query) use ($groups) {
                          if (!in_array(19, $groups) && !in_array(8, $groups)) {
                              $query->whereIn('group_id', $groups);
                          }
                          $query->where('type', 2);
                      });
            })
            ->where('id', $id)
            ->first();

        if ($changeRequest) {
            $currentStatus = $this->getCurrentStatus($changeRequest, $viewStatuses);
            $changeRequest->current_status = $currentStatus;
            $changeRequest->set_status = $this->getSetStatus($currentStatus, $changeRequest->workflow_type_id);
            
            if ($assignedUser = $this->getAssignToUsers()) {
                $changeRequest->assign_to = $assignedUser;
            }
        }

        return $changeRequest;
    }

    public function findCr($id)
    {
        $groups = auth()->user()->user_groups->pluck('group_id')->toArray();
        $viewStatuses = $this->getViewStatuses($groups);
        
        $changeRequest = Change_request::with(['category', 'defects'])
            ->with('attachments', function ($q) use ($groups) {
                $q->with('user');
                if (!in_array(8, $groups)) {
                    $q->whereHas('user', function ($q) {
                        if (Auth::user()->flag == '0') {
                            $q->where('flag', Auth::user()->flag);
                        }
                        $q->where('visible', 1);
                    });
                }
            })
            ->where('id', $id)
            ->first();

        if ($changeRequest) {
            $changeRequest->current_status = $current_status = $this->getCurrentStatusCab($changeRequest, $viewStatuses);
            $changeRequest->set_status = $this->getSetStatus($current_status, $changeRequest->workflow_type_id);
        }

        $assigned_user = $this->getAssignToUsers();
        if ($assigned_user) {
            $changeRequest->assign_to = $assigned_user;
        }

        return $changeRequest;
    }

    public function advancedSearch($getAll = 0)
    {
        $requestQuery = request()->except('_token', 'page');
        $crs = new Change_request();

        foreach ($requestQuery as $key => $fieldValue) {
            if (!empty($fieldValue)) {
                $crs = $this->applySearchFilter($crs, $key, $fieldValue);
            }
        }

        \DB::enableQueryLog();
        $results = $getAll == 0 ? $crs->paginate(10) : $crs->get();
        $queries = \DB::getQueryLog();
        $lastQuery = end($queries);
        \Log::info('Last Query: ', $lastQuery);

        return $results;
    }

    public function searchChangeRequest($id)
    {
        $userFlag = Auth::user()->flag;
        return Change_request::with('Release')
            ->where('id', $id)
            ->orWhere('cr_no', $id)
            ->first();
    }

    public function showChangeRequestData($id, $group)
    {
        return Change_request::with(['current_status' => function ($q) use ($group) {
            $q->where('group_statuses.group_id', $group)->with('status.to_status_workflow');
        }])->where('id', $id)->get();
    }

    public function findWithReleaseAndStatus($id)
    {
        return Change_request::with('release')->find($id);
    }

    protected function applySearchFilter($query, $key, $value)
    {
        switch ($key) {
            case 'id':
                return $query->where(function($q) use ($value) {
                    $q->where('id', $value)->orWhere('cr_no', $value);
                });
            case 'title':
                return $query->where($key, 'LIKE', "%$value%");
            case 'created_at':
            case 'updated_at':
            case 'uat_date':
            case 'release_delivery_date':
            case 'release_receiving_date':
            case 'te_testing_date':
                return $query->whereDate($key, '=', Carbon::createFromTimestamp($value / 1000)->format('Y-m-d'));
            case 'greater_than_date':
                return $query->whereDate('updated_at', '>=', Carbon::createFromTimestamp($value / 1000)->format('Y-m-d'));
            case 'less_than_date':
                return $query->whereDate('updated_at', '<=', Carbon::createFromTimestamp($value / 1000)->format('Y-m-d'));
            case 'status_id':
            case 'new_status_id':
                return $query->whereHas('CurrentRequestStatuses', function ($q) use ($value) {
                    $q->where('new_status_id', $value);
                });
            case 'assignment_user_id':
                return $query->whereHas('CurrentRequestStatuses', function ($q) use ($value) {
                    $q->where('assignment_user_id', $value)->where('active', 1);
                });
            default:
                return $query->where($key, $value);
        }
    }

    protected function resolveGroup($group = null)
    {
        if (!empty($group)) {
            return $group;
        }

        return session('default_group') ?: auth()->user()->default_group;
    }

    protected function getViewStatuses($group = null, $id = null): array
    {
        $userEmail = strtolower(auth()->user()->email);
        $divisionManager = $id ? strtolower(Change_request::where('id', $id)->value('division_manager')) : null;
        $currentStatus = $id ? Change_request_statuse::where('cr_id', $id)->where('active', '1')->value('new_status_id') : null;

        $group = $this->resolveGroup($group);

        // Check if user is division manager and status is business approval
        if ($userEmail === $divisionManager && $currentStatus == config('change_request.status_ids.business_approval')) {
            $group = Group::pluck('id')->toArray();
        }

        $viewStatuses = new GroupStatuses();

        if (is_array($group)) {
            $viewStatuses = $viewStatuses->whereIn('group_id', $group)->where('type', 2);
        } else {
            $viewStatuses = $viewStatuses->where('group_id', $group)->where('type', 2);
        }

        $viewStatuses = $viewStatuses->groupBy('status_id')->get()->pluck('status_id')->toArray();
        
        // Handle technical team status
        if ($id) {
            $technicalCrTeamStatus = $this->getTechnicalTeamCurrentStatus($id);
            if ($technicalCrTeamStatus && in_array($technicalCrTeamStatus->current_status_id, $viewStatuses)) {
                $viewStatuses = [$technicalCrTeamStatus->current_status_id];
            }
        }

        return $viewStatuses;
    }

    protected function getTechnicalTeamCurrentStatus($id)
    {
        $group = $this->resolveGroup();
        $technicalCr = TechnicalCr::where("cr_id", $id)->where('status', '0')->first();
        
        if ($technicalCr) {
            return $technicalCr->technical_cr_team()
                ->where('group_id', $group)
                ->where('status', '0')
                ->first();
        }

        return null;
    }

    protected function getCurrentStatus($changeRequest, $viewStatuses)
    {
        return Change_request_statuse::where('cr_id', $changeRequest->id)
            ->whereIn('new_status_id', $viewStatuses)
            ->where('active', '1')
            ->first();
    }

    protected function getCurrentStatusCab($changeRequest, $viewStatuses)
    {
        return Change_request_statuse::where('cr_id', $changeRequest->id)
            ->where('active', '1')
            ->first();
    }

    protected function getSetStatus($currentStatus, $typeId)
    {
        if (!$currentStatus) {
            return collect();
        }

        $statusId = $currentStatus->new_status_id;
        $previousStatusId = $currentStatus->old_status_id;
        
        return NewWorkFlow::where('from_status_id', $statusId)
            ->where(function($query) use ($previousStatusId) {
                $query->whereNull('previous_status_id')
                      ->orWhere('previous_status_id', 0)
                      ->orWhere('previous_status_id', $previousStatusId);
            })
            ->whereHas('workflowstatus', function ($q) {
                $q->whereColumn('to_status_id', '!=', 'new_workflow.from_status_id');
            })
            ->where('type_id', $typeId)
            ->where('active', '1')
            ->orderBy('id', 'DESC')
            ->get();
    }

    protected function getAssignToUsers()
    {
        $userId = Auth::user()->id;
        $assignTo = User::whereHas('user_report_to', function ($q) use ($userId) {
            $q->where('report_to', $userId)->where('user_id', '!=', $userId);
        })->get();
        
        return count($assignTo) > 0 ? $assignTo : null;
    }
}