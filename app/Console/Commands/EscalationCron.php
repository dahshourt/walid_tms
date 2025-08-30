<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
        $now = Carbon::now();
        
        // Step 1: Fetch SLA rules
        $slaRecords = DB::table('sla_calculations')->get();

        foreach ($slaRecords as $sla) {
            // Step 2: Find change_request_statuses that match this SLA rule
            $changeRequests = DB::table('change_request_statuses')
                ->where('new_status_id', $sla->status_id)
                ->where('active', '1')
                ->get();

            foreach ($changeRequests as $changeRequest) {
                // Calculate SLA deadlines from when the status was set
                $statusSetTime = Carbon::parse($changeRequest->created_at);
                
                // Use separate type fields for each level
                $unitDeadline = $sla->sla_type_unit === 'day'
                    ? $this->addBusinessDays($statusSetTime->copy(), $sla->unit_sla_time)
                    : $this->addBusinessHours($statusSetTime->copy(), $sla->unit_sla_time);
                    
                $divisionDeadline = $sla->sla_type_division === 'day'
                    ? $this->addBusinessDays($statusSetTime->copy(), $sla->division_sla_time)
                    : $this->addBusinessHours($statusSetTime->copy(), $sla->division_sla_time);
                    
                $directorDeadline = $sla->sla_type_director === 'day'
                    ? $this->addBusinessDays($statusSetTime->copy(), $sla->division_sla_time)
                    : $this->addBusinessHours($statusSetTime->copy(), $sla->division_sla_time);

                // Step 3: Check which SLA levels have been violated
                $unitViolated = $now->gt($unitDeadline);
                $divisionViolated = $now->gt($divisionDeadline);
                $directorViolated = $now->gt($directorDeadline);

                if ($unitViolated || $divisionViolated || $directorViolated) {
                    // Step 4: Get group and manager details
                    $group = DB::table('groups')
                        ->where('id', $sla->group_id)
                        ->first();

                    if ($group) {
                        $director = DB::table('directors')->where('id', $group->director_id)->first();
                        $divisionManager = DB::table('division_managers')->where('id', $group->division_manager_id)->first();
                        $unit = DB::table('units')->where('id', $group->unit_id)->first();
                        
                        // Step 5: Send escalation emails based on violation level
                        $this->sendEscalationMails(
                            $changeRequest, 
                            $group, 
                            $director, 
                            $divisionManager, 
                            $unit,
                            $unitViolated,
                            $divisionViolated,
                            $directorViolated,
                            [
                                'unit_deadline' => $unitDeadline,
                                'division_deadline' => $divisionDeadline,
                                'director_deadline' => $directorDeadline,
                                'status_set_time' => $statusSetTime
                            ]
                        );
                    }
                }
            }
        }

        $this->info('Escalation cron job executed successfully.');
    }

    /**
     * Add business days (Sun–Thu only, 8am–4pm)
     */
    private function addBusinessDays(Carbon $date, int $days): Carbon
    {
        $date = $this->normalizeToBusinessTime($date);

        while ($days > 0) {
            $date->addDay();
            if (!$this->isWeekend($date)) {
                $days--;
            }
        }

        return $this->normalizeToBusinessTime($date);
    }

    /**
     * Add business hours (Sun–Thu only, 8am–4pm)
     */
    private function addBusinessHours(Carbon $date, int $hours): Carbon
    {
        $date = $this->normalizeToBusinessTime($date);

        while ($hours > 0) {
            if ($this->isWeekend($date)) {
                $date->addDay()->setTime(8, 0);
                continue;
            }

            $date->addHour();

            if ($date->hour >= 16) {
                // End of working day → move to next business day at 8 AM
                $date->addDay()->setTime(8, 0);
            } else {
                $hours--;
            }
        }

        return $date;
    }

    /**
     * Ensure the date is inside business hours (8am–4pm, Sun–Thu)
     */
    private function normalizeToBusinessTime(Carbon $date): Carbon
    {
        if ($this->isWeekend($date)) {
            // Jump to Sunday 8am
            while ($this->isWeekend($date)) {
                $date->addDay();
            }
            $date->setTime(8, 0);
        }

        if ($date->hour < 8) {
            $date->setTime(8, 0);
        } elseif ($date->hour >= 16) {
            $date->addDay()->setTime(8, 0);
            while ($this->isWeekend($date)) {
                $date->addDay()->setTime(8, 0);
            }
        }

        return $date;
    }

    /**
     * Check if date is Friday or Saturday
     */
    private function isWeekend(Carbon $date): bool
    {
        return $date->isFriday() || $date->isSaturday();
    }

    /**
     * Send escalation emails based on violation levels with progressive escalation
     */
    private function sendEscalationMails($changeRequest, $group, $director, $divisionManager, $unit, 
                                       $unitViolated, $divisionViolated, $directorViolated, $deadlines)
    {
        // Get existing escalation logs for this CR and status
        $existingLogs = DB::table('escalation_logs')
            ->where('cr_id', $changeRequest->cr_id)
            ->first();

        $now = Carbon::now();

        // Step 1: Check Unit Escalation
        if ($unitViolated && (!$existingLogs || !$existingLogs->unit_sent)) {
            if ($unit && $unit->manager_name) {
                // Send to unit manager
                Mail::to($unit->manager_name)
                    ->send(new EscalationMail($changeRequest, $group, 'Unit Level', $deadlines));

                // Log or update escalation
                if ($existingLogs) {
                    DB::table('escalation_logs')
                        ->where('id', $existingLogs->id)
                        ->update([
                            'unit_sent' => 1,
                            'sent_at' => $now,
                            'updated_at' => $now
                        ]);
                } else {
                    DB::table('escalation_logs')->insert([
                        'cr_id' => $changeRequest->cr_id,
                        'unit_sent' => 1,
                        'division_sent' => 0,
                        'director_sent' => 0,
                        'sent_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                $this->info("Unit escalation sent for CR ID {$changeRequest->cr_id}");
            }
        }

        // Step 2: Check Division Escalation (only if unit was already sent)
        if ($divisionViolated && $existingLogs && $existingLogs->unit_sent && !$existingLogs->division_sent) {
            if ($divisionManager && $divisionManager->division_manager_email) {
                // Send to division manager
                Mail::to($divisionManager->division_manager_email)
                    ->send(new EscalationMail($changeRequest, $group, 'Division Level', $deadlines));

                // Update escalation log
                DB::table('escalation_logs')
                    ->where('id', $existingLogs->id)
                    ->update([
                        'division_sent' => 1,
                        'sent_at' => $now,
                        'updated_at' => $now
                    ]);

                $this->info("Division escalation sent for CR ID {$changeRequest->cr_id}");
            }
        }

        // Step 3: Check Director Escalation (only if both unit and division were sent)
        if ($directorViolated && $existingLogs && $existingLogs->unit_sent && $existingLogs->division_sent && !$existingLogs->director_sent) {
            if ($director && $director->email) {
                // Send to director
                Mail::to($director->email)
                    ->send(new EscalationMail($changeRequest, $group, 'Director Level', $deadlines));

                // Update escalation log
                DB::table('escalation_logs')
                    ->where('id', $existingLogs->id)
                    ->update([
                        'director_sent' => 1,
                        'sent_at' => $now,
                        'updated_at' => $now
                    ]);

                $this->info("Director escalation sent for CR ID {$changeRequest->cr_id}");
            }
        }
    }
}