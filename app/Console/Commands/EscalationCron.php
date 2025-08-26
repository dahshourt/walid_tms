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
        // Step 1: Fetch SLA rules
        $slaRecords = DB::table('sla_calculations')->get();

        foreach ($slaRecords as $sla) {
            // Calculate SLA deadline considering business hours/days
            $deadline = $sla->type === 'day'
                ? $this->addBusinessDays(Carbon::parse($sla->created_at), $sla->sla_time)
                : $this->addBusinessHours(Carbon::parse($sla->created_at), $sla->sla_time);

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

                if ($group) {
                    $director = DB::table('directors')->where('id', $group->director_id)->first();
                    $divisionManager = DB::table('division_managers')->where('id', $group->division_manager_id)->first();
                    $unit = DB::table('units')->where('id', $group->unit_id)->first();

                    $directorEmail = $director?->email;
                    $divisionManagerEmail = $divisionManager?->division_manager_email;
                    $unitManager = $unit?->manager_name;

                    // Step 4: Send escalation email
                    $this->sendEscalationMail($violation, $group, $directorEmail, $divisionManagerEmail, $unitManager);
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

    private function sendEscalationMail($violation, $group, $director, $divisionManager, $unit)
    {
        // 👇 Replace with your built-in mail function
        // Example:
         Mail::to([$director, $divisionManager, $unit])
             ->send(new EscalationMail($violation, $group));

        $this->info("Escalation email sent for CR ID {$violation->cr_id} to group {$group->id}");
    }
}
