<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;
use App\Models\Configuration;

class Reject_business_approvals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example: php artisan auto:Reject-cr
     */
    protected $signature = 'auto:reject-cr';

    /**
     * The console command description.
     */
    protected $description = 'Automatically Reject CRs greater than 7 days with new_status_id = 22 and active = 1';

    /**
     * Execute the console command.
     */
    public function handle(ChangeRequestRepository $repo)
    {
        Log::info('=== Auto Reject CR job started ===');
       $configuration = Configuration::where('configuration_name', 'Division Manager Approval')->first();
        $configurationValue = (int) ($configuration->configuration_value ?? 0);
        $sevenDaysAgo = Carbon::now()->subDays($configurationValue);

        // Fetch eligible records
        $records = DB::table('change_request_statuses') 
            ->where('new_status_id', 22)
            ->where('active', 1)
            ->where('created_at', '>', $sevenDaysAgo)
            ->get();

        $approvedCount = 0;

        foreach ($records as $record) {
            $crId = $record->cr_id;
            $user_id = $record->user_id;

            $requestData = new \Illuminate\Http\Request([
                'old_status_id' => '22',
                'new_status_id' => '19',
                'cab_cr_flag' => '1',
                'user_id' => $user_id,
            ]);

            try {
                Log::info("Auto-Reject user: {$user_id}, CR ID: {$crId}");
                $repo->update($crId, $requestData);
                $approvedCount++;
            } catch (\Exception $e) {
                Log::error("Failed to update CR ID {$crId}: " . $e->getMessage());
            }
        }

        Log::info("Auto Rejetion CR job finished. Total Rejected: {$approvedCount}");
        $this->info("Auto Rejetion CR job finished. Total Rejected: {$approvedCount}");
    }
}
