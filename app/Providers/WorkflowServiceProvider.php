<?php

namespace App\Providers;

use App\Services\Workflow\ParallelWorkflowService;
use Illuminate\Support\ServiceProvider;

class WorkflowServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ParallelWorkflowService::class, function ($app) {
            return new ParallelWorkflowService();
        });

        // Register the facade
        $this->app->alias(ParallelWorkflowService::class, 'parallel-workflow');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
