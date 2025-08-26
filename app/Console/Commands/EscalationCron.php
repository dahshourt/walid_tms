<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EscalationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:escalation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SLA violations and send escalation emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Step 1: Fetch SLA rules
        $slaRecords = DB::table('sla_calculations')->get();

        foreach ($slaRecords as $sla) {
            // Calculate SLA deadline
            $deadline = $sla->type === 'day'
                ? Carbon::parse($sla->created_at)->addDays($sla->sla_time)
                : Carbon::parse($sla->created_at)->addHours($sla->sla_time);
            
            // Step 2: Find change_request_statuses that exceeded SLA
            $violations = DB::table('change_request_statuses')
                ->where('new_status_id', $sla->status_id)
                ->where('active', '1')
                ->where('created_at', '<=', $deadline)
                ->get();
                 
            foreach ($violations as $violation) {
                // Step 3: Join with groups + managers
                $group = DB::table('groups')
                    ->where('id', $sla->group_id)
                    ->first();
            // print_r($group);
                if ($group) {
                    $director = DB::table('directors')->where('id', $group->director_id)->first();
                    $divisionManager = DB::table('division_managers')->where('id', $group->division_manager_id)->first();
                    $unit = DB::table('units')->where('id', $group->unit_id)->first();
                    $director = $director->email; 
                    $divisionManager = $divisionManager->division_manager_email;
                    $unit = $unit->manager_name;
                     // Step 4: Send escalation email
                    $this->sendEscalationMail($violation, $group, $director, $divisionManager, $unit);
                }
            }
        }
       
        $this->info('Escalation cron job executed successfully.');
    }

    private function sendEscalationMail($violation, $group, $director, $divisionManager, $unit)
    {
        // 👇 Replace with your built-in mail function
        // Example:
         Mail::to([director, $divisionManager, $unit])
             ->send(new EscalationMail($violation, $group));

        // For testing/logging
        $this->info("Escalation email sent for CR ID {$violation->cr_id} to group {$group->id}");
    }
}
