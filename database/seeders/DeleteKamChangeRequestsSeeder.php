<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeleteKamChangeRequestsSeeder extends Seeder
{
    private function backupTableInDatabase($table)
    {
        $date = date('Y_m_d');
        $backupTable = "{$table}_{$date}";
    
        try {
            DB::statement("DROP TABLE IF EXISTS `$backupTable`");
            DB::statement("CREATE TABLE `$backupTable` LIKE `$table`");
            DB::statement("INSERT INTO `$backupTable` SELECT * FROM `$table`");
            $this->command->info("✅ Backup created: {$backupTable}");
        } catch (\Exception $e) {
            $this->command->warn("Could not create backup: " . $e->getMessage());
        }
    }

    public function run(): void
    {
        $this->command->info("===========================================");
        $this->command->info("Delete KAM Change Requests Seeder");
        $this->command->info("===========================================\n");

        // Backup change_request table if it exists
        if (DB::getSchemaBuilder()->hasTable('change_request')) {
            $this->backupTableInDatabase('change_request');
        }

        DB::transaction(function () {
            // Get KAM Workflow Type
            $this->command->info("\nStep 1: Getting KAM workflow type...");
            
            $kamWorkflowType = DB::table('workflow_type')
                ->where('name', 'KAM')
                ->first();
            
            if (!$kamWorkflowType) {
                $this->command->warn("KAM workflow type not found. Nothing to delete.");
                return;
            }
            
            $kamWorkflowTypeId = $kamWorkflowType->id;
            $this->command->info("KAM Workflow Type ID: {$kamWorkflowTypeId}");
            
            // Check if change_request table exists
            if (!DB::getSchemaBuilder()->hasTable('change_request')) {
                $this->command->warn("Table 'change_request' not found in database.");
                return;
            }
            
            // Check columns
            $columns = DB::getSchemaBuilder()->getColumnListing('change_request');
            
            if (!in_array('workflow_type_id', $columns)) {
                $this->command->error("Column 'workflow_type_id' not found in change_request table.");
                $this->command->info("Available columns: " . implode(', ', $columns));
                return;
            }
            
            // Count and delete
            $changeRequestsCount = DB::table('change_request')
                ->where('workflow_type_id', $kamWorkflowTypeId)
                ->count();
            
            if ($changeRequestsCount == 0) {
                $this->command->info("No change requests found for KAM workflow type.");
                return;
            }
            
            $this->command->warn("Found {$changeRequestsCount} change requests for KAM workflow");
            
            // Delete without asking
            $this->command->info("\nDeleting change requests...");
            
            $deletedCount = DB::table('change_request')
                ->where('workflow_type_id', $kamWorkflowTypeId)
                ->delete();
            
            // Summary
            $this->command->info("\n✅ Successfully deleted {$deletedCount} change requests for KAM workflow!");
            $this->command->table(
                ['Item', 'Value'],
                [
                    ['KAM Workflow Type ID', $kamWorkflowTypeId],
                    ['Change Requests Deleted', $deletedCount],
                ]
            );
        });
    }
}