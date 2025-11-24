<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KamWorkflowRollbackSeeder extends Seeder
{
    /**
     * Run the seeder to rollback KAM workflow data.
     */
    public function run(): void
    {
        
        DB::transaction(function () {
            $this->command->info("Rolling back KAM workflow seeder...");
            
            // Delete KAM application
            $kamApp = DB::table('applications')->where('name', 'KAM')->first();
            if ($kamApp) {
                $this->command->info("Deleting KAM application (ID: {$kamApp->id})...");
                DB::table('group_applications')->where('application_id', $kamApp->id)->delete();
                DB::table('applications')->where('id', $kamApp->id)->delete();
            } else {
                $this->command->warn("KAM application not found");
            }
            
            // Delete kam groups and related data
            $kamGroups = DB::table('groups')
                ->where('title', 'like', '% kam')
                ->pluck('id');
            
            if ($kamGroups->isNotEmpty()) {
                $this->command->info("Deleting " . $kamGroups->count() . " kam groups and related data...");
                
                DB::table('group_statuses')->whereIn('group_id', $kamGroups)->delete();
                DB::table('group_applications')->whereIn('group_id', $kamGroups)->delete();
                DB::table('custom_fields_groups_type')->whereIn('group_id', $kamGroups)->delete();
                
                // if (DB::getSchemaBuilder()->hasTable('sla_calculations')) {
                //     DB::table('sla_calculations')->whereIn('group_id', $kamGroups)->delete();
                // }
                
                if (DB::getSchemaBuilder()->hasTable('model_has_roles')) {
                    DB::table('model_has_roles')
                        ->where('model_type', 'App\\Models\\Group')
                        ->whereIn('model_id', $kamGroups)
                        ->delete();
                }
                
                DB::table('groups')->whereIn('id', $kamGroups)->delete();
                $this->command->info("Deleted {$kamGroups->count()} kam groups");
            } else {
                $this->command->warn("No kam groups found");
            }
            
            // Delete kam statuses
            $kamStatuses = DB::table('statuses')
                ->where('status_name', 'like', '% kam')
                ->pluck('id');
            
            if ($kamStatuses->isNotEmpty()) {
                $this->command->info("Deleting " . $kamStatuses->count() . " kam statuses and related data...");
                
                DB::table('new_workflow_statuses')->whereIn('to_status_id', $kamStatuses)->delete();
                DB::table('new_workflow')->whereIn('from_status_id', $kamStatuses)->delete();
                DB::table('statuses')->whereIn('id', $kamStatuses)->delete();
                $this->command->info("Deleted {$kamStatuses->count()} kam statuses");
            } else {
                $this->command->warn("No kam statuses found");
            }
            
            // Delete KAM workflow type
            $kamWorkflowType = DB::table('workflow_type')->where('name', 'KAM')->first();
            if ($kamWorkflowType) {
                $this->command->info("Deleting KAM workflow type (ID: {$kamWorkflowType->id})...");
                
                DB::table('new_workflow')->where('type_id', $kamWorkflowType->id)->delete();
                DB::table('custom_fields_groups_type')->where('wf_type_id', $kamWorkflowType->id)->delete();
                DB::table('workflow_type')->where('id', $kamWorkflowType->id)->delete();
            } else {
                $this->command->warn("KAM workflow type not found");
            }
            
            $this->command->info("✅ Rollback completed successfully!");
        });
    }
}