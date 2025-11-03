<?php

namespace App\Http\Repository\ChangeRequest;

use App\Contracts\ChangeRequest\ChangeRequestRepositoryInterface;
// declare Entities
use App\Http\Repository\Logs\LogRepository;
use App\Http\Repository\NewWorkFlow\NewWorkflowRepository;
use App\Models\application;
use App\Models\change_request;
use App\Models\change_request_statuse;
use App\Models\GroupStatuses;
use App\Models\NewWorkFlow;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Log;

class ChangeRequestRepository implements ChangeRequestRepositoryInterface
{
    public function createOld($request)
    {
        return change_request::create($request)->id;
    }

    public function LastCRNo()
    {
        $ChangeRequest = change_request::orderby('id', 'desc')->first();

        return isset($ChangeRequest) ? $ChangeRequest->cr_no + 1 : 1;
    }

    public function ShowChangeRequestData($id, $group)
    { // $str = change_request::with('current_status.status.to_status_workflow.to_status')
        // $group = 10;

        $str = change_request::with(['current_status' => function ($q) use ($group) {
            $q->where('group_statuses.group_id', $group)->with('status.to_status_workflow');
        }])->where('id', $id)->get();

        // return Debugbar::info($str->toArray());
        return $str;
    }

    public function update($id, $request)
    {
        // dd($request->all());
        if ($request['assign_to']) {
            $user = User::find($request['assign_to']);
        } else {
            $user = Auth::user();
        }
        $change_request = change_request::find($id);
        /** check assignments */
        if ((isset($request['dev_estimation'])) || (isset($request['testing_estimation'])) || (isset($request['design_estimation'])) || ($request['assign_to'])) {
            $request['assignment_user_id'] = $user->id;
        }
        /** end check */
        $except = ['old_status_id', 'new_status_id', '_method', 'current_status', 'duration', 'current_status', 'categories', 'cat_name', 'pr_name', 'Applications', 'app_name', 'depend_cr_name', 'depend_crs', 'test', 'priorities', 'cr_id', 'assign_to', 'dev_estimation', 'design_estimation', 'testing_estimation', 'assignment_user_id', '_token'];
        if ((isset($request['dev_estimation']) && $request['dev_estimation'] != '') || (isset($request['design_estimation']) && $request['design_estimation'] != '') || (isset($request['testing_estimation']) && $request['testing_estimation'] != '')) {
            if ($user->role_id == 2) {
                // dd("dev");
                if ($change_request->workflow_type_id == 4) {
                    $request['testing_estimation'] = 1;
                }

                if (empty($change_request->design_duration) && empty($change_request->test_duration)) {

                    $request['develop_duration'] = $request['dev_estimation'];
                    $request['developer_id'] = $user->id;
                }
                if (! empty($change_request->design_duration) && ! empty($change_request->test_duration)) {
                    // public function GetLastEndDate($id, $user_id, $column, $last_end_date, $duration, $action)

                    $request['develop_duration'] = $request['dev_estimation'];
                    $request['developer_id'] = $user->id;
                    $dates = $this->GetLastEndDate($id, $request['developer_id'], 'developer_id', $change_request['end_design_time'], $request['develop_duration'], 'dev');
                    $request['start_develop_time'] = isset($dates[0]) ? $dates[0] : '';
                    $request['end_develop_time'] = isset($dates[1]) ? $dates[1] : '';

                    $dates = $this->GetLastEndDate($id, $change_request['tester_id'], 'tester_id', $request['end_develop_time'], $change_request['test_duration'], 'test');
                    $request['start_test_time'] = isset($dates[0]) ? $dates[0] : '';
                    $request['end_test_time'] = isset($dates[1]) ? $dates[1] : '';

                }
                if (empty($change_request->design_duration) && ! empty($change_request->test_duration)) {

                    $request['develop_duration'] = $request['dev_estimation'];
                    $request['developer_id'] = $user->id;
                    $dates = $this->GetLastEndDate($id, $request['developer_id'], 'developer_id', $change_request['end_design_time'], $request['develop_duration'], 'dev');
                    $request['start_develop_time'] = isset($dates[0]) ? $dates[0] : '';
                    $request['end_develop_time'] = isset($dates[1]) ? $dates[1] : '';

                }

            } elseif ($user->role_id == 3) {
                // dd("test");
                if (empty($change_request->design_duration) && empty($change_request->develop_duration)) {
                    $request['test_duration'] = $request['testing_estimation'];
                    $request['tester_id'] = $user->id;

                }
                if (! empty($change_request->design_duration) && ! empty($change_request->develop_duration)) {
                    $request['test_duration'] = $request['testing_estimation'];
                    $request['tester_id'] = $user->id;
                    $dates = $this->GetLastEndDate($id, $request['tester_id'], 'tester_id', $change_request['end_develop_time'], $request['test_duration'], 'dev');
                    $request['start_test_time'] = isset($dates[0]) ? $dates[0] : '';
                    $request['end_test_time'] = isset($dates[1]) ? $dates[1] : '';

                }
                if ((empty($change_request->design_duration) && ! empty($change_request->develop_duration)) || (! empty($change_request->design_duration) && empty($change_request->develop_duration))) {
                    $request['test_duration'] = $request['testing_estimation'];
                    $request['tester_id'] = $user->id;

                }

            } elseif ($user->role_id == 4) {
                if (empty($change_request->test_duration) && empty($change_request->develop_duration)) {
                    $request['design_duration'] = $request['design_estimation'];
                    $request['designer_id'] = $user->id;
                    $dates = $this->GetLastCRDate($id, $user->id, 'designer_id', 'end_design_time', $request['design_duration'], 'design');
                    $request['start_design_time'] = isset($dates[0]) ? $dates[0] : '';
                    $request['end_design_time'] = isset($dates[1]) ? $dates[1] : '';
                }

                if (! empty($change_request->test_duration) && ! empty($change_request->develop_duration)) {
                    $request['design_duration'] = $request['design_estimation'];
                    $request['designer_id'] = $user->id;
                    $dates = $this->GetLastCRDate($id, $user->id, 'designer_id', $request['end_design_time'], $request['design_duration'], 'design');
                    $request['start_design_time'] = isset($dates[0]) ? $dates[0] : '';
                    $request['end_design_time'] = isset($dates[1]) ? $dates[1] : '';

                    $dates = $this->GetLastEndDate($id, $change_request['developer_id'], 'developer_id', $request['end_design_time'], $change_request['test_duration'], 'dev');
                    $request['start_develop_time'] = isset($dates[0]) ? $dates[0] : '';
                    $request['end_develop_time'] = isset($dates[1]) ? $dates[1] : '';

                    $dates = $this->GetLastEndDate($id, $change_request['tester_id'], 'tester_id', $request['end_develop_time'], $change_request['develop_duration'], 'test');
                    $request['start_test_time'] = isset($dates[0]) ? $dates[0] : '';
                    $request['end_test_time'] = isset($dates[1]) ? $dates[1] : '';

                }
                if (empty($change_request->test_duration) && ! empty($change_request->develop_duration)) {

                    $request['design_duration'] = $request['design_estimation'];
                    $request['designer_id'] = $user->id;
                    $dates = $this->GetLastCRDate($id, $user->id, 'designer_id', 'end_design_time', $request['design_duration'], 'design');
                    $request['start_design_time'] = isset($dates[0]) ? $dates[0] : '';
                    $request['end_design_time'] = isset($dates[1]) ? $dates[1] : '';

                    $dates = $this->GetLastEndDate($id, $change_request['developer_id'], 'developer_id', $request['end_design_time'], $change_request['test_duration'], 'dev');
                    $request['start_develop_time'] = isset($dates[0]) ? $dates[0] : '';
                    $request['end_develop_time'] = isset($dates[1]) ? $dates[1] : '';

                }
                if (! empty($change_request->test_duration) && empty($change_request->develop_duration)) {
                    $request['design_duration'] = $request['design_estimation'];
                    $request['designer_id'] = $user->id;

                    $dates = $this->GetLastCRDate($id, $user->id, 'designer_id', 'end_design_time', $request['design_duration'], 'dev');
                    $request['start_design_time'] = isset($dates[0]) ? $dates[0] : '';
                    $request['end_design_time'] = isset($dates[1]) ? $dates[1] : '';

                    $request['start_develop_time'] = $request['end_design_time'];

                }

            }
        }

        $changeRequest = change_request::where('id', $id)->update($request->except($except));
        // $request['assignment_user_id'] = $user->id;
        $this->UpateChangeRequestStatus($id, $request);
        $this->StoreLog($id, $request, 'update');

        return $changeRequest;
    }

    public function GetLastCRDate($id, $user_id, $column, $end_date_column, $duration, $action)
    {

        $user = Auth::user();
        $last_end_date = change_request::where($column, $user_id)->where('id', '!=', $id)->max($end_date_column);
        if ($last_end_date == '' or $last_end_date < date('Y-m-d H:i:s')) {
            $new_start_date = date('Y-m-d H:i:s', strtotime('+3 hours'));
        } else {
            $new_start_date = date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($last_end_date)));
        }

        $new_start_date = date('Y-m-d H:i:s', $this->setToWorkingDate(strtotime($new_start_date)));
        // $new_start_date = date("Y-m-d H:i:s", strtotime('+3 hours', strtotime($new_start_date)));
        $new_end_date = $this->generate_end_date($this->setToWorkingDate(strtotime($new_start_date)), $duration, 0, $user->usr_id, $action);

        return [$new_start_date, $new_end_date];
    }

    // new updates

    // $dates = $this->GetLastEndDate($id,  $change_request['developer_id'], 'developer_id',  $request['end_design_time'],  $change_request['test_duration'] , 'dev');

    public function GetLastEndDate($id, $user_id, $column, $last_end_date, $duration, $action)
    {
        // $user = \Auth::user();
        //  $last_end_date = change_request::where($column, $user_id)->where('id', '!=', $id)->max($end_date_column);
        // if ($last_end_date == '' or $last_end_date < date('Y-m-d H:i:s')) {
        $new_start_date = date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($last_end_date)));
        // } else {
        // $new_start_date = date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($last_end_date)));
        // }

        $new_start_date = date('Y-m-d H:i:s', $this->setToWorkingDate(strtotime($new_start_date)));
        // $new_start_date = date("Y-m-d H:i:s", strtotime('+3 hours', strtotime($new_start_date)));
        $new_end_date = $this->generate_end_date($this->setToWorkingDate(strtotime($new_start_date)), $duration, 0, $user_id, $action);

        return [$new_start_date, $new_end_date];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  ,@param array $request
     * @return \Illuminate\Http\Response
     *                                   Hint: check if it is normal or not and has depend status or not
     */
    public function UpateChangeRequestStatus($id, $request)
    {
        /** check estimation user without changing in status */
        if (! isset($request->new_status) && isset($request->assignment_user_id)) {
            change_request_statuse::where('cr_id', $id)->where('new_status_id', $request->old_status_id)->where('active', '1')->update(['assignment_user_id' => $request->assignment_user_id]);
        }
        /**end  check estimation  */
        $workflow = NewWorkFlow::find($request->new_status_id);
        $user_id = Auth::user()->id;

        if ($workflow) {
            $workflow_active = $workflow->workflow_type == 1 ? '0' : '2';

            $cr_status = change_request_statuse::where('cr_id', $id)->where('new_status_id', $request->old_status_id)->where('active', '1')->first();
            $cr_status->active = $workflow_active;
            $cr_status->save();
            $depend_statuses = change_request_statuse::where('cr_id', $id)->where('old_status_id', $cr_status->old_status_id)->where('active', '1')->get();
            $active = '1';

            if ($workflow_active) { // check if it is normal work flow
                $check_depend_workflow = NewWorkFlow::whereHas('workflowstatus', function ($q) use ($workflow) {
                    $q->where('to_status_id', $workflow->workflowstatus[0]->to_status_id);
                })->pluck('from_status_id');
                $active = $depend_statuses->count() > 0 ? '0' : '1';
                $check_depend_status = change_request_statuse::where('cr_id', $id)->whereIN('new_status_id', $check_depend_workflow)->where('active', '1')->count();
                if ($check_depend_status > 0) {
                    $active = '0';
                }
            } else { // check if it is abnormal work flow
                foreach ($depend_statuses as $item) {
                    change_request_statuse::where('id', $item->id)->update(['active' => '0']);
                }
            }

            $change_request_status = new ChangeRequestStatusRepository();

            foreach ($workflow->workflowstatus as $key => $item) {
                $workflow_check_active = 0;

                if ($workflow->workflow_type != 1) {
                    $workflow_check_active = change_request_statuse::where('cr_id', $id)->where('new_status_id', $item->to_status_id)->where('active', '2')->first();
                }
                if (! $workflow_check_active) {
                    $data = [
                        'cr_id' => $id,
                        'old_status_id' => $request->old_status_id,
                        'new_status_id' => $item->to_status_id,
                        'user_id' => $user_id,
                        'active' => $active,
                        'assignment_user_id' => $request->assignment_user_id,
                    ];
                    $change_request_status->create($data);
                }
            }
        }

        return true;
    }

    public function StoreChangeRequestStatus($cr_id, $request)
    {
        $change_request_status = new ChangeRequestStatusRepository();
        $user_id = Auth::user()->id; // 3;
        $data = [
            'cr_id' => $cr_id,
            'old_status_id' => $request['old_status_id'],
            'new_status_id' => $request['new_status_id'],
            'user_id' => $user_id,
            'active' => '1',
        ];
        $change_request_status->create($data);

        return true;
    }

    public function getWorkFollowDependOnApplication($id)
    {
        $app = application::where('id', $id)->first();

        return $app->workflow_type_id;
    }

    public function create($request)
    {

        if ($request['workflow_type_id'] == 3) {
            $request['workflow_type_id'] = $this->getWorkFollowDependOnApplication($request['application_id']);
        }

        unset($request['active']);
        unset($request['testable']);
        $workflow = new NewWorkflowRepository();
        $defualt_satatus = $workflow->getFirstCreationStatus($request['workflow_type_id'])->from_status_id;
        // $defualt_satatus=3;
        $new_cr_id = $this->LastCRNo();
        $request['requester_id'] = Auth::user()->id;
        $request['requester_name'] = Auth::user()->user_name;
        $request['requester_email'] = Auth::user()->email;
        //  $request['active'] = $request['active'];
        $request['cr_no'] = $new_cr_id;
        $request['old_status_id'] = $defualt_satatus;
        $request['new_status_id'] = $defualt_satatus;
        $change_request = change_request::create($request);

        $this->StoreChangeRequestStatus($change_request->id, $request);

        $this->StoreLog($change_request->id, $request, 'create');

        return $change_request->id;
    }

    public function getAll($group = null)
    {

        if (empty($group)) {
            if (session('default_group')) {
                $group = session('default_group');

            } else {
                $group = auth()->user()->default_group;
            }

        }

        // $group = request()->header('group');
        // dd($group);
        $view_statuses = $this->getViewStatuses($group);

        $changeRequests = change_request::with('RequestStatuses.status')->whereHas('RequestStatuses', function ($query) use ($group, $view_statuses) {
            $query->where('active', '1')->whereIn('new_status_id', $view_statuses)

                ->whereHas('status.group_statuses', function ($query) use ($group) {
                    $query->where('group_id', $group);
                    $query->where('type', 2);
                });
        })->orderBy('id', 'DESC')->get();

        return $changeRequests;
    }

    public function delete($id)
    {
        return change_request::destroy($id);
    }

    public function find($id)
    {

        $groups = auth()->user()->user_groups->pluck('group_id')->toArray();
        $view_statuses = $this->getViewStatuses($groups);

        $changeRequest = change_request::with('category')->with('attachments',
            function ($q) use ($groups) {

                if (! in_array(8, $groups)) {
                    $q->whereHas('user', function ($q) {
                        if (Auth::user()->flag == '0') {
                            $q->where('flag', Auth::user()->flag);
                        }
                        $q->where('visible', 1);
                    });
                }
            }
        )->whereHas('RequestStatuses', function ($query) use ($groups, $view_statuses) {
            $query->where('active', '1')->whereIn('new_status_id', $view_statuses)
                ->whereHas('status.group_statuses', function ($query) use ($groups) {
                    // Check if the groups array does not contain group_id 19 or 8
                    if (! in_array(19, $groups) && ! in_array(8, $groups)) {
                        $query->whereIn('group_id', $groups);
                    }
                    $query->where('type', 2);
                });
        })->where('id', $id)->first();

        if ($changeRequest) {
            $changeRequest->current_status = $current_status = $this->getCurrentStatus($changeRequest, $view_statuses);
            $changeRequest->set_status = $this->GetSetStatus($current_status, $changeRequest->workflow_type_id);
        }

        $assigned_user = $this->AssignToUsers();
        if ($assigned_user) {
            $changeRequest->assign_to = $this->AssignToUsers();
        }

        return $changeRequest;
    }

    public function getViewStatuses($group = null)
    {
        // Get the default group if none is provided
        if (empty($group)) {
            $group = auth()->user()->default_group;
        }

        // Initialize the query for GroupStatuses
        $view_statuses = new GroupStatuses;

        // Check if $group is an array or a single value
        if (is_array($group)) {
            // If it's an array, apply the condition for all group IDs
            $view_statuses = $view_statuses->whereIn('group_id', $group)->where('type', 2);
        } else {
            // If it's a single value, apply the condition for that group
            if ($group != 19 && $group != 8) {
                $view_statuses = $view_statuses->where('group_id', $group)->where('type', 2);
            }
        }

        // Fetch and return the statuses related to the group(s)
        $view_statuses = $view_statuses->get()->pluck('status_id');

        return $view_statuses;
    }

    public function getCurrentStatus($changeRequest, $view_statuses)
    {
        $current_status = change_request_statuse::where('cr_id', $changeRequest->id)->whereIn('new_status_id', $view_statuses)->where('active', '1')->first();

        return $current_status;
    }

    public function GetSetStatus($current_status, $type_id)
    {
        $status_id = $current_status->new_status_id;
        $set_status = NewWorkFlow::where('from_status_id', $status_id)->whereHas('workflowstatus', function ($q) {
            $q->whereColumn('to_status_id', '!=', 'new_workflow.from_status_id');
        })->where('type_id', $type_id)->orderby('workflow_type', 'ASC')->get();
        // $set_status = 1;

        return $set_status;
    }

    public function AssignToUsers()
    {
        $user_id = Auth::user()->id;
        $assign_to = User::whereHas('user_report_to', function ($q) use ($user_id) {
            $q->where('report_to', $user_id)->where('user_id', '!=', $user_id);
        })->get();
        $assign_to = count($assign_to) > 0 ? $assign_to : null;

        return $assign_to;
    }
    // $dates = $this->GetLastCRDate($id, $user->id, 'tester_id', 'end_test_time', $request['test_duration'], 'test');

    public function GetLastCRDate($id, $user_id, $column, $end_date_column, $duration, $action)
    {
        $user = Auth::user();
        $last_end_date = change_request::where($column, $user_id)->where('id', '!=', $id)->max($end_date_column);
        if ($last_end_date == '' or $last_end_date < date('Y-m-d H:i:s')) {
            $new_start_date = date('Y-m-d H:i:s', strtotime('+3 hours'));
        } else {
            $new_start_date = date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($last_end_date)));
        }

        $new_start_date = date('Y-m-d H:i:s', $this->setToWorkingDate(strtotime($new_start_date)));
        // $new_start_date = date("Y-m-d H:i:s", strtotime('+3 hours', strtotime($new_start_date)));
        $new_end_date = $this->generate_end_date($this->setToWorkingDate(strtotime($new_start_date)), $duration, 0, $user->usr_id, $action);

        return [$new_start_date, $new_end_date];
    }

    public function GetLastCRPhase($id, $column, $end_date_column, $duration, $action)
    {
        $user = Auth::user();
        $last_end_date = change_request::where($column)->where('id', '=', $id)->max($end_date_column);

        // Check if $last_end_date is empty
        if (empty($last_end_date)) {
            // Return null for both start and end dates if $last_end_date is empty
            return [null, null];
        }

        // Determine the new start date based on $last_end_date
        if ($last_end_date == '' || $last_end_date < date('Y-m-d H:i:s')) {
            $new_start_date = date('Y-m-d H:i:s', strtotime('+3 hours'));
        } else {
            $new_start_date = date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($last_end_date)));
        }

        // Adjust start date to working hours
        $new_start_date = date('Y-m-d H:i:s', $this->setToWorkingDate(strtotime($new_start_date)));

        // Generate new end date
        $new_end_date = $this->generate_end_date($this->setToWorkingDate(strtotime($new_start_date)), $duration, 0, $user->usr_id, $action);

        return [$new_start_date, $new_end_date];
    }

    public function setToWorkingDate($date)
    {
        // If the start day sat it will be sun
        if (((int) date('w', $date)) == 6) { // sat = 6
            return strtotime(date('Y-m-d H:i:s', $date) . ' +1 days');
        }
        // If the start day fri it will be sun
        if (((int) date('w', $date)) == 5) { // friday = 5
            return strtotime(date('Y-m-d H:i:s', $date) . ' +2 days');
        }
        // Set date to be in the working hours
        if (((int) date('G', $date)) > 15 and ((int) date('G', $date)) < 24) {
            return strtotime(date('Y-m-d 08:00:00', $date) . ' +1 days');
        }
        if (((int) date('G', $date)) > 0 and ((int) date('G', $date)) < 8) {
            return strtotime(date('Y-m-d 08:00:00', $date));
        }

        return $date;
    }

    public function generate_end_date($start_date, $duration, $OnGoing, $user_id = 0, $action = 'dev')
    {
        //  Remove the weekend  days

        $man_power = 4;
        $man_power_ongoing = 4;

        $i = ($action == 'dev') ? ($duration * (int) (($OnGoing) ? (8 / $man_power_ongoing) : (8 / $man_power))) : $duration * 2;
        // $i = ($action == 'dev') ? ($duration * (($OnGoing) ? 8 : 4)) : $duration * 2 ;
        $time = $start_date;
        while ($i != 0) {
            $time = strtotime('+1 hour', $time);
            if (((int) date('w', $time)) < 5 and ((int) date('G', $time)) < 16 and ((int) date('G', $time)) > 8) { // friday = 5 & saturday = 6 and remove after 16:00 and before 08
                $i--;
            }
        }
        $end_date = date('Y-m-d H:i:s', $time);

        return $end_date;
    }

    public function StoreLog($id, $request, $type = 'create')
    {
        $workflow = null;
        $status_title = null;
        if (isset($request->new_status_id)) {
            $workflow = NewWorkFlow::find($request->new_status_id);
            if ($workflow->workflowstatus->count() > 1) {
                $status_title = $workflow->to_status_label;
            } else {
                $status_title = $workflow->workflowstatus[0]->to_status->status_name;
            }
        }

        $log = new LogRepository();
        if ($type == 'create') {
            $log_text = 'Issue opened by ' . Auth::user()->user_name;
            $data = [
                'cr_id' => $id,
                'user_id' => Auth::user()->id,
                'log_text' => $log_text,
            ];
            $log->create($data);
        } else {
            $log_text = "Issue manually set to status '$status_title' by " . Auth::user()->user_name;
            $data = [
                'cr_id' => $id,
                'user_id' => Auth::user()->id,
                'log_text' => $log_text,
            ];
            $log->create($data);

            if ($request->assign_to) {
                $assigned_user = User::find($request->assign_to);
                $log_text = "Issue assigned  manually to  '$assigned_user->user_name'  by " . Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if ($request->design_duration) {
                $log_text = "Issue design duration manually set to  '$request->design_duration H' by " . Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);

                $log_text = "Issue start design time set to  '$request->start_design_time' and end design time set to  '$request->end_design_time' by " . Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }
            if ($request->test_duration) {
                $log_text = "Issue design duration manually set to  '$request->test_duration H' by " . Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);

                $log_text = "Issue start test time set to  '$request->start_test_time' and end test time set to  '$request->end_test_time' by " . Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }
            if ($request->develop_duration) {
                $log_text = "Issue design duration manually set to  '$request->develop_duration H' by " . Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
                $log_text = "Issue start develop time set to  '$request->start_develop_time' and end develop time set to  '$request->end_develop_time' by " . Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }
        }

        return true;
    }

    public function searhchangerequest($id)
    {
        $user_flage = Auth::user()->flag;

        if ($user_flage == '0') {
            $changeRequest = change_request::where('id', $id)->where('requester_id', auth::user()->id)->first();
        } else {
            $changeRequest = change_request::where('id', $id)->first();
        }

        return $changeRequest;
    }

    public function my_assignments_crs()
    {
        $user_id = Auth::user()->id;
        $group = request()->header('group');
        $view_statuses = $this->getViewStatuses();
        $crs = change_request::with('RequestStatuses.status')->whereHas('RequestStatuses', function ($query) use ($user_id, $view_statuses) {
            $query->where('assignment_user_id', $user_id)
                ->where('active', '1')->whereIn('new_status_id', $view_statuses);

        })->get();

        return $crs;
    }

    public function my_crs()
    {
        $user_id = Auth::user()->id;
        $crs = change_request::where('requester_id', $user_id)->get();

        return $crs;
    }

    public function AdvancedSearchResult()
    {
        $request_query = request()->except('_token');

        $CRs = new change_request();

        foreach ($request_query as $key => $field_value) {
            // Check if $field_value is not null or empty
            if (! empty($field_value)) {
                switch ($key) {
                    case 'title':
                        $CRs = $CRs->where($key, 'LIKE', "%$field_value%");
                        break;
                    case 'created_at':
                        $CRs = $CRs->whereDate($key, '=', Carbon::createFromTimestamp($field_value / 1000)->format('Y-m-d'));
                        break;
                    case 'updated_at':
                        $CRs = $CRs->whereDate($key, '=', Carbon::createFromTimestamp($field_value / 1000)->format('Y-m-d'));
                        break;
                    case 'greater_than_date':
                        $CRs = $CRs->whereDate('updated_at', '>=', Carbon::createFromTimestamp($field_value / 1000)->format('Y-m-d'));
                        break;
                    case 'less_than_date':
                        $CRs = $CRs->whereDate('updated_at', '<=', Carbon::createFromTimestamp($field_value / 1000)->format('Y-m-d'));
                        break;
                    case 'uat_date':
                        $CRs = $CRs->whereDate('uat_date', '=', Carbon::createFromTimestamp($field_value / 1000)->format('Y-m-d'));
                        break;
                    case 'release_delivery_date':
                        $CRs = $CRs->whereDate('release_delivery_date', '=', Carbon::createFromTimestamp($field_value / 1000)->format('Y-m-d'));
                        break;
                    case 'release_receiving_date':
                        $CRs = $CRs->whereDate('release_receiving_date', '=', Carbon::createFromTimestamp($field_value / 1000)->format('Y-m-d'));
                        break;
                    case 'te_testing_date':
                        $CRs = $CRs->whereDate('te_testing_date', '=', Carbon::createFromTimestamp($field_value / 1000)->format('Y-m-d'));
                        break;
                    case 'status_id':
                        $CRs = $CRs->whereHas('CurrentRequestStatuses', function ($query) use ($field_value) {
                            $query->where('new_status_id', $field_value);
                        });
                        break;
                    case 'new_status_id':
                        $CRs = $CRs->whereHas('CurrentRequestStatuses', function ($query) use ($field_value) {
                            $query->where('new_status_id', $field_value);
                        });
                        break;
                    case 'assignment_user_id':
                        $CRs = $CRs->whereHas('CurrentRequestStatuses', function ($query) use ($field_value) {
                            $query->where('assignment_user_id', $field_value);
                            $query->where('active', 1);
                        });
                        break;
                    default:
                        $CRs = $CRs->where($key, $field_value);
                }
            }
        }

        DB::enableQueryLog();
        $results = $CRs->get();
        $queries = DB::getQueryLog();
        $lastQuery = end($queries);

        // Optionally: Log the last query
        Log::info('Last Query: ', $lastQuery);

        return $results;
    }
}
