<?php

namespace App\Http\Repository\Logs;

use App\Contracts\Logs\LogRepositoryInterface;
// declare Entities
use App\Models\Application;
use App\Models\Category;
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

    public function logCreate($id, $request, $changeRequest_old, $type = 'create')
    {
        $log = new LogRepository();
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
            $this->createLog($log, $id, $user->id, 'Change Request Created By ' . $user->user_name);

            $new_status_id =  data_get($request, 'new_status_id');

            $status_name = Status::where('id', $new_status_id)->value('status_name');

            $log_message = "Change Request Status changed to '$status_name' By '$user->user_name'";

            $this->createLog($log, $id, $user->id, $log_message);

            return true;
        }
        if ($type === 'shifting') {
            $this->createLog($log, $id, $user->id, 'CR shifted by admin : ' . $user->user_name);

            return true;
        }

        $fields = [
            // 'analysis_feedback' => 'Analysis FeedBack',
            // 'comment' => 'Comment',
            'testable' => ['message' => 'Testable flag changed to'],
            'priority_id' => ['model' => Priority::class, 'field' => 'name', 'message' => 'Priority Changed To'],
            // 'technical_feedback' => 'Technical Feedback Is',
            'unit_id' => ['model' => Unit::class, 'field' => 'name', 'message' => 'CR Assigned To Unit'],
            // 'creator_mobile_number' => 'Creator Mobile Changed To',
            // 'title' => 'Subject Changed To',
            'application_id' => ['model' => Application::class, 'field' => 'name', 'message' => 'Title Changed To'],
            // 'description' => 'CR Description To',
            'category_id' => ['model' => Category::class, 'field' => 'name', 'message' => 'CR Category Changed To'],
            'division_manager_id' => ['model' => DivisionManagers::class, 'field' => 'name', 'message' => 'Division Managers To'],
            'need_down_time' => ['model' => NeedDownTime::class, 'field' => 'name', 'message' => 'Need down time Changed To'],
            'rejection_reason_id' => ['model' => Rejection_reason::class, 'field' => 'name', 'message' => 'rejection Reason Changed To'],
            'deployment_impact' => ['model' => DeploymentImpact::class, 'field' => 'name', 'message' => 'Deployment Impact Changed To'],
        ];
        $excludeNames = ['develop_duration', 'design_duration', 'test_duration', 'new_status_id'];

        // fetch custom fields you want to append
        $customFields = CustomField::query()
            ->whereNotIn('name', $excludeNames)
            ->get();

        $customFieldMap = $customFields->mapWithKeys(function ($cf) use ($request) {

            if (! $request->{$cf->name} || ! array_key_exists($cf->name, $request->all())) {
                return [];
            }

            // Fallback message if label is null
            $label = $cf->label ?: Str::of($cf->name)->replace('_', ' ')->title();

            $message = "$label Changed To";
            $base = [];

            if ($cf->type === 'multiselect') {
                $data = $cf->getSpecificCustomFieldValues((array) $request->{$cf->name});
                $rest_of_message = $data?->implode(', ');
                $message .= " '$rest_of_message'";
                $base['already_has_message'] = true;
            } elseif ($cf->type === 'select') {
                $data = $cf->getSpecificCustomFieldValues((array) $request->{$cf->name});
                $rest_of_message = $data?->implode(', ');

                $message .= " '$rest_of_message'";
                $base['already_has_message'] = true;
            }

            $base['message'] = $message;

            // If the custom field is linked to another table, include that hint
            // (adjust 'field' if your related table uses another display column)
            if (! empty($cf->related_table)) {
                $base['table'] = $cf->related_table;
                $base['field'] = 'name';
            }

            return [$cf->name => $base];
        })->toArray();

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
                     if (!is_array($oldValue)) $oldValue = [];
                     if (!is_array($newValue)) $newValue = [];
                } elseif ($field === 'cr_type') {
                     $oldValue = $change_request->changeRequestCustomFields->where('custom_field_name', 'cr_type')->first()?->custom_field_value;
                     $newValue = $request->cr_type;
                } elseif (in_array($field, ['analysis_feedback', 'technical_feedback'], true)) {
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
                            $message = $info['message'] . " \"$valueName\"";
                        } elseif (array_key_exists('already_has_message', $info)) {
                            if ($field === 'depend_on') {
                                // For depend_on, use the CR Numbers directly
                                $displayValue = is_array($request->depend_on) ? implode(', ', array_filter($request->depend_on)) : $request->depend_on;
                                $message = "Depend On CR Changed To '$displayValue'";
                            } else {
                                $message = $info['message'];
                            }
                        } else {
                            if (is_array($newValue)) {
                                $newValue = implode(' , ', $newValue);
                            }

                            if ($field === 'testable') {
                                $newValue = $request->get('testable') === '1' ? 'Testable' : 'Non Testable';
                                $info['message'] = 'Change Request is';
                            }

                            if (in_array($field, ['technical_attachments', 'business_attachments'], true)) {
                                $files_name = [];
                                $attachments = $request->file($field, []);

                                foreach ($attachments as $attachment) {
                                    $files_name[] = $attachment->getClientOriginalName();
                                }

                                $info['message'] = 'Attachment(s) Added ';
                                $newValue = implode(', ', $files_name);
                            }

                            if (in_array($field, ['analysis_feedback', 'technical_feedback'], true)) {
                                $info['message'] = 'Comment Added ';
                            }

                            $message = $info['message'] . " \"$newValue\"";
                        }

                    } else {
                        if ($info['message']) {
                            $message = $info['message'] . " \"$newValue\"";
                        } else {
                            $message = "$info \"$newValue\"";
                        }
                    }

                    $this->createLog($log, $id, $user->id, "$message By {$user->user_name}");
                }
            }
        }

        // Boolean Toggles
        $this->logToggle($log, $id, $user->id, $request, $change_request, 'postpone', 'CR PostPone changed To');
        $this->logToggle($log, $id, $user->id, $request, $change_request, 'need_ux_ui', 'CR Need UI UX changed To');

        // User Assignments
        $assignments = [
            'assign_to' => 'Issue assigned  manually to',
            'cr_member' => 'Issue assigned  manually to',
            'rtm_member' => 'Issue assigned  manually to',
            'assignment_user_id' => 'Issue assigned  manually to',
            'developer_id' => 'Issue Assigned  Manually to',
            'tester_id' => 'Issue Assigned  Manually to',
            'designer_id' => 'Issue Assigned  Manually to',
        ];

        foreach ($assignments as $field => $label) {
            if (isset($request->$field)) {
                $assignedUser = User::find($request->$field);
                if ($assignedUser) {
                    $this->createLog($log, $id, $user->id, "$label '{$assignedUser->user_name}' by {$user->user_name}");
                }
            }
        }

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

            $newStatusesIds = NewWorkFlowStatuses::where('new_workflow_id', $request->new_status_id)->pluck('to_status_id')->toArray();
            $newStatusesNames = Status::whereIn('id', $newStatusesIds)->pluck('status_name')->toArray();
            $actualStatuses = implode(', ', $newStatusesNames);

            if ($status_title && $request->missing('hold') && $request->missing('is_final_confirmation')) {
                // Dependency Release Log (when the depend cr reach to the status delivered or reject)
                if ($request->released_from_hold) {
                    $this->createLog($log, $id, $user->id, "Change request status has been released by {$user->user_name} and the current status is $actualStatuses");
                }
                // Dependency Hold Log
                elseif ($change_request->fresh()->is_dependency_hold) {
                    $blockingCrs = \App\Models\CrDependency::where('cr_id', $id)
                        ->active()
                        ->with('dependsOnCr:id,cr_no')
                        ->get()
                        ->pluck('dependsOnCr.cr_no')
                        ->filter()
                        ->implode(', ');

                    $this->createLog($log, $id, $user->id, "Change Request Status changed to '$status_title' by {$user->user_name} (Actual Status: $actualStatuses - Pending Dependency (CR#$blockingCrs))");
                }
                // Normal Status Log
                else {
                    $this->createLog($log, $id, $user->id, "Change Request Status changed to '$actualStatuses' by '$user->user_name'");
                }
            } else {
                $log_message = "Change Request Status changed to '$actualStatuses' by '$user->user_name'";

                if ($request->has('is_final_confirmation')) {
                    $log_message = "Issue manually set to status '$actualStatuses' by '$user->user_name' from Administration";
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
            $this->createLog($log, $id, $user->id, "CR Held by $user->user_name");
        } elseif ($request->hold === 0) {
            $this->createLog($log, $id, $user->id, "CR unheld by $user->user_name");
        }

        return true;
    }

    private function createLog($logRepo, $crId, $userId, $message)
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
            $this->createLog($logRepo, $crId, $user->id, "Issue $label Estimated by {$user->user_name}");
        }
    }

    private function logDurationWithTimes($logRepo, $crId, $user, $request, $durationField, $startField, $endField)
    {
        if (isset($request->$durationField)) {
            $this->createLog($logRepo, $crId, $user->id, "Issue $durationField manually set to '{$request->$durationField} H' by {$user->user_name}");
        }

        if (isset($request->$startField) && isset($request->$endField)) {
            $startLabel = Str::of($startField)->replace('_', ' ')->title();
            $endLabel = Str::of($endField)->replace('_', ' ')->title();
            $this->createLog($logRepo, $crId, $user->id, "Issue $startLabel set to '{$request->$startField}' and $endLabel set to '{$request->$endField}' by {$user->user_name}");
        }
    }
}
