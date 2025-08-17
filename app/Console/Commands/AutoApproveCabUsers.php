<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;

class AutoApproveCabUsers extends Command
{
    protected $signature = 'cab:approve-users';
    protected $description = 'Automatically approve cab_cr_users after 2 days if not approved';

    public function handle()
    {
        $daysToSubtract = 2;
		$thresholdDate = Carbon::now();

		while ($daysToSubtract > 0) {
			$thresholdDate->subDay();

			// Skip Friday (5) and Saturday (6) according to ISO-8601 (Mon=1, Sun=7)
			if (!in_array($thresholdDate->dayOfWeekIso, [5, 6])) {
				$daysToSubtract--;
			}
		}

        // Get users who are not approved and older than 2 days
       $users = DB::table('cab_cr_users')
            ->join('cab_crs', 'cab_crs.id', '=', 'cab_cr_users.cab_cr_id')
            ->where('cab_cr_users.status', '0')
             ->where('cab_crs.status', '0')
            ->where('cab_cr_users.created_at', '<=', $thresholdDate)
            ->get();

        $repo = new ChangeRequestRepository();
        $approvedCount = 0;

        foreach ($users as $user) {
            $crId = $user->cr_id;
            
            $user_id = $user->user_id;
             
           /*  $requestData = [
                'old_status_id' => '38',
                'new_status_id' => '160',
                'cab_cr_flag' => '1',
                'user_id' => $user_id,
            ];
             dd((object)$requestData);
			  */
			$requestData = new \Illuminate\Http\Request([
				'old_status_id' => '38',
                'new_status_id' => '160',
                'cab_cr_flag' => '1',
                'user_id' => $user_id,
			]); 
            try {
                
                $repo->update($crId, $requestData);
                $approvedCount++;
            } catch (\Exception $e) {
                Log::error("Failed to update CR ID {$crId}: " . $e->getMessage());
            }
        }

        $this->info("Auto-approved $approvedCount user(s).");
    }
}
