<?php
namespace App\Services\ChangeRequest;

use App\Models\{Change_request, User, Group};
use Carbon\Carbon;
use App\Http\Repository\{
    Logs\LogRepository,
    ChangeRequest\ChangeRequestStatusRepository,
    ChangeRequest\ChangeRequestRepository
};
class ChangeRequestSchedulingService
{
    // public function reorderTimes($crId): array
    // {
    //     try {
    //         $cr = Change_request::find($crId);

    //         if (!$cr) {
    //             return [
    //                 'status' => false,
    //                 'message' => "Change Request with ID {$crId} not found."
    //             ];
    //         }

          
    //         $cr_found_design=Change_request::with('RequestStatuses')
    //         ->where('designer_id', $cr->designer_id)
    //         ->where('id', $crId)
           
    //         ->whereHas('RequestStatuses', function ($query) {
    //           $query->whereIn('new_status_id', [15, 7]);
    //         })
    //         ->first();
    
    //         if ($cr_found_design) {
    //             $this->processDesignPhase($cr);
    //         }
    //         if (!$cr_found_design) {
    //             $this->processDevelopmentPhase($cr);
    //             $this->processTestingPhase($cr);
    //             $this->reorderQueuedRequests($cr, $crId);
    //         }
    //      // die("walid");
            

    //         return [
    //             'status' => true,
    //             'message' => "Successfully reordered times for CR ID {$crId} and related queued CRs."
    //         ];

    //     } catch (\Exception $e) {
    //         return [
    //             'status' => false,
    //             'message' => 'An error occurred while reordering the times: ' . $e->getMessage()
    //         ];
    //     }
    // }
    public function reorderTimes($id): array
    {
        try {

            $crId = Change_request::where('cr_no', $id)->first()->id;
            $cr = Change_request::find($crId);
    
            if (!$cr) {
                return [
                    'status' => false,
                    'message' => "Change Request with ID {$crId} not found."
                ];
            }
    
            $priority = request()->has('priority');
    
            // Check statuses
            $hasDevelopStatus = $cr->RequestStatuses()
                ->whereIn('new_status_id', [10, 8])
                ->exists();
                

// Get the actual records instead of just exists()



                if ($hasDevelopStatus) {
                    
                    $this->processDevelopPhase($cr, $priority);
                    $this->reorderAllTesterQueues();
        
                }
            $hasDesignStatus = $cr->RequestStatuses()
                ->whereIn('new_status_id', [15, 7])
                ->exists();

                $hasTestStatus = $cr->RequestStatuses()
                ->whereIn('new_status_id', [13, 11])
                ->exists();

                if($hasTestStatus){
                    $this->processtestPhase($cr, $priority);

                }
    
           
    
            if ($hasDesignStatus) {
                $this->processDesignPhase($cr);
            }
    
            // After reordering develop phase, reorder ALL tester queues to avoid conflicts
           
            return [
                'status' => true,
                'message' => "Successfully reordered times for CR ID {$crId}."
            ];
        } catch (\Exception $e) {
            
            return [
                'status' => false,
                'message' => 'An error occurred while reordering the times: ' . $e->getMessage()
            ];
        }
    }
    protected function processTestPhase($cr): void
{
    if (!$cr || $cr->test_duration <= 0) {
        throw new \Exception("Invalid CR or no test duration.");
    }

    // Find the last booked CR for this tester
    $activeCr = Change_request::where('tester_id', $cr->tester_id)
        ->where('id', '!=', $cr->id)
        ->orderBy('end_test_time', 'desc')
        ->first();


        $Cr_test_in_progress = Change_request::where('tester_id', $cr->tester_id)
        ->where('id', '=', $cr->id)
        ->whereHas('RequestStatuses', function ($query) {
            $query->where('new_status_id', 74);
        })
       
        ->first();

        if($Cr_test_in_progress){
            throw new \Exception("test phase already in progress for CR ID {$cr->id}.");

        }

    $hasPriority = request()->has('priority');

    if ($activeCr && !$hasPriority && $activeCr->end_test_time) {
        // Tester is busy → start after their last test finishes
        $startTestTime = Carbon::parse($activeCr->end_test_time);
    } else {
        // Tester is free OR priority CR → start now at working hours
        $startTestTime = Carbon::createFromTimestamp(
            $this->setToWorkingDate(Carbon::now()->timestamp)
        );

        // Set current CR status to active testing (13)
      //  $cr->RequestStatuses()->update(['new_status_id' => 13]);

        if ($hasPriority) {

            $repo = new \App\Http\Repository\ChangeRequest\ChangeRequestRepository();

            $req = new \Illuminate\Http\Request([
                'old_status_id' => 11,
                'new_status_id' => 139,
                //propagate sender email for repo user resolution logic
                'assign_to'     => null,
            ]);
    
         
                $repo->UpateChangeRequestStatus($cr->id, $req);
            // Demote other CRs in testing from 13 to 11 (queued)
            Change_request::where('tester_id', $cr->tester_id)
                ->where('id', '!=', $cr->id)
                ->whereHas('RequestStatuses', function ($query) {
                    $query->where('new_status_id', 74);
                })
                ->each(function ($otherCr) {


                    // $otherCr->RequestStatuses()->update(['new_status_id' => 7]);

                    $lastTwoStatuses = $otherCr->AllRequestStatuses()
                    ->orderBy('id', 'desc')
                    ->take(2)
                    ->get();
                   
    
                 

                    if ($lastTwoStatuses->count() == 2) {
                        $latest   = $lastTwoStatuses[0]; // newest
                        $previous = $lastTwoStatuses[1]; // before newest
        //die("ddd");
     
                        // 2️⃣ Make latest inactive
                        $latest->timestamps = false;
                        $latest->update(['active' => '0']);
                     
                        // 3️⃣ Insert a copy of previous with active=1
                   
                  
                        $request=$otherCr->RequestStatuses()->create([
 'old_status_id' => $previous->old_status_id,
 'new_status_id' => $previous->new_status_id,
 'assign_to'     => $previous->assign_to,
 'active'        => '1',
 'user_id'       => $previous->user_id,
 'created_at'    => now(),
 'updated_at'    => now(),
]);

//$this->logRepository->logCreate($otherCr->id, $request, $otherCr, 'create');
                    }
                

                 });
        }
    }

    // Calculate end time for this CR
    $endTestTime = Carbon::parse(
        $this->generateEndDate(
            $startTestTime->timestamp,
            $cr->test_duration,
            false,
            $cr->tester_id,
            'test'
        )
    );

    $cr->update([
        'start_test_time' => $startTestTime->format('Y-m-d H:i:s'),
        'end_test_time'   => $endTestTime->format('Y-m-d H:i:s'),
    ]);

    // Reorder queued CRs (status 11 only)
    $queue = Change_request::where('tester_id', $cr->tester_id)
        ->where('id', '!=', $cr->id)
        ->whereHas('RequestStatuses', function ($query) {
            $query->where('new_status_id', 11); // queued test
        })
        ->orderBy('id')
        ->get();

    foreach ($queue as $queuedCr) {
        if (!empty($queuedCr->test_duration) && $queuedCr->test_duration > 0) {
            // Start after the last CR in the tester's queue
            $startTestTime = Carbon::parse($endTestTime);

            $endTestTime = Carbon::parse(
                $this->generateEndDate(
                    $startTestTime->timestamp,
                    $queuedCr->test_duration,
                    false,
                    $queuedCr->tester_id,
                    'test'
                )
            );

            $queuedCr->update([
                'start_test_time' => $startTestTime->format('Y-m-d H:i:s'),
                'end_test_time'   => $endTestTime->format('Y-m-d H:i:s'),
            ]);
        }
    }
}

    protected function processDevelopPhase($cr, $priority = false): void
    {
        
        if (!$cr || $cr->develop_duration <= 0) {
            throw new \Exception("Invalid CR or no develop duration.");
        }
       
       $Cr_develop_in_progress = Change_request::where('developer_id', $cr->developer_id)
            ->where('id', '=', $cr->id)
            ->whereHas('RequestStatuses', function ($query) {
                $query->where('new_status_id', 10);
            })
           
            ->first();

            if( $Cr_develop_in_progress){
                throw new \Exception("develop phase already in progress for CR ID {$cr->id}.");

            }
        if ($priority) {
            // Make this CR active
          //  $cr->RequestStatuses()->update(['new_status_id' => 10]);

          $repo = new \App\Http\Repository\ChangeRequest\ChangeRequestRepository();

          $req = new \Illuminate\Http\Request([
              'old_status_id' => 8,
              'new_status_id' => 48,
              //propagate sender email for repo user resolution logic
              'assign_to'     => null,
          ]);
  
       
              $repo->UpateChangeRequestStatus($cr->id, $req);
    
            // Demote other active CRs for this developer to queued
            Change_request::where('developer_id', $cr->developer_id)
                ->where('id', '!=', $cr->id)
                ->whereHas('RequestStatuses', fn($q) => $q->where('new_status_id', 10))
                ->each(function ($otherCr) {


                    // $otherCr->RequestStatuses()->update(['new_status_id' => 7]);

                    $lastTwoStatuses = $otherCr->AllRequestStatuses()
                    ->orderBy('id', 'desc')
                    ->take(2)
                    ->get();
                   
    
                 

                    if ($lastTwoStatuses->count() == 2) {
                        $latest   = $lastTwoStatuses[0]; // newest
                        $previous = $lastTwoStatuses[1]; // before newest
        //die("ddd");
     
                        // 2️⃣ Make latest inactive
                        $latest->timestamps = false;
                        $latest->update(['active' => '0']);
                     
                        // 3️⃣ Insert a copy of previous with active=1
                   
                  
$request=$otherCr->RequestStatuses()->create([
 'old_status_id' => $previous->old_status_id,
 'new_status_id' => $previous->new_status_id,
 'assign_to'     => $previous->assign_to,
 'active'        => '1',
 'user_id'       => $previous->user_id,
 'created_at'    => now(),
 'updated_at'    => now(),
]);


                    }
                

                 });
    
            $startTime = Carbon::createFromTimestamp(
                $this->setToWorkingDate(Carbon::now()->timestamp)
            );
        } else {
            // Find last active CR for this developer
            $lastCr = Change_request::where('developer_id', $cr->developer_id)
                ->where('id', '!=', $cr->id)
                ->whereHas('RequestStatuses', function ($q) {
                    $q->where('new_status_id', '=', 10);
                })
                ->orderBy('end_develop_time', 'desc')
                ->first();
    
            if ($lastCr) {
                $startTime = Carbon::parse($lastCr->end_develop_time);


                $repo = new \App\Http\Repository\ChangeRequest\ChangeRequestRepository();

                $req = new \Illuminate\Http\Request([
                    'old_status_id' => 8,
                    'new_status_id' => 48,
                    //propagate sender email for repo user resolution logic
                    'assign_to'     => null,
                ]);
        
             
                    $repo->UpateChangeRequestStatus($cr->id, $req);
               // $cr->RequestStatuses()->update(['new_status_id' => 8]); // queued
            } else {
                $startTime = Carbon::createFromTimestamp(
                    $this->setToWorkingDate(Carbon::now()->timestamp)
                );
              //  $cr->RequestStatuses()->update(['new_status_id' => 10]); // active

              $lastTwoStatuses = $cr->AllRequestStatuses()
              ->orderBy('id', 'desc')
              ->take(2)
              ->get();
             

           

              if ($lastTwoStatuses->count() == 2) {
                  $latest   = $lastTwoStatuses[0]; // newest
                  $previous = $lastTwoStatuses[1]; // before newest
  //die("ddd");

                  // 2️⃣ Make latest inactive
                  $latest->timestamps = false;
                  $latest->update(['active' => '0']);
               
                  // 3️⃣ Insert a copy of previous with active=1
             
            
$cr->RequestStatuses()->create([
'old_status_id' => $previous->old_status_id,
'new_status_id' => $previous->new_status_id,
'assign_to'     => $previous->assign_to,
'active'        => '1',
'user_id'       => $previous->user_id,
'created_at'    => now(),
'updated_at'    => now(),
]);


              }


            }
        }
    
        // Develop end time
        $endTime = Carbon::parse(
            $this->generateEndDate(
                $startTime->timestamp,
                $cr->develop_duration,
                false,
                $cr->developer_id,
                'develop'
            )
        );
    
        // Test start & end time
        $startTestTime = $endTime;
        $endTestTime = Carbon::parse(
            $this->generateEndDate(
                $startTestTime->timestamp,
                $cr->test_duration,
                false,
                $cr->tester_id,
                'test'
            )
        );
    
        // Update CR
        $cr->update([
            'start_develop_time' => $startTime->format('Y-m-d H:i:s'),
            'end_develop_time'   => $endTime->format('Y-m-d H:i:s'),
            'start_test_time'    => $startTestTime->format('Y-m-d H:i:s'),
            'end_test_time'      => $endTestTime->format('Y-m-d H:i:s'),
        ]);
    
        // Reorder queued CRs for this developer
        $this->reorderDeveloperQueueSequentialFromCR($cr);
    }
    
    /**
     * Reorder queued CRs for a developer starting from the given CR's end time
     */
    protected function reorderDeveloperQueueSequentialFromCR($cr): void
    {
        $developerId = $cr->developer_id;
        $startTime = Carbon::parse($cr->end_develop_time);
    
        $queued = Change_request::where('developer_id', $developerId)
            ->where('id', '!=', $cr->id)
            ->whereHas('RequestStatuses', function ($q) {
                $q->where('new_status_id', 8)
                  ->where('new_status_id', '!=', 10);
            })
            ->orderBy('start_develop_time')
            ->get();
    
        foreach ($queued as $qcr) {
            $phaseEnd = Carbon::parse(
                $this->generateEndDate(
                    $startTime->timestamp,
                    $qcr->develop_duration,
                    false,
                    $developerId,
                    'develop'
                )
            );
    
            // Set test start right after develop
            $startTestTime = $phaseEnd;
            $endTestTime = Carbon::parse(
                $this->generateEndDate(
                    $startTestTime->timestamp,
                    $qcr->test_duration,
                    false,
                    $qcr->tester_id,
                    'test'
                )
            );
    
            $qcr->update([
                'start_develop_time' => $startTime->format('Y-m-d H:i:s'),
                'end_develop_time'   => $phaseEnd->format('Y-m-d H:i:s'),
                'start_test_time'    => $startTestTime->format('Y-m-d H:i:s'),
                'end_test_time'      => $endTestTime->format('Y-m-d H:i:s'),
            ]);
    
            $startTime = $phaseEnd;
        }
    }
    protected function reorderAllTesterQueues(): void
{
    $testerIds = Change_request::whereNotNull('tester_id')
        ->distinct()
        ->pluck('tester_id');

    foreach ($testerIds as $testerId) {
        $this->reorderSingleTesterQueue($testerId);
    }
}

/**
 * Reorder a single tester's queue sequentially
 */
protected function reorderSingleTesterQueue(int $testerId): void
{
    $crList = Change_request::where('tester_id', $testerId)
        ->orderBy('start_test_time')
        ->get();

    $startTime = null;

    foreach ($crList as $cr) {
        // Start after develop ends if first in queue
        if (!$startTime) {
            $startTime = Carbon::parse($cr->end_develop_time);
        } else {
            // Avoid overlap
            if (Carbon::parse($cr->start_test_time)->lt($startTime)) {
                $cr->start_test_time = $startTime->format('Y-m-d H:i:s');
            }
        }

        // End time
        $endTime = Carbon::parse(
            $this->generateEndDate(
                Carbon::parse($cr->start_test_time)->timestamp,
                $cr->test_duration,
                false,
                $testerId,
                'test'
            )
        );

        // Update CR
        $cr->update([
            'start_test_time' => $startTime->format('Y-m-d H:i:s'),
            'end_test_time'   => $endTime->format('Y-m-d H:i:s'),
        ]);

        // Next CR starts after this CR finishes
        $startTime = $endTime;
    }
}
    protected function processDesignPhase($cr): void
    {
        if (!$cr || $cr->design_duration <= 0) {
            throw new \Exception("Invalid CR or no design duration.");
        }
    



        // Find if there is already an active CR (status 15) for this designer
        $activeCr = Change_request::where('designer_id', $cr->designer_id)
            ->where('id', '!=', $cr->id)
            ->whereHas('RequestStatuses', function ($query) {
                $query->where('new_status_id', 15);
            })
            ->orderBy('end_design_time', 'desc')
            ->first();

            $Cr_design_in_progress = Change_request::where('designer_id', $cr->designer_id)
            ->where('id', '=', $cr->id)
            ->whereHas('RequestStatuses', function ($query) {
                $query->where('new_status_id', 15);
            })
           
            ->first();

            if( $Cr_design_in_progress){
                throw new \Exception("Design phase already in progress for CR ID {$cr->id}.");

            }
    
        $hasPriority = request()->has('priority');
    
        if ($activeCr && !$hasPriority) {
            // Normal CR → start after current active CR finishes
            $startDesignTime = Carbon::parse($activeCr->end_design_time);
        } else {
            // Has priority OR no active CR → start now
            $startDesignTime = Carbon::createFromTimestamp(
                $this->setToWorkingDate(Carbon::now()->timestamp)
            );
    
  $repo = new \App\Http\Repository\ChangeRequest\ChangeRequestRepository();

        $req = new \Illuminate\Http\Request([
            'old_status_id' => 7,
            'new_status_id' => 46,
            //propagate sender email for repo user resolution logic
            'assign_to'     => null,
        ]);

     
            $repo->UpateChangeRequestStatus($cr->id, $req);
          


            // Set current CR to status 15
           // $cr->RequestStatuses()->update(['new_status_id' => 15]);
    
            if ($hasPriority) {
                // Demote other CRs from 15 → 7
                Change_request::where('designer_id', $cr->designer_id)
                    ->where('id', '!=', $cr->id)
                    ->whereHas('RequestStatuses', function ($query) {
                        $query->where('new_status_id', 15);
                    })
                    ->each(function ($otherCr) {


                       // $otherCr->RequestStatuses()->update(['new_status_id' => 7]);

                       $lastTwoStatuses = $otherCr->AllRequestStatuses()
                       ->orderBy('id', 'desc')
                       ->take(2)
                       ->get();
                      
       
                    

                       if ($lastTwoStatuses->count() == 2) {
                           $latest   = $lastTwoStatuses[0]; // newest
                           $previous = $lastTwoStatuses[1]; // before newest
           //die("ddd");
        
                           // 2️⃣ Make latest inactive
                           $latest->timestamps = false;
                           $latest->update(['active' => '0']);
                        
                           // 3️⃣ Insert a copy of previous with active=1
                      
                     
$otherCr->RequestStatuses()->create([
    'old_status_id' => $previous->old_status_id,
    'new_status_id' => $previous->new_status_id,
    'assign_to'     => $previous->assign_to,
    'active'        => '1',
    'user_id'       => $previous->user_id,
    'created_at'    => now(),
    'updated_at'    => now(),
]);


                       }
                   

                    });
                   // die("walid");
            }
        }
    
        // Calculate end time for this CR
        $endDesignTime = Carbon::parse(
            $this->generateEndDate(
                $startDesignTime->timestamp,
                $cr->design_duration,
                false,
                $cr->designer_id,
                'design'
            )
        );
    
        $cr->update([
            'start_design_time' => $startDesignTime->format('Y-m-d H:i:s'),
            'end_design_time'   => $endDesignTime->format('Y-m-d H:i:s'),
        ]);
    
        // Reorder queued CRs (status 7 only)
        $queue = Change_request::where('designer_id', $cr->designer_id)
            ->where('id', '!=', $cr->id)
            ->whereHas('RequestStatuses', function ($query) {
                $query->where('new_status_id', 7);
            })
            ->orderBy('start_design_time')
            ->get();
    
        foreach ($queue as $queuedCr) {
            if (!empty($queuedCr->design_duration) && $queuedCr->design_duration > 0) {
                $startDesignTime = Carbon::parse($endDesignTime);
    
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
                    'end_design_time'   => $endDesignTime->format('Y-m-d H:i:s'),
                ]);
            }
        }
    }
    
    public function reorderChangeRequests($id)
    {
      

        $crId = Change_request::where('cr_no', $id)->first()->id;

    
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

    // protected function processDesignPhase($cr): void
    // {
    //     if ($cr->design_duration <= 0) {
    //         return;
    //     }

    //     if (!$cr) {
    //         return;
    //         // return [
    //         //     'status' => 'error',
    //         //     'message' => "Change Request with ID {$crId} not found."
    //         // ];
    //     }
   
    //         if (!request()->has('priority')) {

    //             $q = Change_request::with('RequestStatuses')
    //             ->where('designer_id', $cr->designer_id)
    //             ->where('id', $crId)
               
    //             ->whereHas('RequestStatuses', function ($query) {
    //                 $query->where('new_status_id', 15); // Only those with status = 15
    //             })
    //             ->first();
            
    //            // dd($q);
               
    // if($q){
    //    return ;
 
    //     // return [
    //     //     'status' => 'error',
    //     //     'message' => "Change Request with ID  is aleady working on it."
    //     // ];
    // }
    //         } else {

             
    //                 $cr->RequestStatuses()->update(['new_status_id' => 15]);
                   
                
    //         }
         
 


    //         // Fetch all CRs of the same designer, ordered by end time
    //         $designerCrs = Change_request::with('RequestStatuses')
    //             ->where('designer_id', $cr->designer_id)
    //             ->where('id', '!=', $cr->id)
    //             ->get();
    //             $startDesignTime = Carbon::createFromTimestamp($this->setToWorkingDate(Carbon::now()->timestamp));
    //             if (!request()->has('priority')) {
    //             // Find the latest CR with status 15
    //         $crWithStatus15 = $designerCrs
    //             ->filter(function ($item) {
    //                 return $item->RequestStatuses->contains(function ($status) {
    //                     return $status->new_status_id == 15;
    //                 });
    //             })
    //             ->sortByDesc('end_design_time')
    //             ->first();
    
    //         // Default start time is now
          
    
    //         if ($crWithStatus15) {
    //             $startDesignTime = Carbon::parse($crWithStatus15->end_design_time);
    //         }
    //     }
    //         // Calculate end time for current CR
    //         $endDesignTime = Carbon::parse(
    //             $this->generateEndDate(
    //                 $startDesignTime->timestamp,
    //                 $cr->design_duration,
    //                 false,
    //                 $cr->designer_id,
    //                 'design'
    //             )
    //         );
    
    //         // Update current CR
    //         $cr->update([
    //             'start_design_time' => $startDesignTime->format('Y-m-d H:i:s'),
    //             'end_design_time'   => $endDesignTime->format('Y-m-d H:i:s'),
    //         ]);
    //         if (!request()->has('priority')) {
    //         // Now reorder remaining CRs (excluding the current one)
    //         $queue = Change_request::where('designer_id', $cr->designer_id)
    //         ->where('id', '!=', $cr->id)
    //         // ->whereDoesntHave('RequestStatuses', function ($query) {
    //         //     $query->where('new_status_id', 15); // Exclude CRs with status = 15
    //         // })
    //         ->whereHas('RequestStatuses', function ($query) {
    //             $query->where('new_status_id', 7); // Exclude CRs with status = 15
    //         })
    //         ->orderBy('id')
    //         ->get();
    //         } else{
    //             $queue = Change_request::where('designer_id', $cr->designer_id)
    //             ->where('id', '!=', $cr->id)
    //             ->whereHas('RequestStatuses', function ($query) {
    //                 $query->whereIn('new_status_id', [15, 7]);
    //             })
    //             ->orderBy('id')
    //             ->get();
            

    //         }
    
    //         foreach ($queue as $queuedCr) {
    //             if (!empty($queuedCr->design_duration) && $queuedCr->design_duration > 0) {
    //                 if (request()->has('priority')) {
    //                 $queuedCr->RequestStatuses()->where('new_status_id', 15)->update(['new_status_id' => 7]);
    //                 }
    //                 $startDesignTime = Carbon::parse($endDesignTime);
    
    //                 $endDesignTime = Carbon::parse(
    //                     $this->generateEndDate(
    //                         $startDesignTime->timestamp,
    //                         $queuedCr->design_duration,
    //                         false,
    //                         $queuedCr->designer_id,
    //                         'design'
    //                     )
    //                 );
    
    //                 $queuedCr->update([
    //                     'start_design_time' => $startDesignTime->format('Y-m-d H:i:s'),
    //                     'end_design_time'   => $endDesignTime->format('Y-m-d H:i:s'),
    //                 ]);
    //             }
    //         }
    
           
        

         
    //     // $currentTime = Carbon::now()->timestamp;
        
    //     // if (isset($cr->start_design_time) && Carbon::parse($cr->start_design_time)->isFuture()) {
    //     //     $startDesignTime = Carbon::createFromTimestamp($this->setToWorkingDate($currentTime));
    //     //     $endDesignTime = Carbon::parse(
    //     //         $this->generateEndDate(
    //     //             $startDesignTime->timestamp,
    //     //             $cr->design_duration,
    //     //             false,
    //     //             $cr->designer_id,
    //     //             'design'
    //     //         )
    //     //     );

    //     //     $conflictingCR = $this->isDesignerBusy(
    //     //         $cr->designer_id, 
    //     //         $startDesignTime, 
    //     //         $cr->design_duration, 
    //     //         $endDesignTime, 
    //     //         $cr->id
    //     //     );

    //     //     if ($conflictingCR) {
    //     //         $startDesignTime = $this->resolveDesignConflict($conflictingCR, $cr);
    //     //         $endDesignTime = Carbon::parse(
    //     //             $this->generateEndDate(
    //     //                 $startDesignTime->timestamp,
    //     //                 $cr->design_duration,
    //     //                 false,
    //     //                 $cr->designer_id,
    //     //                 'design'
    //     //             )
    //     //         );
    //     //     }

    //     //     $cr->update([
    //     //         'start_design_time' => $startDesignTime->format('Y-m-d H:i:s'),
    //     //         'end_design_time' => $endDesignTime->format('Y-m-d H:i:s'),
    //     //     ]);
    //     // }


     
    // }

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
            $query->Where('developer_id', $cr->developer_id)
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
        // // Design Phase
        // if ($queuedCr->designer_id == $cr->designer_id && 
        //     !empty($queuedCr->design_duration) && 
        //     $queuedCr->design_duration > 0) {
            
        //     $startDesignTime = Carbon::parse($cr->end_design_time);
        //     $endDesignTime = Carbon::parse(
        //         $this->generateEndDate(
        //             $startDesignTime->timestamp,
        //             $queuedCr->design_duration,
        //             false,
        //             $queuedCr->designer_id,
        //             'design'
        //         )
        //     );

        //     $queuedCr->update([
        //         'start_design_time' => $startDesignTime->format('Y-m-d H:i:s'),
        //         'end_design_time' => $endDesignTime->format('Y-m-d H:i:s'),
        //     ]);
        // }

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

    protected function adjustTimesForChangeRequest($id, $changeRequest): void
    {

        $crId = Change_request::where('cr_no', $id)->first()->id;
        $cr = Change_request::find($crId);
        $cr_found_design=Change_request::with('RequestStatuses')
        ->where('designer_id', $cr->designer_id)
        ->where('id', $crId)
       
        ->whereHas('RequestStatuses', function ($query) {
          $query->whereIn('new_status_id', [15, 7]);
        })
        ->first();

        if ($cr_found_design) {
            $this->processDesignPhase($changeRequest);
        }
        // Implementation for adjusting times for a specific change request
        // This would include the complex logic from the original method
        if (!$cr_found_design) {
            $this->processDevelopmentPhase($changeRequest);
        $this->processTestingPhase($changeRequest);
        }
      
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