<?php
namespace App\Services\ChangeRequest;

use App\Models\{Change_request, User, Group};
use Carbon\Carbon;

class ChangeRequestSchedulingService
{
    public function reorderTimes($crId): array
    {
        try {
            $cr = Change_request::find($crId);

            if (!$cr) {
                return [
                    'status' => false,
                    'message' => "Change Request with ID {$crId} not found."
                ];
            }

            $this->processDesignPhase($cr);
            $this->processDevelopmentPhase($cr);
            $this->processTestingPhase($cr);
            $this->reorderQueuedRequests($cr, $crId);

            return [
                'status' => true,
                'message' => "Successfully reordered times for CR ID {$crId} and related queued CRs."
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'An error occurred while reordering the times: ' . $e->getMessage()
            ];
        }
    }

    public function reorderChangeRequests($crId)
    {
        $changeRequest = Change_request::find($crId);

        if (!$changeRequest) {
            return 'Change Request not found';
        }

        // Adjust times for this change request
        $this->adjustTimesForChangeRequest($crId, $changeRequest);

        // Get developer, tester, and designer for future requests
        $developerId = $changeRequest->developer_id;
        $testerId = $changeRequest->tester_id;
        $designerId = $changeRequest->designer_id;

        // Fetch all other CRs for the same developer, tester, and designer
        $otherChangeRequests = Change_request::where(function($query) use ($developerId, $testerId, $designerId) {
            $query->whereIn('developer_id', [$developerId])
                  ->orWhereIn('tester_id', [$testerId])
                  ->orWhereIn('designer_id', [$designerId]);
        })
        ->where('id', '!=', $crId)
        ->orderBy('start_design_time', 'asc')
        ->get();

        // Reorder other CRs
        foreach ($otherChangeRequests as $otherRequest) {
            $this->adjustTimesForChangeRequest($crId, $otherRequest);
        }

        return 'Change requests reordered successfully';
    }

    public function reorderCRQueues(string $crNumber): array
    {
        $targetCR = Change_request::where('id', $crNumber)->first();

        if (!$targetCR) {
            return [
                'status' => false,
                'message' => 'Change Request not found.',
            ];
        }

        $this->shiftQueue($targetCR->developer_id, 'developer_id', $targetCR->id);
        $this->shiftQueue($targetCR->tester_id, 'tester_id', $targetCR->id);
        $this->shiftQueue($targetCR->designer_id, 'designer_id', $targetCR->id);

        return [
            'status' => true,
            'message' => 'Change Request reordered successfully.',
        ];
    }

    protected function processDesignPhase($cr): void
    {
        if ($cr->design_duration <= 0) {
            return;
        }

        $currentTime = Carbon::now()->timestamp;
        
        if (isset($cr->start_design_time) && Carbon::parse($cr->start_design_time)->isFuture()) {
            $startDesignTime = Carbon::createFromTimestamp($this->setToWorkingDate($currentTime));
            $endDesignTime = Carbon::parse(
                $this->generateEndDate(
                    $startDesignTime->timestamp,
                    $cr->design_duration,
                    false,
                    $cr->designer_id,
                    'design'
                )
            );

            $conflictingCR = $this->isDesignerBusy(
                $cr->designer_id, 
                $startDesignTime, 
                $cr->design_duration, 
                $endDesignTime, 
                $cr->id
            );

            if ($conflictingCR) {
                $startDesignTime = $this->resolveDesignConflict($conflictingCR, $cr);
                $endDesignTime = Carbon::parse(
                    $this->generateEndDate(
                        $startDesignTime->timestamp,
                        $cr->design_duration,
                        false,
                        $cr->designer_id,
                        'design'
                    )
                );
            }

            $cr->update([
                'start_design_time' => $startDesignTime->format('Y-m-d H:i:s'),
                'end_design_time' => $endDesignTime->format('Y-m-d H:i:s'),
            ]);
        }
    }

    protected function processDevelopmentPhase($cr): void
    {
        if ($cr->develop_duration <= 0) {
            return;
        }

        if (isset($cr->start_develop_time)) {
            $startDevelopTime = Carbon::createFromTimestamp(
                Carbon::parse($cr->end_design_time)->timestamp
            );

            $endDevelopTime = Carbon::parse(
                $this->generateEndDate(
                    $startDevelopTime->timestamp,
                    $cr->develop_duration,
                    false,
                    $cr->developer_id,
                    'dev'
                )
            );

            $conflictingCR = $this->isDeveloperBusy(
                $cr->developer_id,
                $startDevelopTime,
                $cr->develop_duration,
                $endDevelopTime,
                $cr->id
            );

            if ($conflictingCR) {
                $startDevelopTime = $this->resolveDevelopmentConflict($conflictingCR, $cr);
                $endDevelopTime = Carbon::parse(
                    $this->generateEndDate(
                        $startDevelopTime->timestamp,
                        $cr->develop_duration,
                        false,
                        $cr->developer_id,
                        'dev'
                    )
                );
            }

            $cr->update([
                'start_develop_time' => $startDevelopTime->format('Y-m-d H:i:s'),
                'end_develop_time' => $endDevelopTime->format('Y-m-d H:i:s'),
            ]);
        }
    }

    protected function processTestingPhase($cr): void
    {
        if ($cr->test_duration <= 0) {
            return;
        }

        $startTestTime = Carbon::createFromTimestamp(
            $this->setToWorkingDate(Carbon::parse($cr->end_develop_time)->timestamp)
        );

        if (isset($cr->start_test_time)) {
            $startTestTime = Carbon::parse($cr->end_develop_time);
            if ($startTestTime->isPast()) {
                $startTestTime = Carbon::now();
            }
        }

        $endTestTime = Carbon::parse(
            $this->generateEndDate(
                $startTestTime->timestamp,
                $cr->test_duration,
                false,
                $cr->tester_id,
                'test'
            )
        );

        $conflictingCR = $this->isTesterBusy(
            $cr->tester_id,
            $startTestTime,
            $cr->test_duration,
            $endTestTime,
            $cr->id
        );

        if ($conflictingCR) {
            $startTestTime = $this->resolveTestingConflict($conflictingCR, $cr);
            $endTestTime = Carbon::parse(
                $this->generateEndDate(
                    $startTestTime->timestamp,
                    $cr->test_duration,
                    false,
                    $cr->tester_id,
                    'test'
                )
            );
        }

        if (!isset($cr->start_test_time) || Carbon::parse($cr->start_test_time)->isFuture()) {
            $cr->update([
                'start_test_time' => $startTestTime->format('Y-m-d H:i:s'),
                'end_test_time' => $endTestTime->format('Y-m-d H:i:s'),
            ]);
        }
    }

    protected function reorderQueuedRequests($cr, $crId): void
    {
        $queue = Change_request::where(function ($query) use ($cr) {
            $query->where('designer_id', $cr->designer_id)
                  ->orWhere('developer_id', $cr->developer_id)
                  ->orWhere('tester_id', $cr->tester_id);
        })
        ->where('id', '!=', $crId)
        ->orderBy('id')
        ->get();

        foreach ($queue as $queuedCr) {
            $this->adjustQueuedRequest($queuedCr, $cr);
        }
    }

    protected function adjustQueuedRequest($queuedCr, $cr): void
    {
        // Design Phase
        if ($queuedCr->designer_id == $cr->designer_id && 
            !empty($queuedCr->design_duration) && 
            $queuedCr->design_duration > 0) {
            
            $startDesignTime = Carbon::parse($cr->end_design_time);
            $endDesignTime = Carbon::parse(
                $this->generateEndDate(
                    $startDesignTime->timestamp,
                    $queuedCr->design_duration,
                    false,
                    $queuedCr->designer_id,
                    'design'
                )
            );

            $queuedCr->update([
                'start_design_time' => $startDesignTime->format('Y-m-d H:i:s'),
                'end_design_time' => $endDesignTime->format('Y-m-d H:i:s'),
            ]);
        }

        // Development Phase
        if ($queuedCr->developer_id == $cr->developer_id && 
            !empty($queuedCr->develop_duration) && 
            $queuedCr->develop_duration > 0) {
            
            $startDevelopTime = Carbon::parse($cr->end_develop_time);
            $endDevelopTime = Carbon::parse(
                $this->generateEndDate(
                    $startDevelopTime->timestamp,
                    $queuedCr->develop_duration,
                    false,
                    $queuedCr->developer_id,
                    'dev'
                )
            );

            if ($this->isDeveloperUnavailable($queuedCr->developer_id, $startDevelopTime, $endDevelopTime, $queuedCr->id)) {
                $startDevelopTime = $this->getFirstAvailableTime($queuedCr->developer_id, $startDevelopTime, $endDevelopTime, $queuedCr->id);
                $endDevelopTime = Carbon::parse(
                    $this->generateEndDate(
                        $startDevelopTime->timestamp,
                        $queuedCr->develop_duration,
                        false,
                        $queuedCr->developer_id,
                        'dev'
                    )
                );
            }

            $queuedCr->update([
                'start_develop_time' => $startDevelopTime->format('Y-m-d H:i:s'),
                'end_develop_time' => $endDevelopTime->format('Y-m-d H:i:s'),
            ]);
        }

        // Test Phase
        if ($queuedCr->tester_id == $cr->tester_id && 
            !empty($queuedCr->test_duration) && 
            $queuedCr->test_duration > 0) {
            
            $startTestTime = Carbon::parse($cr->end_test_time ?? $cr->end_develop_time);
            $endTestTime = Carbon::parse(
                $this->generateEndDate(
                    $startTestTime->timestamp,
                    $queuedCr->test_duration,
                    false,
                    $queuedCr->tester_id,
                    'test'
                )
            );

            if ($this->isTesterUnavailable($queuedCr->tester_id, $startTestTime, $endTestTime, $queuedCr->id)) {
                $startTestTime = $this->getFirstAvailableTimeForTest($queuedCr->tester_id, $startTestTime, $endTestTime, $queuedCr->id);
                $endTestTime = Carbon::parse(
                    $this->generateEndDate(
                        $startTestTime->timestamp,
                        $queuedCr->test_duration,
                        false,
                        $queuedCr->tester_id,
                        'test'
                    )
                );
            }

            $queuedCr->update([
                'start_test_time' => $startTestTime->format('Y-m-d H:i:s'),
                'end_test_time' => $endTestTime->format('Y-m-d H:i:s'),
            ]);
        }
    }

    // Conflict detection methods
    public function isDeveloperBusy($developerId, $startDevelopTime, $developDuration, $endDevelopTime, $shiftingCrId = null)
    {
        return Change_request::where('developer_id', $developerId)
            ->where('id', '!=', $shiftingCrId)
            ->where(function ($query) use ($startDevelopTime, $endDevelopTime) {
                $query->whereBetween('start_develop_time', [$startDevelopTime, $endDevelopTime])
                      ->orWhereBetween('end_develop_time', [$startDevelopTime, $endDevelopTime])
                      ->orWhere(function ($query) use ($startDevelopTime, $endDevelopTime) {
                          $query->where('start_develop_time', '<=', $startDevelopTime)
                                ->where('end_develop_time', '>=', $endDevelopTime);
                      });
            })
            ->first();
    }

    public function isDesignerBusy($designerId, $startDesignerTime, $designerDuration, $endDesignerTime, $shiftingCrId = null)
    {
        return Change_request::where('designer_id', $designerId)
            ->where('id', '!=', $shiftingCrId)
            ->where(function ($query) use ($startDesignerTime, $endDesignerTime) {
                $query->whereBetween('start_design_time', [$startDesignerTime, $endDesignerTime])
                      ->orWhereBetween('end_design_time', [$startDesignerTime, $endDesignerTime])
                      ->orWhere(function ($query) use ($startDesignerTime, $endDesignerTime) {
                          $query->where('start_design_time', '<=', $startDesignerTime)
                                ->where('end_design_time', '>=', $endDesignerTime);
                      });
            })
            ->first();
    }

    public function isTesterBusy($testerId, $startTestTime, $testDuration, $endTestTime, $shiftingCrId = null)
    {
        return Change_request::where('tester_id', $testerId)
            ->where('id', '!=', $shiftingCrId)
            ->where(function ($query) use ($startTestTime, $endTestTime) {
                $query->whereBetween('start_test_time', [$startTestTime, $endTestTime])
                      ->orWhereBetween('end_test_time', [$startTestTime, $endTestTime])
                      ->orWhere(function ($query) use ($startTestTime, $endTestTime) {
                          $query->where('start_test_time', '<=', $startTestTime)
                                ->where('end_test_time', '>=', $endTestTime);
                      });
            })
            ->first();
    }

    // Helper methods for working time calculations
    public function setToWorkingDate($date): int
    {
        if ($date instanceof \Carbon\Carbon) {
            $date = $date->timestamp;
        }

        $workingHours = config('change_request.working_hours');
        $weekendDays = $workingHours['weekend_days'];

        // Weekend handling
        if (in_array((int) date('w', $date), $weekendDays)) {
            $daysToAdd = $weekendDays[0] == 5 ? 2 : 1; // If Friday is weekend, add 2 days, otherwise 1
            $date = strtotime(date('Y-m-d 08:00:00', $date) . " +{$daysToAdd} days");
        }

        // Working hours handling
        $hour = (int) date('G', $date);
        $startHour = $workingHours['start'];
        $endHour = $workingHours['end'];

        if ($hour >= $endHour) {
            $date = strtotime(date("Y-m-d {$startHour}:00:00", $date) . ' +1 days');
        } elseif ($hour < $startHour) {
            $date = strtotime(date("Y-m-d {$startHour}:00:00", $date));
        }

        // Re-check weekend after adjustment
        if (in_array((int) date('w', $date), $weekendDays)) {
            $daysToAdd = $weekendDays[0] == 5 ? 2 : 1;
            $date = strtotime(date("Y-m-d {$startHour}:00:00", $date) . " +{$daysToAdd} days");
        }

        return $date;
    }

    public function generateEndDate($startDate, $duration, $onGoing, $userId = 0, $action = 'dev'): string
    {
        $manPower = config('change_request.default_values.man_power');
        $manPowerOngoing = config('change_request.default_values.man_power_ongoing');

        $assignUser = User::find($userId);
        if ($assignUser && $assignUser->defualt_group) {
            $groupPower = $assignUser->defualt_group->man_power;
            $userManPower = $assignUser->man_power;

            if ($userManPower) {
                $manPower = $userManPower;
                $manPowerOngoing = $userManPower == 8 ? 1 : 8 - $userManPower;
            } else {
                $manPower = $groupPower;
                $manPowerOngoing = $groupPower == 8 ? 1 : 8 - $groupPower;
            }
        }

        // Prevent division by zero
        if ($manPowerOngoing == 0) $manPowerOngoing = 1;
        if ($manPower == 0) $manPower = 1;

        $i = ($action == 'dev') 
            ? ($duration * (int) (($onGoing) ? (8 / $manPowerOngoing) : (8 / $manPower))) 
            : $duration * 2;

        $time = $startDate;
        $workingHours = config('change_request.working_hours');
        $weekendDays = $workingHours['weekend_days'];

        while ($i != 0) {
            $time = strtotime('+1 hour', $time);
            $dayOfWeek = (int) date('w', $time);
            $hour = (int) date('G', $time);
            
            if (!in_array($dayOfWeek, $weekendDays) && 
                $hour < $workingHours['end'] && 
                $hour >= $workingHours['start']) {
                --$i;
            }
        }

        return date('Y-m-d H:i:s', $time);
    }

    // Conflict resolution methods
    protected function resolveDesignConflict($conflictingCR, $cr)
    {
        $date1 = $conflictingCR->end_design_time;
        $date2 = Carbon::now();
        $date3 = $conflictingCR->start_design_time;
        $date4 = $cr->start_design_time;

        $date2 = Carbon::createFromTimestamp($this->setToWorkingDate($date2->timestamp));
        $date3 = Carbon::createFromTimestamp(Carbon::parse($date3)->timestamp);

        if ($date2->greaterThan($date3)) {
            return Carbon::createFromTimestamp(Carbon::parse($date1)->timestamp);
        } else {
            return Carbon::createFromTimestamp($this->setToWorkingDate($date2->timestamp));
        }
    }

    protected function resolveDevelopmentConflict($conflictingCR, $cr)
    {
        $date1 = $conflictingCR->start_develop_time;
        $date2 = $cr->end_design_time;

        if (Carbon::parse($date1)->greaterThan(Carbon::parse($date2))) {
            return Carbon::createFromTimestamp($this->setToWorkingDate(Carbon::parse($date1)->timestamp));
        } else {
            return Carbon::createFromTimestamp($this->setToWorkingDate(Carbon::parse($date2)->timestamp));
        }
    }

    protected function resolveTestingConflict($conflictingCR, $cr)
    {
        $date1 = $conflictingCR->start_test_time;
        $date2 = $cr->end_develop_time;

        if (Carbon::parse($date1)->greaterThan(Carbon::parse($date2))) {
            return Carbon::createFromTimestamp($this->setToWorkingDate(Carbon::parse($date1)->timestamp));
        } else {
            return Carbon::createFromTimestamp($this->setToWorkingDate(Carbon::parse($date2)->timestamp));
        }
    }

    // Availability checking methods
    private function isDeveloperUnavailable($developerId, $startTime, $endTime, $excludeCrId): bool
    {
        return Change_request::where('developer_id', $developerId)
            ->where('id', '!=', $excludeCrId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_develop_time', [$startTime, $endTime])
                      ->orWhereBetween('end_develop_time', [$startTime, $endTime])
                      ->orWhere(function ($q) use ($startTime, $endTime) {
                          $q->where('start_develop_time', '<', $startTime)
                            ->where('end_develop_time', '>', $endTime);
                      });
            })
            ->exists();
    }

    private function isTesterUnavailable($testerId, $startTime, $endTime, $excludeCrId): bool
    {
        return Change_request::where('tester_id', $testerId)
            ->where('id', '!=', $excludeCrId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_test_time', [$startTime, $endTime])
                      ->orWhereBetween('end_test_time', [$startTime, $endTime])
                      ->orWhere(function ($q) use ($startTime, $endTime) {
                          $q->where('start_test_time', '<', $startTime)
                            ->where('end_test_time', '>', $endTime);
                      });
            })
            ->exists();
    }

    private function getFirstAvailableTime($developerId, $startTime, $endTime, $excludeCrId)
    {
        $unavailablePeriods = Change_request::where('developer_id', $developerId)
            ->where('id', '!=', $excludeCrId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_develop_time', [$startTime, $endTime])
                      ->orWhereBetween('end_develop_time', [$startTime, $endTime])
                      ->orWhere(function ($q) use ($startTime, $endTime) {
                          $q->where('start_develop_time', '<', $startTime)
                            ->where('end_develop_time', '>', $endTime);
                      });
            })
            ->orderBy('end_develop_time', 'asc')
            ->get(['start_develop_time', 'end_develop_time']);

        $availabilityStart = Carbon::parse($startTime);
        foreach ($unavailablePeriods as $period) {
            $periodEnd = Carbon::parse($period->end_develop_time);
            if ($periodEnd->greaterThan($availabilityStart)) {
                $availabilityStart = $periodEnd;
            }
        }

        return $availabilityStart;
    }

    private function getFirstAvailableTimeForTest($testerId, $startTime, $endTime, $excludeCrId)
    {
        $unavailablePeriods = Change_request::where('tester_id', $testerId)
            ->where('id', '!=', $excludeCrId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_test_time', [$startTime, $endTime])
                      ->orWhereBetween('end_test_time', [$startTime, $endTime])
                      ->orWhere(function ($q) use ($startTime, $endTime) {
                          $q->where('start_test_time', '<', $startTime)
                            ->where('end_test_time', '>', $endTime);
                      });
            })
            ->orderBy('end_test_time', 'asc')
            ->get(['start_test_time', 'end_test_time']);

        $availabilityStart = Carbon::parse($startTime);
        foreach ($unavailablePeriods as $period) {
            $periodEnd = Carbon::parse($period->end_test_time);
            if ($periodEnd->greaterThan($availabilityStart)) {
                $availabilityStart = $periodEnd;
            }
        }

        return $availabilityStart;
    }

    function isStartTimeInFuture($startTime): bool
    {
        $start = Carbon::parse($startTime);
        $now = Carbon::now();
        return $now->lessThan($start);
    }

    protected function adjustTimesForChangeRequest($crId, $changeRequest): void
    {
        // Implementation for adjusting times for a specific change request
        // This would include the complex logic from the original method
        $this->processDesignPhase($changeRequest);
        $this->processDevelopmentPhase($changeRequest);
        $this->processTestingPhase($changeRequest);
    }

    protected function shiftQueue($userId, $roleColumn, $targetCrId): void
    {
        // Implementation for shifting the queue for a specific role
        $affectedRequests = Change_request::where($roleColumn, $userId)
            ->where('id', '!=', $targetCrId)
            ->orderBy('created_at')
            ->get();

        foreach ($affectedRequests as $request) {
            $this->adjustTimesForChangeRequest($request->id, $request);
        }
    }
}