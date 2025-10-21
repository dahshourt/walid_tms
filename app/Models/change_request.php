<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Change_request extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    public $table = 'change_request';

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        'name',
        'is_overdue',
        'duration_summary',
        'completion_percentage'
    ];

    /**
     * Disable mass assignment protection for flexibility.
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'active' => 'boolean',
        'testable' => 'boolean',
        'need_design' => 'boolean',
        'need_iot_e2e_testing' => 'boolean',
        'need_down_time' => 'boolean',
        'postpone' => 'boolean',
        'approval' => 'boolean',
        'start_design_time' => 'datetime',
        'end_design_time' => 'datetime',
        'start_develop_time' => 'datetime',
        'end_develop_time' => 'datetime',
        'start_test_time' => 'datetime',
        'end_test_time' => 'datetime',
        'release_delivery_date' => 'datetime',
        'release_receiving_date' => 'datetime',
        'te_testing_date' => 'datetime',
        'uat_date' => 'datetime',
        'cost' => 'decimal:2',
        'design_duration' => 'integer',
        'develop_duration' => 'integer',
        'test_duration' => 'integer',
        'uat_duration' => 'integer',
        'man_days' => 'decimal:2',
    ];

    public function scopeNotInFinalState(Builder $query): Builder
    {
        return $query->whereHas('CurrentRequestStatuses', function ($query) {
            return $query->whereNotIn('new_status_id', [config('change_request.status_ids.Reject'), config('change_request.status_ids.Cancel'), config('change_request.parked_status_ids.promo_closure')]);;
        });
    }

    // ===================================
    // RELATIONSHIPS - ORIGINAL NAMES
    // ===================================

    /**
     * Get the defects associated with this change request.
     */
    public function defects(): HasMany
    {
        return $this->hasMany(Defect::class, 'cr_id', 'id');
    }

    /**
     * Get the logs associated with this change request.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(Log::class, 'cr_id', 'id');
    }

    /**
     * Get the custom fields for this change request.
     */
    public function change_request_custom_fields(): HasMany
    {
        return $this->hasMany(ChangeRequestCustomField::class, 'cr_id', 'id');
    }

    /**
     * Get request statuses for this change request.
     */
    public function Req_status(): HasMany
    {
        return $this->hasMany(Change_request_statuse::class, 'cr_id', 'id')
            ->select('id', 'new_status_id', 'old_status_id', 'active');
    }

    /**
     * Get the category this change request belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id')->select('id', 'name');
    }

    /**
     * Get the priority of this change request.
     */
    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class, 'priority_id')->select('id', 'name');
    }

    /**
     * Get the department this change request belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id')->select('id', 'name');
    }

    /**
     * Get the application this change request is for.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id')->select('id', 'name');
    }

    /**
     * Get the change request this one depends on.
     */
    public function depend_cr(): BelongsTo
    {
        return $this->belongsTo(Change_request::class, 'depend_cr_id')->select('id', 'title', 'cr_no');
    }

    /**
     * Get the requester of this change request.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Get the developer assigned to this change request.
     */
    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id')->select('id', 'name', 'user_name', 'email');
    }

    /**
     * Get the tester assigned to this change request.
     */
    public function tester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tester_id')->select('id', 'name', 'user_name', 'email');
    }

    /**
     * Get the designer assigned to this change request.
     */
    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'designer_id')->select('id', 'name', 'user_name', 'email');
    }

    /**
     * Get the active CAB record for this change request.
     */
    public function cab_cr(): HasOne
    {
        return $this->hasOne(CabCr::class, 'cr_id', 'id')->where('status', '0');
    }

    /**
     * Get the active technical CR record.
     */
    public function technical_Cr(): HasOne
    {
        return $this->hasOne(TechnicalCr::class, 'cr_id', 'id')->where('status', '0');
    }

    /**
     * Get the first technical CR record.
     */
    public function technical_Cr_first(): HasOne
    {
        return $this->hasOne(TechnicalCr::class, 'cr_id', 'id')->orderBy('id', 'DESC');
    }

    /**
     * Get current group statuses through status relationship.
     */
    public function current_status(): HasManyThrough
    {
        return $this->hasManyThrough(
            GroupStatuses::class,
            Change_request_statuse::class,
            'cr_id',
            'status_id',
            'id',
            'new_status_id'
        )->where('group_statuses.type', 1);
    }

    /**
     * Get request statuses ordered by ID descending.
     */
    public function RequestStatuses(): HasMany
    {
        return $this->hasMany(Change_request_statuse::class, 'cr_id', 'id')
            ->where('active', '1')
            ->orderBy('id', 'DESC');
    }

    public function RequestStatusesDone()
    {
        return $this->hasMany(Change_request_statuse::class, 'cr_id', 'id')
            ->where('active', '2')
            ->orderBy('id', 'desc');
    }

    public function AllRequestStatuses()
    {
        return $this->hasMany(Change_request_statuse::class, 'cr_id', 'id')->orderBy('id', 'DESC');
    }

    /**
     * Get the current request status.
     */
    public function CurrentRequestStatuses(): HasOne
    {
        return $this->hasOne(Change_request_statuse::class, 'cr_id', 'id')->where('active', '1');
    }

    /**
     * Get the division manager for this change request.
     */
    public function division_manger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'division_manager_id');
    }

    /**
     * Get attachments for this change request.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachements_crs::class, 'cr_id');
    }

    /**
     * Get the release this change request is associated with.
     */
    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class, 'release_name', 'id');
    }

    /**
     * Get current status with relationship.
     */
    public function currentStatusRel(): HasOne
    {
        return $this->hasOne(Change_request_statuse::class, 'cr_id')
            ->where('active', 1)
            ->latest('id')
            ->with('status');
    }

    // ===================================
    // ACCESSORS & MUTATORS
    // ===================================

    /**
     * Get the name attribute (alias for title).
     */
    public function getNameAttribute(): string
    {
        return $this->title ?? '';
    }

    /**
     * Check if the change request is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        $releaseDate = $this->release_delivery_date;
        return $releaseDate && $releaseDate->isPast() && !$this->isCompleted();
    }

    /**
     * Get a summary of all durations.
     */
    public function getDurationSummaryAttribute(): array
    {
        return [
            'design' => $this->design_duration ?? 0,
            'development' => $this->develop_duration ?? 0,
            'testing' => $this->test_duration ?? 0,
            'uat' => $this->uat_duration ?? 0,
            'total' => $this->getTotalDuration(),
        ];
    }

    /**
     * Calculate completion percentage based on completed phases.
     */
    public function getCompletionPercentageAttribute(): int
    {
        $phases = ['design', 'development', 'testing'];
        $completedPhases = 0;
        $totalPhases = count($phases);

        if ($this->end_design_time) {
            $completedPhases++;
        }
        if ($this->end_develop_time) {
            $completedPhases++;
        }
        if ($this->end_test_time) {
            $completedPhases++;
        }

        return $totalPhases > 0 ? round(($completedPhases / $totalPhases) * 100) : 0;
    }

    // ===================================
    // SCOPES
    // ===================================

    /**
     * Scope a query to only include active change requests.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include change requests by priority.
     */
    public function scopeByPriority(Builder $query, int $priorityId): Builder
    {
        return $query->where('priority_id', $priorityId);
    }

    /**
     * Scope a query to only include change requests by category.
     */
    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to only include change requests assigned to a user.
     */
    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('developer_id', $userId)
                ->orWhere('tester_id', $userId)
                ->orWhere('designer_id', $userId);
        });
    }

    /**
     * Scope a query to only include overdue change requests.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('release_delivery_date', '<', now())
            ->where('active', true);
    }

    // ===================================
    // ORIGINAL METHODS - ENHANCED
    // ===================================

    /**
     * Get current status for list view with enhanced error handling.
     */
    public function ListCurrentStatus()
    {
        try {
            $group = $this->getCurrentGroupId();
            $view_statuses = GroupStatuses::where('group_id', $group)
                ->where('type', 2)
                ->pluck('status_id');

            $status = Change_request_statuse::where('cr_id', $this->id)
                ->whereIn('new_status_id', $view_statuses)
                ->where('active', '1')
                ->first();

            return $status;
        } catch (\Exception $e) {
            \Log::error("Error getting list current status for CR {$this->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get available releases with enhanced filtering.
     */
    public function get_releases(): Collection
    {
        return Release::whereDate('go_live_planned_date', '>', now())
            ->where('active', true)
            ->orderBy('go_live_planned_date')
            ->get();
    }

    /**
     * Get current status (old method) with better error handling.
     */
    public function getCurrentStatusOld()
    {
        try {
            $status = Change_request_statuse::where('cr_id', $this->id)
                ->where('active', '1')
                ->first();

            if ($status) {
                $workflow = NewWorkFlow::where('from_status_id', $status->old_status_id)
                    ->where('type_id', $this->workflow_type_id)
                    ->first();

                $status->same_time = $workflow->same_time ?? 0;
                $status->to_status_label = $workflow->to_status_label ?? "";
            }

            return $status;
        } catch (\Exception $e) {
            \Log::error("Error getting current status for CR {$this->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get technical team current status with enhanced logic.
     */
    public function GetTechnicalTeamCurrentStatus()
    {
        try {
            $group = $this->getCurrentGroupId();
            $technical_cr_team_status = null;

            $TechnicalCr = TechnicalCr::where("cr_id", $this->id)
                ->where('status', '0')
                ->first();

            if ($TechnicalCr) {
                $technical_cr_team_status = $TechnicalCr->technical_cr_team()
                    ->where('group_id', $group)
                    ->where('status', '0')
                    ->first();
            }

            return $technical_cr_team_status;
        } catch (\Exception $e) {
            \Log::error("Error getting technical team status for CR {$this->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get current status with enhanced workflow logic and better error handling.
     */
    public function getCurrentStatus()
    {
        try {

            if (request()->reference_status) {
                $statusInfo = Change_request_statuse::find(request()->reference_status);
                $status = $this->attachWorkflowInfo($statusInfo);

            } else {
                $group = $this->getCurrentGroupId();
                $view_statuses = GroupStatuses::where('group_id', $group)
                    ->where('type', 2)
                    ->pluck('status_id')
                    ->toArray();

                $technical_cr_team_status = $this->GetTechnicalTeamCurrentStatus();

                if ($technical_cr_team_status) {
                    if (in_array($technical_cr_team_status->current_status_id, $view_statuses)) {
                        $view_statuses = [$technical_cr_team_status->current_status_id];
                    }
                }

                $status = Change_request_statuse::where('cr_id', $this->id)
                    ->whereIn('new_status_id', $view_statuses)
                    ->where('active', '1')
                    ->first();

                if ($status) {
                    $status = $this->attachWorkflowInfo($status);
                } else {
                    // Fallback logic
                    $status = Change_request_statuse::where('cr_id', $this->id)
                        ->where('active', '1')
                        ->first();

                    if ($status) {
                        $status = $this->attachWorkflowInfo($status);
                    } else {
                        $status = Change_request_statuse::where('cr_id', $this->id)
                            ->orderBy('id', 'desc')
                            ->first();
                        if ($status) {
                            $status = $this->attachWorkflowInfo($status);
                        }
                    }
                }
            }

            return $status;
        } catch (\Exception $e) {
            \Log::error("Error getting current status for CR {$this->id}: " . $e->getMessage());
            return null;
        }
    }

    public function getallCurrentStatus()
    {
        $statuses = Change_request_statuse::where('cr_id', $this->id)->where('active', '1')->get();
        return $statuses;
    }

    // ===================================
    // HELPER METHODS
    // ===================================

    /**
     * Get current group ID from session or user default.
     */
    private function getCurrentGroupId(): int
    {
        if (session('default_group')) {
            return session('default_group');
        }

        return auth()->user()->default_group ?? 1;
    }

    /**
     * Attach workflow information to status object.
     */
    private function attachWorkflowInfo($status)
    {
        if (!$status) {
            return null;
        }

        try {
            $workflow = NewWorkFlow::where('from_status_id', $status->old_status_id)
                ->where('type_id', $this->workflow_type_id)
                ->first();

            $status->same_time = $workflow->same_time ?? 0;
            $status->to_status_label = $workflow->to_status_label ?? "";

            return $status;
        } catch (\Exception $e) {
            \Log::error("Error attaching workflow info for CR {$this->id}: " . $e->getMessage());
            $status->same_time = 0;
            $status->to_status_label = "";
            return $status;
        }
    }

    private function attachWorkflowInfoById($status)
    {
        if (!$status) {
            return null;
        }

        try {
            $workflow = NewWorkFlow::where('from_status_id', $status->new_status_id)
                ->where('type_id', $this->workflow_type_id)
                ->first();

            $status->same_time = $workflow->same_time ?? 0;
            $status->to_status_label = $workflow->to_status_label ?? "";

            return $status;
        } catch (\Exception $e) {
            \Log::error("Error attaching workflow info for CR {$this->id}: " . $e->getMessage());
            $status->same_time = 0;
            $status->to_status_label = "";
            return $status;
        }
    }

    /**
     * Check if the change request is completed.
     */
    public function isCompleted(): bool
    {
        $currentStatus = $this->getCurrentStatus();

        if (!$currentStatus) {
            return false;
        }

        // Add your completed status IDs here
        $completedStatusIds = [/* Add your completed status IDs */];

        return in_array($currentStatus->new_status_id, $completedStatusIds);
    }

    /**
     * Get total duration of all phases.
     */
    public function getTotalDuration(): int
    {
        return ($this->design_duration ?? 0) +
            ($this->develop_duration ?? 0) +
            ($this->test_duration ?? 0) +
            ($this->uat_duration ?? 0);
    }

    /**
     * Get remaining duration based on current phase.
     */
    public function getRemainingDuration(): int
    {
        $totalDuration = $this->getTotalDuration();
        $completionPercentage = $this->completion_percentage;

        return max(0, $totalDuration - (($completionPercentage / 100) * $totalDuration));
    }

    /**
     * Check if change request needs approval.
     */
    public function needsApproval(): bool
    {
        return !$this->approval && $this->isInApprovalPhase();
    }

    /**
     * Check if change request is in approval phase.
     */
    private function isInApprovalPhase(): bool
    {
        $currentStatus = $this->getCurrentStatus();

        if (!$currentStatus) {
            return false;
        }

        // Add your approval status IDs here
        $approvalStatusIds = [/* Add your approval status IDs */];

        return in_array($currentStatus->new_status_id, $approvalStatusIds);
    }

    /**
     * Get change requests that depend on this one.
     */
    public function getDependentChangeRequests(): Collection
    {
        return self::where('depend_cr_id', $this->id)
            ->where('active', true)
            ->get();
    }

    /**
     * Check if this change request has dependencies that are not completed.
     */
    public function hasUncompletedDependencies(): bool
    {
        if (!$this->depend_cr_id) {
            return false;
        }

        $dependentCr = self::find($this->depend_cr_id);

        return $dependentCr && !$dependentCr->isCompleted();
    }

    /**
     * Get estimated completion date based on current progress.
     */
    public function getEstimatedCompletionDate(): ?\Carbon\Carbon
    {
        if ($this->isCompleted()) {
            return $this->updated_at;
        }

        $remainingDays = $this->getRemainingDuration();

        if ($remainingDays <= 0) {
            return now();
        }

        return now()->addDays($remainingDays);
    }


    /**
     * Get current status for division page with better error handling.
     */
    public function getCurrentStatusForDivision()
    {
        try {
            $status = Change_request_statuse::where('cr_id', $this->id)
                ->where('active', '1')
                ->first();

            if ($status) {
                $workflow = NewWorkFlow::where('from_status_id', $status->old_status_id)
                    ->where('type_id', $this->workflow_type_id)
                    ->first();

                $status->same_time = $workflow->same_time ?? 0;
                $status->to_status_label = $workflow->to_status_label ?? "";
            }

            return $status;
        } catch (\Exception $e) {
            \Log::error("Error getting current status for CR {$this->id}: " . $e->getMessage());
            return null;
        }
    }

    public function inFinalState(): bool
    {
        $current_status = $this->CurrentRequestStatuses->new_status_id;

        return in_array($current_status, [config('change_request.parked_status_ids.promo_closure')]);
    }

    public function isAlreadyCancelledOrRejected(): bool
    {
        $current_status = $this->CurrentRequestStatuses->new_status_id;

        return in_array($current_status, [config('change_request.status_ids.Reject'), config('change_request.status_ids.Cancel')]);
    }
}
