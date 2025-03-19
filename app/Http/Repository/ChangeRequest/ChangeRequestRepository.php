<?php

namespace App\Http\Repository\ChangeRequest;

use App\Contracts\ChangeRequest\ChangeRequestRepositoryInterface;
// declare Entities
use App\Http\Repository\Logs\LogRepository;
use App\Http\Repository\NewWorkFlow\NewWorkflowRepository;
use App\Models\Application;
use App\Models\Change_request;
use App\Models\Change_request_statuse;
use App\Models\GroupStatuses;
use App\Models\NewWorkFlow;
use App\Models\Status;
use App\Models\User;
use App\Models\Priority;
use App\Models\Unit;
use App\Models\Category;
use App\Models\DivisionManagers;
use App\Models\CustomField;
use App\Models\ChangeRequestCustomField;
use App\Models\CabCrUser;
use App\Models\CabCr;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Http\Controllers\Mail\MailController;

class ChangeRequestRepository implements ChangeRequestRepositoryInterface
{
    private $changeRequest_old;

     

    public function createOld($request)
    {
        return Change_request::create($request)->id;
    }
    public function findById($id)
    {
       
        return   Change_request::find($id);
    }

    public function reorderChangeRequests($crId)
    {
     
         $changeRequest = $this->findById($crId); 

   
        if (!$changeRequest) {
            // If not found, return back with a flash message
            return 'Change Request not found';
            //return back()->with('error', 'Change Request not found.');
        }
        // Adjust times for this change request
        $this->adjustTimesForChangeRequest($crId,$changeRequest);

        // Get developer, tester, and designer for future requests
        $developerId = $changeRequest->developer_id;
        $testerId = $changeRequest->tester_id;
        $designerId = $changeRequest->designer_id;

        // Fetch all other CRs for the same developer, tester, and designer
        $otherChangeRequests = Change_request::whereIn('developer_id', [$developerId])
            ->orWhereIn('tester_id', [$testerId])
            ->orWhereIn('designer_id', [$designerId])
            ->where('id', '!=', $crId)
            ->orderBy('start_design_time', 'asc')
            ->get();

        // Reorder other CRs
        foreach ($otherChangeRequests as $otherRequest) {
            $this->adjustTimesForChangeRequest($crId,$otherRequest);
        }

        return 'Change requests reordered successfully';
    }

 
    public function reorderCRQueues(string $crNumber)
    {
        // Fetch the target CR
        $targetCR = Change_request::where('id', $crNumber)->first();

        if (!$targetCR) {
            return [
                'status' => false,
                'message' => 'Change Request not found.',
            ];
        }

        $developer_id = $targetCR->developer_id;
        $tester_id = $targetCR->tester_id;
        $designer_id = $targetCR->designer_id;

        // Reorder for each role queue
        $this->shiftQueue($developer_id, 'developer_id', $targetCR->id);
        $this->shiftQueue($tester_id, 'tester_id', $targetCR->id);
        $this->shiftQueue($designer_id, 'designer_id', $targetCR->id);

        return [
            'status' => true,
            'message' => 'Change Request reordered successfully.',
        ];
    }

    /**
     * Shift the queue for a specific role.
     *
     * @param int $userId
     * @param string $roleColumn
     * @param int $targetCrId
     * @return void
     */
    public function isDeveloperBusy($developerId, $startDevelopTime, $developDuration,$endDevelopTime, $shiftingCrId = null)
    {

    //     if($this->isStartTimeInFuture($startDevelopTime))
    //    {

    //     return null;
    //    }
        // Calculate the end develop time based on the start time and the duration
       // $endDevelopTime = Carbon::parse($startDevelopTime)->copy()->addMinutes($developDuration);
    
        // Find any overlapping CR for this developer, excluding the shifting CR
        $conflictingCR = Change_request::where('developer_id', $developerId)
            ->where('id', '!=', $shiftingCrId) // Exclude the shifting CR
            ->where(function ($query) use ($startDevelopTime, $endDevelopTime) {
                // Overlapping conditions
                $query->whereBetween('start_develop_time', [$startDevelopTime, $endDevelopTime]) // Starts during this period
                      ->orWhereBetween('end_develop_time', [$startDevelopTime, $endDevelopTime]) // Ends during this period
                      ->orWhere(function ($query) use ($startDevelopTime, $endDevelopTime) {
                          // Encapsulates the entire period
                          $query->where('start_develop_time', '<=', $startDevelopTime)
                                ->where('end_develop_time', '>=', $endDevelopTime);
                      });
            })
            ->first();
    
        return $conflictingCR ? $conflictingCR : null;
    }
    // public function isDesignerBusy($designerId, $startDesignerTime, $DesignerDuration,  $endDesignerTime,$shiftingCrId = null)
    // {
        
    // //echo $designerId; die;
    //     // Find any overlapping CR for this developer, excluding the shifting CR
    //     $conflictingCR = Change_request::where('designer_id ', $designerId)
    //         ->where('id', '!=', $shiftingCrId) // Exclude the shifting CR
    //         ->where(function ($query) use ($startDesignerTime, $endDesignerTime) {
    //             // Overlapping conditions
    //             $query->whereBetween('start_design_time', [$startDesignerTime, $endDesignerTime]) // Starts during this period
    //                   ->orWhereBetween('end_design_time', [$startDesignerTime, $endDesignerTime]) // Ends during this period
    //                   ->orWhere(function ($query) use ($startDesignerTime, $endDesignerTime) {
    //                       // Encapsulates the entire period
    //                       $query->where('start_design_time', '<=', $startDesignerTime)
    //                             ->where('end_design_time', '>=', $endDesignerTime);
    //                   });
    //         })
    //         ->first();
            
    // die("ww");
    //     return $conflictingCR ? $conflictingCR : null;
    // }

    public function isDesignerBusy($designerId, $startDesignerTime, $DesignerDuration, $endDesignerTime, $shiftingCrId = null)
    {
        // Define the query
        $conflictingCRQuery = Change_request::where('designer_id', $designerId)
            ->where('id', '!=', $shiftingCrId) // Exclude the shifting CR
            ->where(function ($query) use ($startDesignerTime, $endDesignerTime) {
                // Overlapping conditions
                $query->whereBetween('start_design_time', [$startDesignerTime, $endDesignerTime]) // Starts during this period
                      ->orWhereBetween('end_design_time', [$startDesignerTime, $endDesignerTime]) // Ends during this period
                      ->orWhere(function ($query) use ($startDesignerTime, $endDesignerTime) {
                          // Encapsulates the entire period
                          $query->where('start_design_time', '<=', $startDesignerTime)
                                ->where('end_design_time', '>=', $endDesignerTime);
                      });
            })->first();
    
        // Print the SQL query
       
    
        return $conflictingCRQuery ? $conflictingCRQuery : null;
    }






    function isStartTimeInFuture($startTime)
    {
        // Parse the start time to a Carbon instance
        $start = Carbon::parse($startTime);
        $now = Carbon::now();
    
        // Check if the start time is in the future
        return $now->lessThan($start); // Returns true if now is before the start time
    }
    public function isTesterBusy($developerId, $startDevelopTime, $developDuration, $endDevelopTime, $shiftingCrId = null)
    {

        // Calculate the end develop time based on the start time and the duration
        //$endDevelopTime = Carbon::parse($startDevelopTime)->copy()->addMinutes($developDuration);
    
        // Find any overlapping CR for this developer, excluding the shifting CR
        $conflictingCR = Change_request::where('tester_id', $developerId)
            ->where('id', '!=', $shiftingCrId) // Exclude the shifting CR
            ->where(function ($query) use ($startDevelopTime, $endDevelopTime) {
                // Overlapping conditions
                $query->whereBetween('start_test_time', [$startDevelopTime, $endDevelopTime]) // Starts during this period
                      ->orWhereBetween('end_test_time', [$startDevelopTime, $endDevelopTime]) // Ends during this period
                      ->orWhere(function ($query) use ($startDevelopTime, $endDevelopTime) {
                          // Encapsulates the entire period
                          $query->where('start_test_time', '<=', $startDevelopTime)
                                ->where('end_test_time', '>=', $endDevelopTime);
                      });
            })
            ->first();
    
        return $conflictingCR ? $conflictingCR : null;
    }

    public function reorderTimes($crId)
{
   
    try {
        // Retrieve the specified CR by its ID
        $cr = Change_request::find($crId);

        if (!$cr) {
            return response()->json(['status' => 'error', 'message' => "Change Request with ID {$crId} not found."]);
        }

        // Initialize current time for the start of the CR
        $currentTime = Carbon::now()->timestamp;
      
        // Update the specified CR times
        if ($cr->design_duration > 0) {
          
            // Check if the current CR's start time is in the future
            if (isset($cr->start_design_time) && Carbon::parse($cr->start_design_time)->isFuture()) {
                // Proceed with shifting the design phase
              $startDesignTime = Carbon::createFromTimestamp($this->setToWorkingDate($currentTime)); 
        
        $endDesignTime = Carbon::parse(
                    $this->generate_end_date(
                        $startDesignTime->timestamp,
                        $cr->design_duration,
                        false,
                        $cr->designer_id,
                        'design'
                    )
                ) ;
      
                $conflictingDeveloperCRId = $this->isDesignerBusy($cr->designer_id, $startDesignTime, $cr->design_duration, $endDesignTime, $cr->id);


                if (!empty($conflictingDeveloperCRId->id)) {
                      $date1 =$conflictingDeveloperCRId->end_design_time;
                      $date3 =$conflictingDeveloperCRId->start_design_time;
                      $date4 =$cr->start_design_time;
                      $date3=  Carbon::createFromTimestamp(Carbon::parse($date3)->timestamp);

                      $date2= Carbon::now();  
                      $date2=  Carbon::createFromTimestamp($this->setToWorkingDate(Carbon::parse($date2)->timestamp));
                    
                   if($date2>$date3 ){
                $startDesignTime = Carbon::createFromTimestamp(Carbon::parse($date1)->timestamp);
        
                   }else {
                   $startDesignTime = Carbon::createFromTimestamp($this->setToWorkingDate(Carbon::parse($date2)->timestamp));
        
                   }
                  
 
                   if ($date3->isSameDay($date4) ||$date2->isSameDay($date3)){
                   
                    $startDesignTime = Carbon::createFromTimestamp(Carbon::parse($date2)->timestamp);

                   }
                }
$startDesignTime; 
               $endDesignTime = Carbon::parse(
                    $this->generate_end_date(
                        $startDesignTime->timestamp,
                        $cr->design_duration,
                        false,
                        $cr->designer_id,
                        'design'
                    )
                );  //die;
                // Update CR with new start and end times
                $cr->update([
                    'start_design_time' => $startDesignTime->format('Y-m-d H:i:s'),
                    'end_design_time' => $endDesignTime->format('Y-m-d H:i:s'),
                ]); 
                
                
            } 
        } else {
            // No design duration specified, set endDesignTime to the current timestamp
            $endDesignTime = $currentTime;
        }
    
if ($cr->develop_duration > 0) {
   
   
    // Check if the current CR's start develop time is in the future
    if (isset($cr->start_develop_time)) {
     // If the start time is in the future, set the start develop time to now
      
     $startDevelopTime = Carbon::createFromTimestamp((Carbon::parse($cr->end_design_time)->timestamp));
 $cr->end_design_time; 
        // Calculate the end time for development based on the new start time
       echo  $endDevelopTime =  Carbon::parse($this->generate_end_date(
            $startDevelopTime->timestamp,
            $cr->develop_duration,
            false,
            $cr->developer_id,
            'dev'
        )); 
        // Check if the developer is busy with another CR
        $conflictingDeveloperCRId = $this->isDeveloperBusy($cr->developer_id, $startDevelopTime, $cr->develop_duration, $endDevelopTime, $cr->id);
      
    
        if (!empty($conflictingDeveloperCRId->id)) {
              $date1 =$conflictingDeveloperCRId->start_develop_time;
             $date2= $cr->end_design_time; 
            
           if($date1>$date2 ){
            $startDevelopTime = Carbon::createFromTimestamp($this->setToWorkingDate(Carbon::parse($date1)->timestamp));

           }else {
            $startDevelopTime = Carbon::createFromTimestamp($this->setToWorkingDate(Carbon::parse($date2)->timestamp));

           }



            // Recalculate end time for development
            $endDevelopTime = Carbon::parse(
                $this->generate_end_date(
                    $startDevelopTime->timestamp,
                    $cr->develop_duration,
                    false,
                    $cr->developer_id,
                    'dev'
                )
            );
        } else{



            $cr->update([
                'start_develop_time' => $startDevelopTime->format('Y-m-d H:i:s'),
                'end_develop_time' => $endDevelopTime->format('Y-m-d H:i:s'),
            ]);

        }

        // Update the CR with the calculated start and end times
        $cr->update([
            'start_develop_time' => $startDevelopTime->format('Y-m-d H:i:s'),
            'end_develop_time' => $endDevelopTime->format('Y-m-d H:i:s'),
        ]);
    }
    else {

        $endDevelopTime = Carbon::parse($endDesignTime);
    } 
} 

if ($cr->test_duration > 0) {
   
    // Calculate initial start time based on end develop time
     $startTestTime = Carbon::createFromTimestamp($this->setToWorkingDate(Carbon::parse($cr->end_develop_time)->timestamp));
   
    // Check if the current CR's start test time is in the future
    if (isset($cr->start_test_time) ) {
        // Set start test time to end develop time if it's in the future
         $startTestTime = Carbon::parse($cr->end_develop_time);  
         if ($startTestTime->isPast()) {
            $startTestTime = Carbon::now();
        }
    }

    // Calculate the end time for testing
    $endTestTime = Carbon::parse(
        $this->generate_end_date(
            $startTestTime->timestamp,
            $cr->test_duration,
            false,
            $cr->tester_id,
            'test'
        )
    ); 

    // Check if the tester is busy with another CR
    $conflictingTesterCRId = $this->isTesterBusy($cr->tester_id, $startTestTime, $cr->test_duration,$endTestTime, $cr->id);
   

    if (!empty($conflictingTesterCRId->id)) {
     
    $date1 =$conflictingTesterCRId->start_test_time; 
    $date2= $cr->end_develop_time;  
       
      if($date1>$date2 ){
       $startTestTime = Carbon::createFromTimestamp($this->setToWorkingDate(Carbon::parse($date1)->timestamp));

      }else {
       $startTestTime = Carbon::createFromTimestamp($this->setToWorkingDate(Carbon::parse($date2)->timestamp));

      }
        
       // $startTestTime = Carbon::createFromTimestamp($this->setToWorkingDate(Carbon::parse($conflictingTesterCRId->end_test_time)->timestamp));

        // Recalculate end time for testing
        $endTestTime = Carbon::parse(
            $this->generate_end_date(
                $startTestTime->timestamp,
                $cr->test_duration,
                false,
                $cr->tester_id,
                'test'
            )
        );
    }

    // Update the CR with the calculated start and end times, only if start_test_time has not been altered previously
    if (!isset($cr->start_test_time) || Carbon::parse($cr->start_test_time)->isFuture()) {
        $cr->update([
            'start_test_time' => $startTestTime->format('Y-m-d H:i:s'),
            'end_test_time' => $endTestTime->format('Y-m-d H:i:s'),
        ]);
    }
} else {
    // No testing duration specified, use endDevelopTime as the default
    $endTestTime = Carbon::parse($endDevelopTime);
}



     $queue = Change_request::where(function ($query) use ($cr) {
            $query->where('designer_id', $cr->designer_id)
                ->orWhere('developer_id', $cr->developer_id)
                ->orWhere('tester_id', $cr->tester_id);
        })
        ->where('id', '!=', $crId) // Exclude the current CR
        ->where(function ($query) use ($cr) {
            // Ensure that we're filtering only by designer_id, developer_id, or tester_id
            $query->where('designer_id', $cr->designer_id)
                ->orWhere('developer_id', $cr->developer_id)
                ->orWhere('tester_id', $cr->tester_id);
        })
        ->orderBy('id') // Order by a priority or ID (customize if needed)
        ->get();
       
        // Reorder each CR in the queue based on the previous CR's end time
        foreach ($queue as $queuedCr) {
            if (
                (isset($conflictingDeveloperCRId->id) && $queuedCr->id == $conflictingDeveloperCRId->id) || 
                (isset($conflictingTesterCRId->id) && $conflictingTesterCRId->id == $queuedCr->id)
            ) {
               
            
                // Check if the start times are in the future
                $dev = isset($conflictingDeveloperCRId->start_develop_time) ? $this->isStartTimeInFuture($conflictingDeveloperCRId->start_develop_time) : false;
                $test = isset($conflictingTesterCRId->start_test_time) ? $this->isStartTimeInFuture($conflictingTesterCRId->start_test_time) : false;
            
                // If either is not in the future, skip this iteration
                if (!$dev || !$test) {
                    continue;
                }
            }
            
          
            // Design Phase: Set the next CR's start time based on the current CR's end time
            if ($queuedCr->designer_id == $cr->designer_id && !empty($queuedCr->design_duration) && $queuedCr->design_duration > 0) {
                // Use the current CR's end design time as the next CR's start design time
                
                $startDesignTime = Carbon::parse($endDesignTime); // The current CR's end design time
                $endDesignTime = Carbon::parse(
                    $this->generate_end_date(
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

            // Development Phase: Set the next CR's start time based on the current CR's end design time
            if ($queuedCr->developer_id == $cr->developer_id && !empty($queuedCr->develop_duration) && $queuedCr->develop_duration > 0 ) {
                // Use the current CR's end design time as the next CR's start develop time
             //  echo $queuedCr->id; die;
             $startDevelopTime = Carbon::parse($cr->end_develop_time); // The current CR's end design time
           
               $endDevelopTime = Carbon::parse(
                    $this->generate_end_date(
                        $startDevelopTime->timestamp,
                        $queuedCr->develop_duration,
                        false,
                        $queuedCr->developer_id,
                        'dev'
                    )
                );
                if ($this->isDeveloperUnavailable($queuedCr->developer_id, $startDevelopTime, $endDevelopTime, $queuedCr->id)) {
                    // Get the earliest available time
                    $startDevelopTime = $this->getFirstAvailableTime($queuedCr->developer_id, $startDevelopTime, $endDevelopTime, $queuedCr->id);
                
                    // Adjust end time based on the new start time
                    $endDevelopTime = Carbon::parse(
                        $this->generate_end_date(
                            $startDevelopTime->timestamp,
                            $cr->develop_duration,
                            false,
                            $cr->developer_id,
                            'dev'
                        )
                    );
                }
                $queuedCr->update([
                    'start_develop_time' => $startDevelopTime->format('Y-m-d H:i:s'),
                    'end_develop_time' => $endDevelopTime->format('Y-m-d H:i:s'),
                ]);
            }

            // Test Phase: Set the next CR's start time based on the current CR's end develop time
            if ($queuedCr->tester_id == $cr->tester_id && !empty($queuedCr->test_duration) && $queuedCr->test_duration > 0) {
                // Use the current CR's end develop time as the next CR's start test time
                $startTestTime = Carbon::parse($endDevelopTime); // The current CR's end develop time
                $endTestTime = Carbon::parse(
                    $this->generate_end_date(
                        $startTestTime->timestamp,
                        $queuedCr->test_duration,
                        false,
                        $queuedCr->tester_id,
                        'test'
                    )
                );
                if ($this->isTesterUnavailable($queuedCr->tester_id, $startTestTime, $endTestTime, $queuedCr->id)) {
                    // Get the earliest available time for the tester
                    $startTestTime = $this->getFirstAvailableTimefortest($queuedCr->tester_id, $startTestTime, $endTestTime, $queuedCr->id);
                
                    // Adjust end time based on the new start time
                    $endTestTime = Carbon::parse(
                        $this->generate_end_date(
                            $startTestTime->timestamp,
                            $cr->test_duration,
                            false,
                            $cr->tester_id,
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
        return [
            'status' => true,
            'message' => "Successfully reordered times for CR ID {$crId} and related queued CRs.",
        ];
        
    } catch (\Exception $e) {

        return [
            'status' => false,
            'message' => 'An error occurred while reordering the times: ' . $e->getMessage(),
        ];
    }
    
    return [
        'status' => true,
        'message' => "Successfully reordered times for CR ID {$crId} and related queued CRs.",
    ];
}
private function getFirstAvailableTimefortest($entityId, $startTime, $endTime, $excludeCrId)
{
    
        $unavailablePeriods = Change_request::where('tester_id', $entityId)
        ->where('id', '!=', $excludeCrId) // Exclude the current CR
        ->where(function ($query) use ($startTime, $endTime) {
            $query->whereBetween('start_test_time', [$startTime, $endTime]) // Overlaps start
                ->orWhereBetween('end_test_time', [$startTime, $endTime]) // Overlaps end
                ->orWhere(function ($q) use ($startTime, $endTime) { // Fully overlaps
                    $q->where('start_test_time', '<', $startTime)
                        ->where('end_test_time', '>', $endTime);
                });
        })
        ->orderBy('end_test_time', 'asc') // Order by end time to find the earliest availability
        ->get(['start_test_time', 'end_test_time']);


    // Find the first available slot after these periods
    $availabilityStart = Carbon::parse($startTime);
    foreach ($unavailablePeriods as $period) {
        $periodEnd = Carbon::parse($period->end_test_time);
        if ($periodEnd->greaterThan($availabilityStart)) {
            $availabilityStart = $periodEnd; // Move start time after this period ends
        }
    }

    return $availabilityStart;
}
/**
 * Check if a developer is unavailable for a given period.
 *
 * @param int $developerId The ID of the developer to check.
 * @param string $startTime The start time of the period to check.
 * @param string $endTime The end time of the period to check.
 * @param int $excludeCrId The ID of the current CR to exclude from the check.
 * @return bool True if the developer is unavailable, false otherwise.
 */

 private function getFirstAvailableTime($developerId, $startTime, $endTime, $excludeCrId)
{
    $unavailablePeriods = Change_request::where('developer_id', $developerId)
        ->where('id', '!=', $excludeCrId) // Exclude the current CR
        ->where(function ($query) use ($startTime, $endTime) {
            $query->whereBetween('start_develop_time', [$startTime, $endTime]) // Overlaps start
                ->orWhereBetween('end_develop_time', [$startTime, $endTime]) // Overlaps end
                ->orWhere(function ($q) use ($startTime, $endTime) { // Fully overlaps
                    $q->where('start_develop_time', '<', $startTime)
                        ->where('end_develop_time', '>', $endTime);
                });
        })
        ->orderBy('end_develop_time', 'asc') // Order by end time to find the earliest availability
        ->get(['start_develop_time', 'end_develop_time']);

    // Find the first available slot after these periods
    $availabilityStart = Carbon::parse($startTime);
    foreach ($unavailablePeriods as $period) {
        $periodEnd = Carbon::parse($period->end_develop_time);
        if ($periodEnd->greaterThan($availabilityStart)) {
            $availabilityStart = $periodEnd; // Move start time after this period ends
        }
    }

    return $availabilityStart;
}
private function isDeveloperUnavailable($developerId, $startTime, $endTime, $excludeCrId)
{
    return Change_request::where('developer_id', $developerId)
        ->where('id', '!=', $excludeCrId) // Exclude the current CR
        ->where(function ($query) use ($startTime, $endTime) {
            $query->whereBetween('start_develop_time', [$startTime, $endTime]) // Overlaps start
                ->orWhereBetween('end_develop_time', [$startTime, $endTime]) // Overlaps end
                ->orWhere(function ($q) use ($startTime, $endTime) { // Fully overlaps
                    $q->where('start_develop_time', '<', $startTime)
                        ->where('end_develop_time', '>', $endTime);
                });
        })
        ->exists();
}

/**
 * Check if a tester is unavailable for a given period.
 *
 * @param int $testerId The ID of the tester to check.
 * @param string $startTime The start time of the period to check.
 * @param string $endTime The end time of the period to check.
 * @param int $excludeCrId The ID of the current CR to exclude from the check.
 * @return bool True if the tester is unavailable, false otherwise.
 */
private function isTesterUnavailable($testerId, $startTime, $endTime, $excludeCrId)
{
    return Change_request::where('tester_id', $testerId)
        ->where('id', '!=', $excludeCrId) // Exclude the current CR
        ->where(function ($query) use ($startTime, $endTime) {
            $query->whereBetween('start_test_time', [$startTime, $endTime]) // Overlaps start
                ->orWhereBetween('end_test_time', [$startTime, $endTime]) // Overlaps end
                ->orWhere(function ($q) use ($startTime, $endTime) { // Fully overlaps
                    $q->where('start_test_time', '<', $startTime)
                        ->where('end_test_time', '>', $endTime);
                });
        })
        ->exists();
}

/**
 * Reorder times for a given change request.
 *
 * @param int $crId The ID of the change request to reorder.
 * @return array The result of the operation.
 */


public function Change_request($crId)
{
    try {
        $cr = ChangeRequest::findOrFail($crId);

        // Ensure availability and calculate times
        $startDesignTime = Carbon::createFromFormat('Y-m-d H:i:s', $cr->start_design_time);
        $endDesignTime = $this->generate_end_date(
            $this->setToWorkingDate($startDesignTime->timestamp),
            $cr->design_duration,
            false,
            $cr->designer_id,
            'design'
        );

        if (!$this->checkAvailability($cr->designer_id, $startDesignTime, $endDesignTime)) {
            $startDesignTime = $this->findNextAvailableTime($cr->designer_id, $startDesignTime);
            $endDesignTime = $this->generate_end_date(
                $this->setToWorkingDate($startDesignTime->timestamp),
                $cr->design_duration,
                false,
                $cr->designer_id,
                'design'
            );
        }

        $startDevelopTime = Carbon::parse($this->setToWorkingDate($endDesignTime->timestamp));
        $endDevelopTime = $this->generate_end_date(
            $startDevelopTime->timestamp,
            $cr->develop_duration,
            false,
            $cr->developer_id,
            'dev'
        );

        if (!$this->checkAvailability($cr->developer_id, $startDevelopTime, $endDevelopTime)) {
            $startDevelopTime = $this->findNextAvailableTime($cr->developer_id, $startDevelopTime);
            $endDevelopTime = $this->generate_end_date(
                $startDevelopTime->timestamp,
                $cr->develop_duration,
                false,
                $cr->developer_id,
                'dev'
            );
        }

        $startTestTime = Carbon::parse($this->setToWorkingDate($endDevelopTime->timestamp));
        $endTestTime = $this->generate_end_date(
            $startTestTime->timestamp,
            $cr->test_duration,
            false,
            $cr->tester_id,
            'test'
        );

        if (!$this->checkAvailability($cr->tester_id, $startTestTime, $endTestTime)) {
            $startTestTime = $this->findNextAvailableTime($cr->tester_id, $startTestTime);
            $endTestTime = $this->generate_end_date(
                $startTestTime->timestamp,
                $cr->test_duration,
                false,
                $cr->tester_id,
                'test'
            );
        }

        // Update CR with new times
        $cr->start_design_time = $startDesignTime->toDateTimeString();
        $cr->end_design_time = Carbon::parse($endDesignTime)->toDateTimeString();
        $cr->start_develop_time = $startDevelopTime->toDateTimeString();
        $cr->end_develop_time = Carbon::parse($endDevelopTime)->toDateTimeString();
        $cr->start_test_time = $startTestTime->toDateTimeString();
        $cr->end_test_time = Carbon::parse($endTestTime)->toDateTimeString();

        $cr->save();

        return [
            'status' => true,
            'message' => "Times reordered successfully for CR ID {$crId}.",
        ];
    } catch (\Exception $e) {
        \Log::error("Error reordering times for CR ID {$crId}: " . $e->getMessage());
        return [
            'status' => false,
            'message' => "Error reordering times for CR ID {$crId}: " . $e->getMessage(),
        ];
    }
}

/**
 * Check if a user is available in the given timeframe.
 */
public function checkAvailability($userId, $startTime, $endTime)
{
    $overlappingTasks = ChangeRequest::where(function ($query) use ($startTime, $endTime) {
        $query->whereBetween('start_design_time', [$startTime, $endTime])
            ->orWhereBetween('end_design_time', [$startTime, $endTime])
            ->orWhereBetween('start_develop_time', [$startTime, $endTime])
            ->orWhereBetween('end_develop_time', [$startTime, $endTime])
            ->orWhereBetween('start_test_time', [$startTime, $endTime])
            ->orWhereBetween('end_test_time', [$startTime, $endTime]);
    })
    ->where(function ($query) use ($userId) {
        $query->where('designer_id', $userId)
            ->orWhere('developer_id', $userId)
            ->orWhere('tester_id', $userId);
    })
    ->exists();

    return !$overlappingTasks;
}

/**
 * Find the next available time for a user.
 */
public function findNextAvailableTime($userId, $currentTime)
{
    $lastTask = ChangeRequest::where(function ($query) use ($userId) {
        $query->where('designer_id', $userId)
            ->orWhere('developer_id', $userId)
            ->orWhere('tester_id', $userId);
    })
    ->where('end_design_time', '<=', $currentTime)
    ->orWhere('end_develop_time', '<=', $currentTime)
    ->orWhere('end_test_time', '<=', $currentTime)
    ->orderByDesc('end_design_time')
    ->orderByDesc('end_develop_time')
    ->orderByDesc('end_test_time')
    ->first();

    if ($lastTask) {
        return Carbon::parse($this->setToWorkingDate(strtotime($lastTask->end_test_time)));
    }

    return $currentTime;
}


    
    // Helper function to calculate end time based on duration and working hours
    private function calculateEndTime($startTime, $duration)
    {
        // Convert the start time to the correct working start time
        $startTimeInWorkingHours = $this->setToWorkingDate($startTime);

        // Calculate end time by adding the work duration in days
        $endTime = $startTimeInWorkingHours;

        // Add working hours (4 hours per day, in your case)
        $endTime = strtotime("+{$duration} days", $endTime); // Duration is in days

        return date('Y-m-d H:i:s', $endTime);
    }


    public function LastCRNo()
    {
        $ChangeRequest = Change_request::orderby('id', 'desc')->first();

        return isset($ChangeRequest) ? $ChangeRequest->cr_no + 1 : 1;
    }

    public function ShowChangeRequestData($id, $group)
    { //$str = Change_request::with('current_status.status.to_status_workflow.to_status')
        //$group = 10;

        $str = Change_request::with(['current_status' => function ($q) use ($group) {
            $q->where('group_statuses.group_id', $group)->with('status.to_status_workflow');
        }])->where('id', $id)->get();
        // return Debugbar::info($str->toArray());
        return $str;
    }


    public function update($id, $request)
    {
        
        
        if($request->cab_cr_flag == '1')
        {
            $cr = Change_request::find($id);
            $user_id = Auth::user()->id;
            $CabCr = CabCr::where("cr_id",$id)->where('status','0')->first();
            $status = $request->new_status_id;
            $new_workflow  = new NewWorkFlow();
            //$to_statsus =  $new_workflow->workflowstatus[$status]->new_workflow_id;
            unset($request['cab_cr_flag']);
            if($status == '37')//reject
            {
                $CabCr->status = '2';
                $CabCr->save();
                $CabCr->cab_cr_user()->where('user_id', $user_id)->update([
                    'status' => '2'
                ]);
            }
            else//approve
            {           
                $CabCr->cab_cr_user()->where('user_id', $user_id)->update([
                    'status' => '1'
                ]);
            
                $count_all_users = $CabCr->cab_cr_user->count();// get count for all users that are approve CR
                $count_approved_users = $CabCr->cab_cr_user->where('status','1')->count();// get count for all users that need to take action on cr}
                if($count_all_users > $count_approved_users)
                {
                    return true;
                }
                else
                {
                    $CabCr->status = '1';
                    $CabCr->save();
                }
            }
            
        }
        unset($request['cab_cr_flag']); 
        
       $new_status_id = null;
       if($request->new_status_id) $new_status_id = $request->new_status_id;


       $old_status_id = null;
       if($request->old_status_id) $old_status_id = $request->old_status_id;


        if ($request['assign_to']) {
            $user = User::find($request['assign_to']);
        } else {
            $user = \Auth::user();
        }

        

        if(!empty($request->cap_users))
        { 
        $record = CabCr::create([
            'cr_id' => $id,
            'status' => "0",
            
        ]);

        $insertedId = $record->id;

        
            foreach ($request->cap_users as $userId) {
                CabCrUser::create([
                    'user_id' => $userId,
                    'cab_cr_id' => $insertedId,
                    'status' => "0",
                ]);
            
            }
        }
        

        //dd($user->role_id);
        $change_request = Change_request::find($id);
        /** check assignments */
        if ((isset($request['dev_estimation'])) || (isset($request['testing_estimation'])) || (isset($request['design_estimation'])) || ($request['assign_to'])) {
             $request['assignment_user_id'] = $user->id;
        }
       
         
/** end check */
        $except = ['old_status_id', 'new_status_id', '_method', 'current_status', 'duration', 'current_status', 'categories', 'cat_name', 'pr_name', 'Applications', 'app_name', 'depend_cr_name', 'depend_crs', 'test', 'priorities', 'cr_id', 'assign_to', 'dev_estimation', 'design_estimation', 'testing_estimation', 'assignment_user_id', '_token', 'attach', 'business_attachments', 'technical_attachments', 'cap_users','analysis_feedback','technical_feedback','need_ux_ui','business_feedback','rejection_reason_id'];

        // calculate estimation
        if ((isset($request['dev_estimation']) && $request['dev_estimation'] != '') || (isset($request['design_estimation']) && $request['design_estimation'] != '') || (isset($request['testing_estimation']) && $request['testing_estimation'] != '')) 
        {
            
            $data = $this->calculateEstimation($id,$change_request,$request,$user);
            $request->merge($data);
        }
        
        $this->changeRequest_old = Change_request::find($id);
        $arr = Arr::except($request, $except);
        //$data = $arr->all();
        //$arr = $request->except($except);
        $data = $request->except($except);
        //dd($data);
        
        
        foreach ($data as $key => $value) {
            if($key != "_token")
            {
                $custom_field_id = CustomField::findId($key);
                if($custom_field_id && $value)
                {
                    $change_request_custom_field = array(
                        "cr_id" =>$id,
                        "custom_field_id" =>$custom_field_id->id,
                        "custom_field_name" =>$key,
                        "custom_field_value" =>$value,
                    );
                    $this->InsertOrUpdateChangeRequestCustomField($change_request_custom_field);
                }
            }
            
        }
       
        $changeRequest = Change_request::where('id', $id)->update($arr->except($except));
        


        


        //$request['assignment_user_id'] = $user->id;
        if($new_status_id) $request['new_status_id'] = $new_status_id;
        if($old_status_id) $request['old_status_id'] = $old_status_id;
        //dd($request->all(),$new_status_id);
        if($request->new_status_id) $this->UpateChangeRequestStatus($id, $request);
        $this->StoreLog($id, $request, 'update');
        return $changeRequest;
    }
    public function GetLastCRDate($id, $user_id, $column, $end_date_column, $duration, $action)
    {
       
        //$user = \Auth::user();
        $last_end_date = Change_request::where($column, $user_id)->where('id', '!=', $id)->max($end_date_column);
        if ($last_end_date == '' or $last_end_date < date('Y-m-d H:i:s')) {
            $new_start_date = date('Y-m-d H:i:s', strtotime('+3 hours'));
        } else {
            $new_start_date = date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($last_end_date)));
        }

        $new_start_date = date('Y-m-d H:i:s', $this->setToWorkingDate(strtotime($new_start_date)));
        $now = Carbon::now();
        if (!Carbon::parse($new_start_date)->gt(Carbon::now()))
        {
            $new_start_date = date('Y-m-d H:i:s');
        }
        //$new_start_date = date("Y-m-d H:i:s", strtotime('+3 hours', strtotime($new_start_date)));
        $new_end_date = $this->generate_end_date($this->setToWorkingDate(strtotime($new_start_date)), $duration, 0, $user_id, $action);

        return [$new_start_date, $new_end_date];
    }

    // new updates

   // $dates = $this->GetLastEndDate($id,  $change_request['developer_id'], 'developer_id',  $request['end_design_time'],  $change_request['test_duration'] , 'dev');

    public function GetLastEndDate($id, $user_id, $column, $last_end_date, $duration, $action)
    {
        //$user = \Auth::user();
      //  $last_end_date = Change_request::where($column, $user_id)->where('id', '!=', $id)->max($end_date_column);
       // if ($last_end_date == '' or $last_end_date < date('Y-m-d H:i:s')) {
            $new_start_date = date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($last_end_date)));
       // } else {
           // $new_start_date = date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($last_end_date)));
       // }

        $new_start_date = date('Y-m-d H:i:s', $this->setToWorkingDate(strtotime($new_start_date)));
        $now = Carbon::now();
        if (!Carbon::parse($new_start_date)->gt(Carbon::now()))
        {
            $new_start_date = date('Y-m-d H:i:s');
        }
        
        //$new_start_date = date("Y-m-d H:i:s", strtotime('+3 hours', strtotime($new_start_date)));
        $new_end_date = $this->generate_end_date($this->setToWorkingDate(strtotime($new_start_date)), $duration, 0, $user_id, $action);
        return [$new_start_date, $new_end_date];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id      ,@param array $request
     *
     * @return \Illuminate\Http\Response
     *                                   Hint: check if it is normal or not and has depend status or not
     */
    public function UpateChangeRequestStatus($id, $request)
    {
        //dd($id,$request->all());
/** check estimation user without changing in status */
        if (!isset($request->new_status) && isset($request->assignment_user_id)) {
            Change_request_statuse::where('cr_id', $id)->where('new_status_id', $request->old_status_id)->where('active', '1')->update(['assignment_user_id' => $request->assignment_user_id]);
        }
/**end  check estimation  */
        if (isset($request['new_status_id'])) {
            $new_status_id = $request['new_status_id'];  
        } 
        elseif (isset($request->new_status_id)) {
           $new_status_id = $request->new_status_id;  
        }
        $workflow = NewWorkFlow::find($new_status_id);
        //dd($workflow, $workflow->workflowstatus);
        if(isset(\Auth::user()->id) && \Auth::user()->id != null)
        {
            $user_id = \Auth::user()->id   ;    
        }else{
            $user_id = $request['assign_to'] ;
        }
         


         if (isset($request['old_status_id'])) {
           
            $old_status_id = $request['old_status_id'];  
        } elseif (isset($request->old_status_id) ) {
           
            $old_status_id = $request->old_status_id;  
        }


        if ($workflow) {
            $workflow_active = $workflow->workflow_type == 1 ? '0' : '2';
   
            $cr_status = Change_request_statuse::where('cr_id', $id)->where('new_status_id',  $old_status_id)->where('active', '1')->first();
            //dd($id, $request,$cr_status);
            $date = Carbon::parse($cr_status->created_at);
            $now = Carbon::now();
            $diff = $date->diffInDays($now);

            $cr_status->sla_dif = $diff;
            $cr_status->active = $workflow_active;
            $cr_status->save();
            $depend_statuses = Change_request_statuse::where('cr_id', $id)->where('old_status_id', $cr_status->old_status_id)->where('active', '1')->get();
            $active = '1';
            
            if ($workflow_active) { // check if it is normal work flow
                $check_depend_workflow = NewWorkFlow::whereHas('workflowstatus', function ($q) use ($workflow) {
                    $q->where('to_status_id', $workflow->workflowstatus[0]->to_status_id);
                })->pluck('from_status_id');
                $active = $depend_statuses->count() > 1 ? '0' : '1';
                $check_depend_status = Change_request_statuse::where('cr_id', $id)->whereIN('new_status_id', $check_depend_workflow)->where('active', '1')->count();
                if ($check_depend_status > 0) {
                    $active = '0';
                }
            } else { // check if it is abnormal work flow
                foreach ($depend_statuses as $item) {
                    Change_request_statuse::where('id', $item->id)->update(['active' => '0']);
                }
            }

            $change_request_status = new ChangeRequestStatusRepository();

            foreach ($workflow->workflowstatus as $key => $item) {
                $workflow_check_active = 0;
                if($item->dependency_ids)
                {
                    $dependency_ids_array = $item->dependency_ids;
                    $to_remove = array($item->new_workflow_id);
                    $result = array_diff($dependency_ids_array, $to_remove);
                    foreach($result as $x=>$worflow_status)
                    {
                        //dd($worflow_status);
                        $depend_workflow = NewWorkFlow::find($worflow_status);
                        $check_depend_workflow_status = Change_request_statuse::where('cr_id', $id)->where('new_status_id', $depend_workflow->from_status_id)->where('old_status_id', $depend_workflow->previous_status_id)->where('active', '1')->count();
                        if($check_depend_status)
                        {
                            $active='0';
                            break;
                        }
                    }
                }
                
                //dd($item,$item->dependency_ids,$result);
                // if ($workflow->workflow_type != 1) {
                //     $workflow_check_active = Change_request_statuse::where('cr_id', $id)->where('new_status_id', $item->to_status_id)->where('active', '2')->first();
                // }
                if (!$workflow_check_active) {
                    $status_sla = Status::find($item->to_status_id);
                    if($status_sla)
                    {
                        $status_sla = $status_sla->sla;
                    }
                    else
                    {
                        $status_sla = 0;
                    }    
                    $data = [
                        'cr_id' => $id,
                        'old_status_id' => $request['old_status_id'],
                        'new_status_id' => $item->to_status_id,
                        'user_id' => $user_id,
                        'sla' => $status_sla,
                        'active' => $active,
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
        $status_sla = Status::find($request['new_status_id']);
        if($status_sla)
        {
            $status_sla = $status_sla->sla;
        }
        else
        {
            $status_sla = 0;
        }
        $user_id = \Auth::user()->id; // 3;
        $data = [
            'cr_id' => $cr_id,
            'old_status_id' => $request['old_status_id'],
            'new_status_id' => $request['new_status_id'],
            'sla' => $status_sla,
            'user_id' => $user_id,
            //'updated_at' => NULL,
            'active' => '1',
        ];
        $change_request_status->create($data);

        return true;
    }

    public function getWorkFollowDependOnApplication($id)
    {
        $app = Application::where('id', $id)->first();
        return $app->workflow_type_id;
    }
    public function findWithReleaseAndStatus($id)
    {
      
        // Fetch the Change Request with related Release and Release Status
    
     
     return   $changeRequest= Change_request::with('release')->find($id);
     
        // if ($changeRequest) {
        //     // Print the ChangeRequest, Release, and ReleaseStatus
        //     echo '<pre>';
        //     print_r($changeRequest->toArray());
        //     echo '</pre>'; die('ggg');
        // }
        // die("walid");
    }
    public function create($request)
    {
        
        //if ($request['workflow_type_id'] == 3) {
            //$request['workflow_type_id'] = $this->getWorkFollowDependOnApplication($request['application_id']);
       // }

        // unset($request['active']);
        // unset($request['testable']);
        $workflow = new NewWorkflowRepository();
        $defualt_satatus = $workflow->getFirstCreationStatus($request['workflow_type_id'])->from_status_id;
        //$defualt_satatus=3;
        $new_cr_id = $this->LastCRNo();
        $request['requester_id'] = \Auth::user()->id;
        $request['requester_name'] = \Auth::user()->user_name;
        $request['requester_email'] = \Auth::user()->email;
        //  $request['active'] = $request['active'];
        $request['cr_no'] = $new_cr_id;
        $request['old_status_id'] = $defualt_satatus;
        $request['new_status_id'] = $defualt_satatus;

        //dd($request);
        

       $create_data = Arr::except($request, ['old_status_id', 'new_status_id', '_method', 'current_status', 'duration', 'current_status', 'categories', 'cat_name', 'pr_name', 'Applications', 'app_name', 'depend_cr_name', 'depend_crs', 'test', 'priorities', 'cr_id', 'assign_to', 'dev_estimation', 'design_estimation', 'testing_estimation', 'assignment_user_id', '_token', 'attach', 'business_attachments', 'technical_attachments', 'cap_users','analysis_feedback','technical_feedback','need_ux_ui','business_feedback','rejection_reason_id','cr_member','cr_no']);
        $change_request = Change_request::create($create_data);
        
        //$data = $request;
        $data = Arr::except($request, ['technical_attachments', 'business_attachments']);
        //dd($data);
        foreach ($data as $key => $value) {
            if($key != "_token")
            {
                $custom_field_id = CustomField::findId($key);
                if($custom_field_id && $value){
                    $change_request_custom_field = array(
                        "cr_id" =>$change_request->id,
                        "custom_field_id" =>$custom_field_id->id,
                        "custom_field_name" =>$key,
                        "custom_field_value" =>$value,
                    );
                    $this->InsertOrUpdateChangeRequestCustomField($change_request_custom_field);
                }
            }
        }
        

        $this->StoreChangeRequestStatus($change_request->id, $request);

        $this->StoreLog($change_request->id, $request, 'create');

        // send mail 

        $mailController = new MailController();

        // send mail to requester
        $mailController->notifyRequesterCrCreated($request['requester_email'] , $change_request->id);

        // send mail to division manager
        $mailController->notifyDivisionManager($request['division_manager'] , $request['requester_email'], $change_request->id ,$request['title'] , $request['description'] , $request['requester_name']);

        return $change_request->id;
    }

    public function getAll($group=null)
    {

        
        if(empty($group)){
            if(session('default_group')){
                $group = session('default_group');
    
            }else {
                $group = auth()->user()->default_group;
            }


        }
        
        //$group = request()->header('group');
        //dd($group);
        $view_statuses = $this->getViewStatuses($group);
        
        
        $changeRequests = Change_request::with('RequestStatuses.status')->whereHas('RequestStatuses', function ($query) use ($group, $view_statuses) {
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
        return Change_request::destroy($id);
    }


    public function findCr($id){
        $groups = auth()->user()->user_groups->pluck('group_id')->toArray();
        
        $view_statuses = $this->getViewStatuses($groups);
        $changeRequest = Change_request::with(['category','defects'])->with('attachments',
            function ($q) use ($groups) {
                $q->with('user');
                if (!in_array(8, $groups)) {
                    $q->whereHas('user', function ($q) {
                        if (\Auth::user()->flag == '0') {
                            $q->where('flag', \Auth::user()->flag);
                        }
                        $q->where('visible', 1);
                    });
                }
            })->where('id', $id)->first();
            // $changeRequest =    $changeRequest->whereHas('RequestStatuses', function ($query) use ($groups, $view_statuses) {
            // $query->where('active', '1')->whereIn('new_status_id', $view_statuses)
            //     ->whereHas('status.group_statuses', function ($query) use ($groups) {
            //         // Check if the groups array does not contain group_id 19 or 8
            //         if (!in_array(19, $groups) && !in_array(8, $groups)) {
            //             $query->whereIn('group_id', $groups);
            //         }
            //         $query->where('type', 2);
            //     });
        
    
        if ($changeRequest) {
            $changeRequest->current_status = $current_status = $this->getCurrentStatusCab($changeRequest, $view_statuses);
            //dd($current_status);
            $changeRequest->set_status = $this->GetSetStatus($current_status, $changeRequest->workflow_type_id);
        }
    
        $assigned_user = $this->AssignToUsers();
        if ($assigned_user) {
            $changeRequest->assign_to = $this->AssignToUsers();
        }
    
        return $changeRequest;
    }

    public function find($id)
    {
        
        $groups = auth()->user()->user_groups->pluck('group_id')->toArray();
        
        $view_statuses = $this->getViewStatuses($groups);

        $changeRequest = Change_request::with('category')->with('attachments',
            function ($q) use ($groups) {
                $q->with('user');
                if (!in_array(8, $groups)) {
                    $q->whereHas('user', function ($q) {
                        if (\Auth::user()->flag == '0') {
                            $q->where('flag', \Auth::user()->flag);
                        }
                        $q->where('visible', 1);
                    });
                }
            });
            $changeRequest =    $changeRequest->whereHas('RequestStatuses', function ($query) use ($groups, $view_statuses) {
            $query->where('active', '1')->whereIn('new_status_id', $view_statuses)
                ->whereHas('status.group_statuses', function ($query) use ($groups) {
                    // Check if the groups array does not contain group_id 19 or 8
                    if (!in_array(19, $groups) && !in_array(8, $groups)) {
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
        if(session('default_group')){
            $group = session('default_group');

        }else {
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

            //if ($group != 19 && $group != 8) {
                $view_statuses = $view_statuses->where('group_id', $group)->where('type', 2);
               
            //}
        }
        
    
        // Fetch and return the statuses related to the group(s)
        $view_statuses = $view_statuses->groupBy('status_id')->get()->pluck('status_id');
        
        return $view_statuses;
    }
    
    public function getCurrentStatusCab($changeRequest, $view_statuses)
    {
        $current_status = Change_request_statuse::where('cr_id', $changeRequest->id)->where('active', '1')->first();

        return $current_status;
    }

    public function getCurrentStatus($changeRequest, $view_statuses)
    {
        $current_status = Change_request_statuse::where('cr_id', $changeRequest->id)->whereIn('new_status_id', $view_statuses)->where('active', '1')->first();

        return $current_status;
    }

    public function GetSetStatus($current_status, $type_id)
    {
        //dd($current_status);
        $status_id = $current_status->new_status_id;
        $previous_status_id = $current_status->old_status_id;
        $set_status = NewWorkFlow::where('from_status_id', $status_id)->where(function($query) use ($previous_status_id){
            $query->WhereNull('previous_status_id');
            $query->ORwhere('previous_status_id',$previous_status_id);
        })->whereHas('workflowstatus', function ($q) {
            $q->whereColumn('to_status_id', '!=', 'new_workflow.from_status_id');
        })->where('type_id', $type_id)->where('active','1')->orderby('id', 'DESC')->get();
        //$set_status = 1;

        return $set_status;
    }

    public function AssignToUsers()
    {
        $user_id = \Auth::user()->id;
        $assign_to = User::whereHas('user_report_to', function ($q) use ($user_id) {
            $q->where('report_to', $user_id)->where('user_id', '!=', $user_id);
        })->get();
        $assign_to = count($assign_to) > 0 ? $assign_to : null;
        return $assign_to;
    }

  

    public function setToWorkingDate($date)
{
    if ($date instanceof \Carbon\Carbon) {
        $date = $date->timestamp;
    }

    // If the start day is Saturday, it will be Sunday
    if (((int) date('w', $date)) == 6) { // Saturday = 6
        $date = strtotime(date('Y-m-d 08:00:00', $date) . ' +1 days');
    }

    // If the start day is Friday, it will be Sunday
    if (((int) date('w', $date)) == 5) { // Friday = 5
        $date = strtotime(date('Y-m-d 08:00:00', $date) . ' +2 days');
    }

    // Adjust the time to within working hours
    $hour = (int) date('G', $date);

    if ($hour >= 16) { // After 4 PM, move to the next working day at 8 AM
        $date = strtotime(date('Y-m-d 08:00:00', $date) . ' +1 days');
    } elseif ($hour < 8) { // Before 8 AM, set to 8 AM of the same day
        $date = strtotime(date('Y-m-d 08:00:00', $date));
    }

    // Ensure the adjusted date is not on a weekend
    if (((int) date('w', $date)) == 6) { // Saturday
        $date = strtotime(date('Y-m-d 08:00:00', $date) . ' +1 days');
    } elseif (((int) date('w', $date)) == 5) { // Friday
        $date = strtotime(date('Y-m-d 08:00:00', $date) . ' +2 days');
    }

    return $date;
}


    public function generate_end_date($start_date, $duration, $OnGoing, $user_id = 0, $action = 'dev')
    {
        //  Remove the weekend  days

        $man_power = 4;
        $man_power_ongoing = 4;
        
        $assign_user = User::find($user_id);
        if($assign_user && $assign_user->defualt_group)
        {
            $group_power = $assign_user->defualt_group->man_power;
            $user_man_power = $assign_user->man_power;
            if($user_man_power)
            {
                $man_power = $user_man_power;
                if($user_man_power == 8)
                {
                    $man_power_ongoing = 1;
                }
                else
                {
                    $man_power_ongoing = 8 - $user_man_power;
                }
                
            }
            else
            {
                $man_power = $group_power;
                if($group_power == 8)
                {
                    $man_power_ongoing = 1;
                }
                else
                {
                    $man_power_ongoing = 8 - $group_power;
                }
                
            }
        }
        
  // Prevent division by zero
    if ($man_power_ongoing == 0) {
        $man_power_ongoing = 1;  // Default to 1 to avoid division by zero
    }
    if ($man_power == 0) {
        $man_power = 1;  // Default to 1 to avoid division by zero
    }
        
        $i = ($action == 'dev') ? ($duration * (int) (($OnGoing) ? (8 / $man_power_ongoing) : (8 / $man_power))) : $duration * 2;
        //$i = ($action == 'dev') ? ($duration * (($OnGoing) ? 8 : 4)) : $duration * 2 ;
        $time = $start_date;
        while ($i != 0) {
            $time = strtotime('+1 hour', $time);
            if (((int) date('w', $time)) < 5 and ((int) date('G', $time)) < 16 and ((int) date('G', $time)) > 8) { // friday = 5 & saturday = 6 and remove after 16:00 and before 08
                --$i;
            }
        }
        $end_date = date('Y-m-d H:i:s', $time);

        return $end_date;
    }

     public function StoreLog($id, $request, $type = 'create')
    { 
        $change_request = $this->changeRequest_old;
        //dd($request->all());
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
            $log_text = 'Issue opened by ' . \Auth::user()->user_name;
            $data = [
                'cr_id' => $id,
                'user_id' => \Auth::user()->id,
                'log_text' => $log_text,
            ];
            $log->create($data);
        } else {
            //$change_request = Change_request::find($id);
              
            if(isset($request->analysis_feedback) && ($change_request->analysis_feedback !=  $request->analysis_feedback) )
            {
                $log_text = " Analysis FeedBack  ".  " \"  $request->analysis_feedback \" " . " By " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if(isset($request->priority_id)  && ( $change_request->priority_id !=  $request->priority_id) )
            {  
                $priority = Priority::find($request->priority_id)->name;
                $log_text = "Priority Changed To ".  " \"  $priority \" " . " By " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if(isset($request->technical_feedback)  && ($change_request->technical_feedback !=  $request->technical_feedback) )
            {  
                $log_text = "Technical Feedback Is ".  " \"  $request->technical_feedback \" " . " By " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if(isset($request->unit_id))
            {  
                $unit_name = Unit::find($request->unit_id)->name;
                 
                $log_text = "CR Assigned To Unit  ".  " \"  $unit_name \" " . " By " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if(isset($request->creator_mobile_number))
            {  
                $log_text = "Creator Mobile Changed To  ".  " \"  $request->creator_mobile_number \" " . " By " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if(isset($request->title)  && ($change_request->title !=  $request->title) )
            {  
                $log_text = "Subject Changed To  ".  " \"  $request->title \" " . " By " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if(isset($request->application_id)   && ($change_request->application_id !=  $request->application_id) ) 
            {  
                $application_name = Application::find($request->application_id)->name;
                $log_text = "Title Changed To  ".  " \"  $application_name \" " . " By " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if(isset($request->description) && ($change_request->description != $request->description ))
            {  
                $log_text = "CR Description To  ".  " \"  $request->description \" " . " By " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if(isset($request->category_id)  && ($change_request->category_id !=   $request->category_id ) )
            {  
                $catigory_name = Category::find($request->category_id)->name;
                $log_text = "CR Category Changed To  ".  " \" $catigory_name  \" " . " By " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if(isset($request->postpone)  && ($change_request->postpone !=   $request->postpone ) )
            {  
                if($request->postpone == 1)
                {
                    $log_text = "CR PostPone changed To  Active BY " . \Auth::user()->user_name;
                }
                else
                {
                    $log_text = "CR PostPone changed To  InActive BY " . \Auth::user()->user_name;
                }
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if(isset($request->need_ux_ui)  && ($change_request->need_ux_ui !=   $request->need_ux_ui ) )
            {  
                
                if($request->need_ux_ui == 1)
                {
                    $log_text = "CR Need UI UX changed To  Active BY " . \Auth::user()->user_name;
                }
                else
                {
                    $log_text = "CR Need UI UX changed To  InActive BY " . \Auth::user()->user_name;
                }
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }
            if(isset($request->division_manager_id)  &&  ($change_request->division_manager_id !=   $request->division_manager_id ) )
            {  
                $divisionManagers_name = DivisionManagers::find($request->division_manager_id)->name;
                $log_text = "Division Managers To  ".  " \" $divisionManagers_name  \" " . " By " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }
            
            if(isset($request->new_status_id))
            {
                $log_text = "Issue manually set to status '$status_title' by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }
            

            if (isset($request->assign_to)) {
                $assigned_user = User::find($request->assign_to);
                $log_text = "Issue assigned  manually to  '$assigned_user->user_name'  by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }


            if (isset($request->developer_id)) {
                $assigned_user = User::find($request->developer_id);
                $log_text = "Issue Assigned  Manually to  '$assigned_user->user_name'  by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if (isset($request->tester_id)) {
                $assigned_user = User::find($request->tester_id);
                $log_text = "Issue Assigned  Manually to  '$assigned_user->user_name'  by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }
             
            if (isset($request->designer_id)) {
                $assigned_user = User::find($request->designer_id);
                $log_text = "Issue Assigned  Manually to  '$assigned_user->user_name'  by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if(isset($request['develop_duration']) && empty($request->developer_id))
            {
                $log_text = "Issue Dev Estimated  by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if(isset($request['design_duration']) && empty($request->developer_id))
            {
                $log_text = "Issue Design Estimated  by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if(isset($request['test_duration']) && empty($request->tester_id))
            {
                $log_text = "Issue Testing Estimated  by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }

            if (isset($request->design_duration)) {
                $log_text = "Issue design duration manually set to  '$request->design_duration H' by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);

                $log_text = "Issue start design time set to  '$request->start_design_time' and end design time set to  '$request->end_design_time' by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }
            if (isset($request->test_duration)) {
                $log_text = "Issue design duration manually set to  '$request->test_duration H' by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);

                $log_text = "Issue start test time set to  '$request->start_test_time' and end test time set to  '$request->end_test_time' by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
            }
            if (isset($request->develop_duration)) {
                $log_text = "Issue design duration manually set to  '$request->develop_duration H' by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
                    'log_text' => $log_text,
                ];
                $log->create($data);
                $log_text = "Issue start develop time set to  '$request->start_develop_time' and end develop time set to  '$request->end_develop_time' by " . \Auth::user()->user_name;
                $data = [
                    'cr_id' => $id,
                    'user_id' => \Auth::user()->id,
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
		$changeRequest = Change_request::with('Release')->where('id', $id)->first();
        /* if ($user_flage == '0') {
            $changeRequest = Change_request::with("Release")->where('id', $id)->where('requester_id', auth::user()->id)->first();
        } else {
            $changeRequest = Change_request::with('Release')->where('id', $id)->first();
        }
		dd($changeRequest); */
        return $changeRequest;
    }

    public function my_assignments_crs()
    {
        $user_id = Auth::user()->id;
        //$group = request()->header('group');
        if(session('default_group')){
            $group = session('default_group');

        }else {
            $group = auth()->user()->default_group;
        }
        $view_statuses = $this->getViewStatuses();

       
        
        $crs = Change_request::with('Req_status.status')->whereHas('Req_status', function ($query) use ($user_id, $group, $view_statuses) {
            $query->where('assignment_user_id', $user_id)
                #->ORwhere('active', '2')
                ->whereIn('new_status_id', $view_statuses);

        })->get();
        //dd($view_statuses);
        return $crs;
    }

    public function my_crs()
    {
        $user_id = Auth::user()->id;
        $crs = Change_request::where('requester_id', $user_id)->get();

        return $crs;
    }

    public function AdvancedSearchResult($getall=0)
    {
        $request_query = request()->except('_token','page');
         
        $CRs = new change_request();
        
        foreach ($request_query as $key => $field_value) {
            if (!empty($field_value)) {
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
    
        \DB::enableQueryLog();
        if ($getall == 0) {
            $results = $CRs->paginate(10);
        } else {
            $results = $CRs->get();
        }
      
        $queries = \DB::getQueryLog();
        $lastQuery = end($queries);
    
        \Log::info('Last Query: ', $lastQuery);
        
        return $results;
    }

    public function get_change_request_by_release($release_id){
        return $changeRequests = Change_request::with("CurrentRequestStatuses")->where('release_name', $release_id)->where("workflow_type_id", 5)->get();
    }

    public function UpateChangeRequestReleaseStatus($id, $request)
    {
        //dd($id,$request);
/** check estimation user without changing in status */
        if (!isset($request->new_status) && isset($request->assignment_user_id)) {
            Change_request_statuse::where('cr_id', $id)->where('new_status_id', $request->old_status_id)->where('active', '1')->update(['assignment_user_id' => $request->assignment_user_id]);
        }
/**end  check estimation  */
        if (isset($request['new_status_id'])) {
           
            $new_status_id = $request['new_status_id'];  
        } elseif (isset($request->new_status_id)) {
           
            $new_status_id = $request->new_status_id;  
        }
        
        if (isset($request['old_status_id'])) {
           
            $old_status_id = $request['old_status_id'];  
        } elseif (isset($request->old_status_id) ) {
           
            $old_status_id = $request->old_status_id;  
        }
        $workflow = NewWorkFlow::where('from_status_id',$old_status_id)->where('type_id',5)->first();
        if(isset(\Auth::user()->id) && \Auth::user()->id != null)
        {
            $user_id = \Auth::user()->id   ;    
        }else{
            $user_id = $request['assign_to'] ;
        }
         




        if ($workflow) {
            $workflow_active = $workflow->workflow_type == 1 ? '0' : '2';
   
            $cr_status = Change_request_statuse::where('cr_id', $id)->where('new_status_id',  $old_status_id)->where('active', '1')->first();
            //dd($id, $request,$cr_status);
            $date = Carbon::parse($cr_status->created_at);
            $now = Carbon::now();
            $diff = $date->diffInDays($now);

            $cr_status->sla_dif = $diff;
            $cr_status->active = $workflow_active;
            $cr_status->save();
            $depend_statuses = Change_request_statuse::where('cr_id', $id)->where('old_status_id', $cr_status->old_status_id)->where('active', '1')->get();
            $active = '1';
            
            if ($workflow_active) { // check if it is normal work flow
                $check_depend_workflow = NewWorkFlow::whereHas('workflowstatus', function ($q) use ($workflow) {
                    $q->where('to_status_id', $workflow->workflowstatus[0]->to_status_id);
                })->pluck('from_status_id');
                $active = $depend_statuses->count() > 0 ? '0' : '1';
                $check_depend_status = Change_request_statuse::where('cr_id', $id)->whereIN('new_status_id', $check_depend_workflow)->where('active', '1')->count();
                if ($check_depend_status > 0) {
                    $active = '0';
                }
            } else { // check if it is abnormal work flow
                foreach ($depend_statuses as $item) {
                    Change_request_statuse::where('id', $item->id)->update(['active' => '0']);
                }
            }

            $change_request_status = new ChangeRequestStatusRepository();

            foreach ($workflow->workflowstatus as $key => $item) {
                $workflow_check_active = 0;

                if ($workflow->workflow_type != 1) {
                    $workflow_check_active = Change_request_statuse::where('cr_id', $id)->where('new_status_id', $item->to_status_id)->where('active', '2')->first();
                }
                if (!$workflow_check_active) {
                    $status_sla = Status::find($item->to_status_id);
                    if($status_sla)
                    {
                        $status_sla = $status_sla->sla;
                    }
                    else
                    {
                        $status_sla = 0;
                    }    
                    $data = [
                        'cr_id' => $id,
                        'old_status_id' => $request['old_status_id'],
                        'new_status_id' => $item->to_status_id,
                        'user_id' => $user_id,
                        'sla' => $status_sla,
                        'active' => $active,
                      //  'assignment_user_id' => $request->assignment_user_id,
                    ];
                    $change_request_status->create($data);
                }
            }
        }

        return true;
    }


    public function InsertOrUpdateChangeRequestCustomField(array $data)
    {
        ChangeRequestCustomField::updateOrCreate(
            ['cr_id' => $data['cr_id'], 'custom_field_id' => $data['custom_field_id'],'custom_field_name' => $data['custom_field_name']],
            ['custom_field_value' => $data['custom_field_value']]
        );
        return true;
    }


    public function calculateEstimation($id,$change_request,$request,$user)
    {
            if ($change_request->workflow_type_id == 4) {
                $request['testing_estimation'] = 1;
            }
            $test_duration = $change_request->test_duration;
            $design_duration = $change_request->design_duration;
            $develop_duration = $change_request->develop_duration;
            $return_data = array(); 
            if(isset($request['dev_estimation'])) // calc dev estimation
            {
				if(isset($design_duration))
                {
                    $return_data['develop_duration'] = $request['dev_estimation'];
               
                    if(isset($request['developer_id'])&&!empty($request['developer_id'])){
                        $return_data['developer_id']=$request['developer_id'];
                    }
                    else {
                        $return_data['developer_id'] = $user->id;
                    }
    
                    $dates = $this->GetLastEndDate($id,   $request['developer_id'], 'developer_id',  $change_request['end_design_time'],   $request['dev_estimation'] , 'dev');
                    $return_data['start_develop_time'] = isset($dates[0]) ? $dates[0] : '';
                    $return_data['end_develop_time'] = isset($dates[1]) ? $dates[1] : '';
                    if(!empty($test_duration))
                    {
                        $dates = $this->GetLastEndDate($id,   $change_request['tester_id'], 'tester_id',   $return_data['end_develop_time'],   $change_request['test_duration'] , 'test');
                        $return_data['start_test_time'] = isset($dates[0]) ? $dates[0] : '';
                        $return_data['end_test_time'] = isset($dates[1]) ? $dates[1] : '';
                    }
                }
                else
                {
                    $return_data['develop_duration'] = $request['dev_estimation'];
                    if(isset($request['developer_id'])&&!empty($request['developer_id'])){
                        $return_data['developer_id']=$request['developer_id'];
                    }
                    else {
                        $return_data['developer_id'] = $user->id;
                    }
                }
                
            }


            if(isset($request['testing_estimation'])) // calc test estimation
            {

				if(isset($design_duration))
                {
                    $return_data['test_duration'] = $request['testing_estimation'];
               
                    if(isset($request['tester_id'])&&!empty($request['tester_id'])){
                        $return_data['tester_id']=$request['tester_id'];
                    }
                    else {
                        $return_data['tester_id'] = $user->id;
                    }
    
                    if(!empty($develop_duration))
                    {
                        $dates = $this->GetLastEndDate($id,   $change_request['developer_id'], 'developer_id',  $change_request['end_design_time'],   $change_request['develop_duration'] , 'dev');
                        $return_data['start_develop_time'] = isset($dates[0]) ? $dates[0] : '';
                        $return_data['end_develop_time'] = isset($dates[1]) ? $dates[1] : '';

                        $dates = $this->GetLastEndDate($id,   $request['tester_id'] , 'tester_id',  $change_request['end_develop_time'],  $request['testing_estimation'] , 'dev');
                        $request['start_test_time'] = isset($dates[0]) ? $dates[0] : '';
                        $request['end_test_time'] = isset($dates[1]) ? $dates[1] : '';
                    }
                }
                else
                {
                    $return_data['test_duration'] = $request['testing_estimation'];
                    if(isset($request['tester_id'])&&!empty($request['tester_id'])){
                        $return_data['tester_id']=$request['tester_id'];
                    }
                    else {
                        $return_data['tester_id'] = $user->id;
                    }
                }
                
            }



            if(isset($request['design_estimation'])) // calc design estimation
            {

                $return_data['design_duration'] = $request['design_estimation'];
               
                if(isset($request['designer_id'])&&!empty($request['designer_id']))
                {
                    $return_data['designer_id']=$request['designer_id'];
                }
                else 
                {
                    $return_data['designer_id'] = $user->id;
                }
                $dates = $this->GetLastCRDate($id, $user->id, 'designer_id', 'end_design_time', $request['design_estimation'], 'design');
                $return_data['start_design_time'] = isset($dates[0]) ? $dates[0] : '';
                $return_data['end_design_time'] = isset($dates[1]) ? $dates[1] : '';

                if(!empty($develop_duration))
                {
                    $dates = $this->GetLastEndDate($id,  $change_request['developer_id'], 'developer_id',  $request['end_design_time'],  $change_request['develop_duration'] , 'dev');
                    $return_data['start_develop_time'] = isset($dates[0]) ? $dates[0] : '';
                    $return_data['end_develop_time'] = isset($dates[1]) ? $dates[1] : '';
                }

                if(!empty($test_duration))
                {
                    $dates = $this->GetLastEndDate($id,  $change_request['tester_id'], 'tester_id',  $return_data['end_develop_time'],  $change_request['test_duration'] , 'test');
                    $return_data['start_test_time'] = isset($dates[0]) ? $dates[0] : '';
                    $return_data['end_test_time'] = isset($dates[1]) ? $dates[1] : '';
                }
                
            }

           
             
            return $return_data; 
        
    }



    public function CountCrsPerSystem($workflow_type)
    {
        $collection = Change_request::groupBy('application_id')->selectRaw('count(*) as total, application_id')->where('workflow_type_id',$workflow_type)->get();
        return $collection;
    }

    public function CountCrsPerStatus()
    {
     
        $collection = Change_request_statuse::groupBy('new_status_id')->selectRaw('count(*) as total, new_status_id')->where('active','1')->get();
        
        return $collection;
    }


    public function CountCrsPerSystemAndStatus($workflow_type)
    {

        $collection = Change_request_statuse::
                    whereHas('ChangeRequest', function ($q) use ($workflow_type) {
                        $q->where('workflow_type_id', $workflow_type);
                    })
                    ->groupBy('new_status_id')
                    ->selectRaw('count(*) as total, new_status_id')
                    ->where('active','1')
                    ->get();
        return $collection;

    }



}
