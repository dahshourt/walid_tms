<?php

namespace App\Services\ChangeRequest;

use Illuminate\Support\ServiceProvider;
use App\Services\StatusConfigService;
use Illuminate\Support\Facades\Config;

class ChangeRequestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Always reload status IDs from database after app boots
        if ($this->shouldLoadDynamicStatuses()) {
            $this->loadDynamicStatusIds();
        }
    }

    /**
     * Determine if we should load dynamic statuses
     */
    protected function shouldLoadDynamicStatuses(): bool
    {
        // Don't load during config:cache or config:clear
        if ($this->app->runningInConsole()) {
            $command = $_SERVER['argv'][1] ?? '';
            
            // Skip for these commands
            $skipCommands = [
                'config:cache',
                'config:clear',
                'package:discover',
            ];
            
            foreach ($skipCommands as $skipCommand) {
                if (str_contains($command, $skipCommand)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Load dynamic status IDs from database and override config
     * Loads BOTH default and KAM status IDs separately
     */
    protected function loadDynamicStatusIds(): void
    {
        try {
            // Load default status IDs from database
            $statusIds = StatusConfigService::loadStatusIds();
            
            // Override config with database values (even if config is cached)
            if (!empty($statusIds)) {
                Config::set('change_request.status_ids', $statusIds);
            }

            // Load KAM status IDs from database
            $kamStatusIds = StatusConfigService::loadStatusIdsKam();
            
            // Override config with database values
            if (!empty($kamStatusIds)) {
                Config::set('change_request.status_ids_kam', $kamStatusIds);
            }

        } catch (\Exception $e) {
            // Log the error but don't break the application
            if (config('app.debug')) {
                \Log::warning('Failed to load dynamic status IDs: ' . $e->getMessage());
            }
        }
    }
}