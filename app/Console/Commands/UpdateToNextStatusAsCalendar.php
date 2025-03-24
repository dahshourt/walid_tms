<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Factories\ChangeRequest\ChangeRequestFactory;

class UpdateToNextStatusAsCalendar   extends Command
{

    private $changerequest;
    public function __construct(ChangeRequestFactory $changerequest)
    {
        parent::__construct();
        $this->changerequest = $changerequest::index();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CalendarUpdateStatus:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the  promo CR to next status as per Calendar';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->changerequest->update_to_next_status_calendar();
    }
}
