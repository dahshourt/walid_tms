<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class WorkflowConfigServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Get the KAM workflow ID from the database
        $kamWorkflow = DB::table('workflow_type')
            ->where('name', 'KAM')
            ->first();

        // If KAM workflow exists, add it to the config
        if ($kamWorkflow) {
            config([
                'change_request.default_values.first_cr_no.' . $kamWorkflow->id => 9000
            ]);
        }
    }

    public function register()
    {
        //
    }
}