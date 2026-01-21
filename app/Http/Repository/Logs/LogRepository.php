<?php

namespace App\Http\Repository\Logs;

use App\Contracts\Logs\LogRepositoryInterface;
// declare Entities
use App\Models\Application;
use App\Models\Category;
use App\Models\Change_request;
use App\Models\CustomField;
use App\Models\DeploymentImpact;
use App\Models\DivisionManagers;
use App\Models\Log;
use App\Models\NeedDownTime;
use App\Models\NewWorkFlow;
use App\Models\NewWorkFlowStatuses;
use App\Models\Priority;
use App\Models\Rejection_reason;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use Auth;
use Illuminate\Support\Str;

class LogRepository implements LogRepositoryInterface
{
    public function getAll()
    {
        return Log::all();
    }

    public function create($request)
    {
        return Log::create($request);
    }

    public function delete($id)
    {
        return Log::destroy($id);
    }

    public function update($request, $id)
    {
        return Log::where('id', $id)->update($request);
    }

    public function find($id)
    {
        return Log::find($id);
    }

    public function get_by_cr_id($id)
    {
        return Log::where('cr_id', $id)->get();
    }

    public function updateactive($active, $id)
    {
        if ($active) {
            return $this->update(['active' => '0'], $id);
        }

        return $this->update(['active' => '1'], $id);

    }

    public function logCreate($id, $request, $changeRequest_old, $type = 'create'): bool
    {
        $log = new self();
        // $user_id = $request->user_id ? $request->user_id : \Auth::user()->id;

        if ($request instanceof \Illuminate\Support\Collection) {
            $user_id = $request->get('user_id', Auth::id());
        } elseif (is_array($request)) {
            $user_id = $request['user_id'] ?? Auth::id();
        } elseif ($request instanceof \Illuminate\Http\Request) {
            $user_id = $request->input('user_id', Auth::id());
        } else {
            $user_id = Auth::id();
        }

        $user = User::find($user_id);

        $change_request = $changeRequest_old;

        if ($type === 'create') {
            $this->createLog($log, $id, $user->id, "Change Request Created By '$user->user_name'");

            $new_status_id = data_get($request, 'new_status_id');

            $change_request = new Change_request();

            $log_message = $this->prepareCRStatusLogMessage($new_status_id, $change_request, $user, 'create');

            $this->createLog($log, $id, $user->id, $log_message);

            return true;
        }
        if ($type === 'shifting') {
            $this->createLog($log, $id, $user->id, 'Change Request shifted by admin : ' . $user->user_name);

            return true;
        }

        $fields = [
            // 'analysis_feedback' => 'Analysis FeedBack',
            // 'comment' => 'Comment',
            'priority_id' => ['model' => Priority::class, 'field' => 'name', 'message' => 'Change Request Priority Changed To'],
            // 'technical_feedback' => 'Technical Feedback Is',
            'unit_id' => ['model' => Unit::class, 'field' => 'name', 'message' => 'Change Request Assigned To Unit'],
            // 'creator_mobile_number' => 'Creator Mobile Changed To',
            // 'title' => 'Subject Changed To',
            'application_id' => ['model' => Application::class, 'field' => 'name', 'message' => 'Change Request Title Changed To'],
            // 'description' => 'CR Description To',
            'category_id' => ['model' => Category::class, 'field' => 'name', 'message' => 'Change Request Category Changed To'],
            'division_manager_id' => ['model' => DivisionManagers::class, 'field' => 'name', 'message' => 'Division Managers To'],
            'need_down_time' => ['model' => NeedDownTime::class, 'field' => 'name', 'message' => 'Change Request Need down time Changed To'],
            'rejection_reason_id' => ['model' => Rejection_reason::class, 'field' => 'name', 'message' => 'Change Request rejection Reason Changed To'],
            'deployment_impact' => ['model' => DeploymentImpact::class, 'field' => 'name', 'message' => 'Change Request Deployment Impact Changed To'],
        ];

        $excludeNames = ['new_status_id', 'testing_estimation', 'design_estimation', 'dev_estimation'];

        // fetch custom fields you want to append
        $customFields = CustomField::query()
            ->whereNotIn('name', $excludeNames)
            ->get();

        $cf_default_log_message = ":cf_label Changed To ':cf_value' by :user_name";

        $customFieldMap = $customFields->mapWithKeys(function ($cf) use ($request, $cf_default_log_message, $user) {

            if (! $request->{$cf->name} || ! array_key_exists($cf->name, $request->all())) {
                return [];
            }

            // Fallback message if label is null
            $label = $cf->label ?: Str::of($cf->name)->replace('_', ' ')->title();
            $cf_log_message = $cf->log_message ?? $cf_default_log_message;

            $base = [];

            if (in_array($cf->type, ['multiselect', 'select'], true)) {
                $data = $cf->getSpecificCustomFieldValues((array) $request->{$cf->name});

                $value = $data?->implode(', ');
            }

            if ($cf->name === 'testable') {
                $value = $request->get('testable') === '1' ? 'Testable' : 'Non Testable';
            }

            if (in_array($cf->name, ['technical_attachments', 'business_attachments'], true)) {
                $files_name = [];
                $attachments = $request->file($cf->name, []);

                foreach ($attachments as $attachment) {
                    $files_name[] = $attachment->getClientOriginalName();
                }

                $value = implode(', ', $files_name);
            }

            if ($cf->name === 'depend_on') {
                // For depend_on, use the CR Numbers directly
                $value = is_array($request->depend_on) ? implode(', ', array_filter($request->depend_on)) : $request->depend_on;
            }

            $base['message'] = trans($cf_log_message, [
                'cf_label' => $label,
                'cf_value' => $value ?? $request->{$cf->name},
                'user_name' => $user->user_name,
            ]);

            $base['already_has_message'] = true;

            return [$cf->name => $base];
        })->toArray();

        $all_logs = [];

        // append without overriding existing keys in $fields
        $fields += $customFieldMap;
        foreach ($fields as $field => $info) {
            if (isset($request->$field)) {
                if ($field === 'kpi') {
                    $oldValue = $change_request->kpis->first()->id ?? null;
                    $newValue = $request->kpi;
                } elseif ($field === 'depend_on') {
                    $oldValue = $change_request->dependencies
                        ->where('pivot.status', '0')
                        ->pluck('id')
                        ->toArray();
                    $newValue = $request->depend_on;
                    // Normalize to arrays
                    if (! is_array($oldValue)) {
                        $oldValue = [];
                    }
                    if (! is_array($newValue)) {
                        $newValue = [];
                    }
                } elseif ($field === 'cr_type') {
                    $oldValue = $change_request->changeRequestCustomFields->where('custom_field_name', 'cr_type')->first()?->custom_field_value;
                    $newValue = $request->cr_type;
                } elseif (in_array($field, ['designer_id', 'assignment_user_id', 'design_estimation', 'dev_estimation', 'testing_estimation', 'cr_member'], true)) {
                    $oldValue = $change_request->changeRequestCustomFields->where('custom_field_name', $field)->last()?->custom_field_value;
                    $newValue = $request->{$field};
                } else {
                    $oldValue = $change_request->$field ?? null;
                    $newValue = $request->$field;
                }

                if ($oldValue != $newValue) {

                    if (is_array($info)) {
                        if (isset($info['model'])) {
                            $modelName = $info['model'];
                            $fieldName = $info['field'];
                            $valueName = $modelName::find($newValue)?->$fieldName;
                            $message = $info['message'] . " '$valueName' By '$user->user_name'";

                            $all_logs[] = $message;
                        } elseif (array_key_exists('already_has_message', $info)) {
                            $all_logs[] = $info['message'];
                        }
                    }
                }
            }
        }

        // Store all logs
        $this->createMultipleLogs($change_request->id, $user->id, $all_logs);

        // Boolean Toggles
        $this->logToggle($log, $id, $user->id, $request, $change_request, 'postpone', 'CR PostPone changed To');
        $this->logToggle($log, $id, $user->id, $request, $change_request, 'need_ux_ui', 'CR Need UI UX changed To');

        // User Assignments
        $assignments = [
            'assign_to' => 'Change Request assigned manually to',
        ];

        $assignment_logs = [];

        foreach ($assignments as $field => $label) {
            if (isset($request->$field)) {
                // TODO: Take this query out of the foreach
                $assignedUser = User::find($request->$field);
                if ($assignedUser) {
                    $assignment_logs[] = "$label '{$assignedUser->user_name}' by {$user->user_name}";
//                    $this->createLog($log, $id, $user->id, "$label '{$assignedUser->user_name}' by {$user->user_name}");
                }
            }
        }

        $this->createMultipleLogs($id, $user->id, $assignment_logs);

        // Estimations without assignments

        $this->logEstimateWithoutAssignee($log, $id, $user, $request, 'design_duration', 'design_duration', 'Design');
        $this->logEstimateWithoutAssignee($log, $id, $user, $request, 'develop_duration', 'developer_id', 'Dev');
        $this->logEstimateWithoutAssignee($log, $id, $user, $request, 'test_duration', 'tester_id', 'Testing');

        // Durations with times
        $this->logDurationWithTimes($log, $id, $user, $request, 'design_duration', 'start_design_time', 'end_design_time');
        $this->logDurationWithTimes($log, $id, $user, $request, 'develop_duration', 'start_develop_time', 'end_develop_time');
        $this->logDurationWithTimes($log, $id, $user, $request, 'test_duration', 'start_test_time', 'end_test_time');

        // Status change
        if (isset($request->new_status_id)) {
            // echo $request->new_status_id; die;
            $workflow = NewWorkFlow::find($request->new_status_id);

            $status_title = null;
            if ($workflow && ! empty($workflow->to_status_label)) {
                $status_title = $workflow->to_status_label;
            }

            if ($status_title && $request->missing('hold') && $request->missing('is_final_confirmation')) {
                // Dependency Release Log (when the depend cr reach to the status delivered or reject)
                if ($request->released_from_hold) {
                    $newStatusesIds = NewWorkFlowStatuses::where('new_workflow_id', $request->new_status_id)->pluck('to_status_id')->toArray();
                    $newStatusesNames = Status::whereIn('id', $newStatusesIds)->pluck('status_name')->toArray();
                    $actualStatuses = implode(', ', $newStatusesNames);

                    $this->createLog($log, $id, $user->id, "Change request status has been released by {$user->user_name} and the current status is $actualStatuses");
                }
                // Dependency Hold Log
                elseif ($change_request->fresh()->is_dependency_hold) {
                    $newStatusesIds = NewWorkFlowStatuses::where('new_workflow_id', $request->new_status_id)->pluck('to_status_id')->toArray();
                    $newStatusesNames = Status::whereIn('id', $newStatusesIds)->pluck('status_name')->toArray();
                    $actualStatuses = implode(', ', $newStatusesNames);

                    $blockingCrs = \App\Models\CrDependency::where('cr_id', $id)
                        ->active()
                        ->with('dependsOnCr:id,cr_no')
                        ->get()
                        ->pluck('dependsOnCr.cr_no')
                        ->filter()
                        ->implode(', ');

                    $this->createLog($log, $id, $user->id, "Change Request Status changed to '$actualStatuses' by {$user->user_name} (Pending Dependency (CR#$blockingCrs))");
                }
                // Normal Status Log
                else {
                    $log_message = $this->prepareCRStatusLogMessage($request->new_status_id, $change_request, $user);

                    $this->createLog($log, $id, $user->id, $log_message);
                }
            } else {
                $log_message = $this->prepareCRStatusLogMessage($request->new_status_id, $change_request, $user);

                if ($request->has('is_final_confirmation')) {
                    $log_message = "$log_message from Administration";
                }

                $this->createLog($log, $id, $user->id, $log_message);
            }
            /*
            $workflow = NewWorkFlow::find($request->new_status_id);
            $status_title = $workflow->workflowstatus->count() > 1
                ? $workflow->to_status_label
                : $workflow->workflowstatus[0]->to_status->status_name;
            */
            // $this->createLog($log, $id, $user->id, "Change Request Status changed to '$status_title' by {$user->user_name}");
        }

        if ($request->hold === 1) {
            $this->createLog($log, $id, $user->id, "Change Request Held by $user->user_name");
        } elseif ($request->hold === 0) {
            $this->createLog($log, $id, $user->id, "Change Request unheld by $user->user_name");
        }

        return true;
    }

    private function createLog($logRepo, $crId, $userId, $message): void
    {
        $this->create([
            'cr_id' => $crId,
            'user_id' => $userId,
            'log_text' => $message,
        ]);
    }

    private function logToggle($logRepo, $crId, $userId, $request, $old, $field, $messagePrefix)
    {
        if (isset($request->$field) && $request->$field != $old->$field) {
            $status = $request->$field == 1 ? 'Active' : 'InActive';
            $this->createLog($logRepo, $crId, $userId, "$messagePrefix $status BY " . Auth::user()->user_name);
        }
    }

    private function logEstimateWithoutAssignee($logRepo, $crId, $user, $request, $durationField, $assigneeField, $label)
    {
        if (isset($request->$durationField) && empty($request->$assigneeField)) {
            $log_message = "Change Request $label Estimated by {$user->user_name}";

            if (! $this->logExists($log_message, $crId)) {
                $this->createLog($logRepo, $crId, $user->id, $log_message);
            }
        }
    }

    private function logDurationWithTimes($logRepo, $crId, $user, $request, $durationField, $startField, $endField)
    {
        if (isset($request->$durationField)) {
            $cleaned_field = Str::of($durationField)->remove('_id')->replace('_', ' ')->title();
            $log_message = "Change Request $cleaned_field manually set to '{$request->$durationField} H' by {$user->user_name}";

            if (! $this->logExists($log_message, $crId)) {
                $this->createLog($logRepo, $crId, $user->id, $log_message);
            }
        }

        if (isset($request->$startField) && isset($request->$endField)) {
            $startLabel = Str::of($startField)->replace('_', ' ')->title();
            $endLabel = Str::of($endField)->replace('_', ' ')->title();

            $log_message = "Change Request $startLabel set to '{$request->$startField}' and $endLabel set to '{$request->$endField}' by {$user->user_name}";

            if (! $this->logExists($log_message, $crId)) {
                $this->createLog($logRepo, $crId, $user->id, $log_message);
            }

        }
    }

    private function logExists(string $log_message, string $crId): bool
    {
        return Log::where('cr_id', $crId)->where('log_text', $log_message)->exists();
    }

    private function prepareCRStatusLogMessage(int $status_id, Change_request $change_request, User $user, ?string $stage = null): string
    {
        $default_status_log_message = "Change Request Status changed to ':status_name' By ':user_name'";

        if ($stage === 'create') {
            $status = Status::findOrFail($status_id);

            $status_name = $status?->status_name;
            $log_message = $status->log_message ?? $default_status_log_message;
        } else {
            $newStatusesIds = NewWorkFlowStatuses::where('new_workflow_id', $status_id)->pluck('to_status_id')->toArray();
            $statuses = Status::whereIn('id', $newStatusesIds)->toBase()->get(['status_name', 'log_message']);

            $status_name = $statuses?->pluck('status_name')->unique()->implode(', ');
            $log_message = $statuses->whereNotNull('log_message')->first()->log_message ?? $default_status_log_message;
        }

        return trans($log_message, [
            'status_name' => $status_name,
            'user_name' => $user->user_name,
        ]);
    }

    private function createMultipleLogs(int $crId, int $userId, array $logs): void
    {
        if (count($logs) === 0) {
            return;
        }

        $formated_logs = [];
        $now = now();

        foreach ($logs as $log_message) {
            $formated_logs[] = [
                'cr_id' => $crId,
                'user_id' => $userId,
                'log_text' => $log_message,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        Log::insert($formated_logs);
    }
}
