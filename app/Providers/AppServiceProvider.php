<?php

namespace App\Providers;

use App\Contracts\ChangeRequest\ChangeRequestRepositoryInterface;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Helpers\StatusHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ChangeRequestRepositoryInterface::class, ChangeRequestRepository::class);
        foreach (glob(app_path('Helpers') . '/*.php') as $file) {
            require_once $file;
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
 
    public function boot()
    {
        if (!Schema::hasTable('workflow_type') || app()->runningInConsole()) {
            return;
        }
    
        // Get existing workflows from config
        $workflows = Config::get('change_request.workflows', []);
    
        // Fetch workflow type "KAM" from database
        $kamWorkflowId = DB::table('workflow_type')
            ->where('name', 'KAM')
            ->value('id');
    
        if ($kamWorkflowId) {
            $workflows['KAM'] = $kamWorkflowId;
        }
    
        // Update config at runtime
        Config::set('change_request.workflows', $workflows);
        
        // Load existing config
        $statusIds = Config::get('change_request.status_ids', []);
        
        // Loop through each status in config
        foreach ($statusIds as $key => $value) {
            $kamName = ucwords(str_replace('_', ' ', $key)) . ' kam';

            // Find that name in DB
            $kamId = DB::table('statuses')
                ->whereRaw('LOWER(status_name) = ?', [strtolower($kamName)])
                ->value('id');
    
            // If found, create new config key 'pending_cab_kam'
            if ($kamId && !array_key_exists($key . '_kam', $statusIds)) {
                $statusIds[$key . '_kam'] = $kamId;
            }
        }
        
        // Update the config in runtime
        Config::set('change_request.status_ids', $statusIds);
        
        // URL::forceRootUrl('https://10.19.44.26/index.php'); // replace with your server IP
        Paginator::useBootstrap();
        Schema::defaultstringLength(191);
    }
}
