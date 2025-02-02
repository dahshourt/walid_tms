<?php

namespace App\Console\Commands;
use App\Http\Controllers\Releases\ReleaseController;
use Illuminate\Console\Command;
use App\Factories\Releases\ReleaseFactory;

class UpdateReleaseAndCrs extends Command
{ 

     protected $releaseController;
        public function __construct(ReleaseFactory $releaseFactory)
        {
            parent::__construct();

            // Manually instantiate the controller with its dependency
            $this->releaseController =  $releaseFactory::index();
        }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateReleaseAndCrs:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        parent::__construct();
    }*/

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->releaseController->update_release_its_crs();
    }
}
