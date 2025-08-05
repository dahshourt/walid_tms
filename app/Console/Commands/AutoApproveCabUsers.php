<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\ChangeRequest\changeRequestController;

class AutoApproveCabUsers extends Command
{
    protected $signature = 'cab:approve-users';

    protected $description = 'Automatically approve cab_cr_users after 2 days if not approved';

    public function handle()
    {
        $thresholdDate = Carbon::now()->subDays(2);

        // Get all users that meet the condition
        $users = DB::table('cab_cr_users')
            ->where('status', '0')
            ->where('created_at', '<=', $thresholdDate)
            ->get();

        $controller = App::make(changeRequestController::class);

        $approvedCount = 0;

        foreach ($users as $user) {
            // You can customize this request data as needed
            $requestData = [
                'user_id' => $user->user_id,
                'status' => "Approved",
                // Add more fields if needed
            ];

            $request = new Request($requestData);

            try {
                // Call the controller's update function
                $controller->update($request, $user->cab_cr_id);
                $approvedCount++;
            } catch (\Exception $e) {
                Log::error("Failed to auto-approve user ID {$user->id}: " . $e->getMessage());
            }
        }

        $this->info("Auto-approved $approvedCount user(s).");
    }
}
