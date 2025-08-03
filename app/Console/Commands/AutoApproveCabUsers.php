<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoApproveCabUsers extends Command
{
    protected $signature = 'cab:approve-users';

    protected $description = 'Automatically approve cab_cr_users after 2 days if not approved';

    public function handle()
    {
        $thresholdDate = Carbon::now()->subDays(2);
         
        $updated = DB::table('cab_cr_users')
            ->where('status', '0')
            ->where('created_at', '<=', $thresholdDate)
            ->update(['status' => '2']);

        $this->info("Auto-approved $updated user(s).");
    }
}
