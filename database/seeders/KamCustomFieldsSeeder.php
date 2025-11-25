<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KamCustomFieldsSeeder extends Seeder
{
    private function backupTableInDatabase($table)
    {
        $date = date('Y_m_d');
        $backupTable = "{$table}_{$date}";
    
        DB::statement("DROP TABLE IF EXISTS `$backupTable`");
        DB::statement("CREATE TABLE `$backupTable` LIKE `$table`");
        DB::statement("INSERT INTO `$backupTable` SELECT * FROM `$table`");
    }

    public function run(): void
    {
        // Backup relevant tables
        //$this->backupTableInDatabase('custom_fields_groups_type');
		//DB::statement('DELETE FROM custom_fields_groups_type WHERE status_id NOT IN (SELECT id FROM statuses);');
		DB::statement('DELETE FROM custom_fields_groups_type WHERE wf_type_id = 37;');
        DB::transaction(function () {
            // ========================================
            // STEP 1: Get KAM and In-house IDs
            // ========================================
            $this->command->info("Step 1: Getting KAM and In-house workflow type IDs...");
            
            $inHouseWorkflowType = DB::table('workflow_type')
                ->where('id', 3)
                ->first();
            
            $kamWorkflowType = DB::table('workflow_type')
                ->where('name', 'KAM')
                ->first();
            
            if (!$kamWorkflowType) {
                $this->command->error("KAM workflow type not found! Please run KamWorkflowSeeder first.");
                return;
            }
            
            $inHouseTypeId = $inHouseWorkflowType->id;
            $kamWorkflowTypeId = $kamWorkflowType->id;
            
            $this->command->info("In-house Type ID: {$inHouseTypeId}");
            $this->command->info("KAM Type ID: {$kamWorkflowTypeId}");
            
            // ========================================
            // STEP 2: Get Status Mapping
            // ========================================
            $this->command->info("Step 2: Building status mapping...");
            
            $statusMapping = [];
            
            // Get all KAM statuses that have ' kam' suffix
            $kamStatuses = DB::table('statuses')
                ->where('status_name', 'like', '% kam')
                ->get();
            
            foreach ($kamStatuses as $kamStatus) {
                // Remove ' kam' suffix to find original status
                $originalStatusName = str_replace(' kam', '', $kamStatus->status_name);
                
                $originalStatus = DB::table('statuses')
                    ->where('status_name', $originalStatusName)
                    ->first();
                
                if ($originalStatus) {
                    $statusMapping[$originalStatus->id] = $kamStatus->id;
                }
            }
            
            $this->command->info("Found " . count($statusMapping) . " status mappings");
            
            // ========================================
            // STEP 3: Get Group Mapping
            // ========================================
            $this->command->info("Step 3: Building group mapping...");
            
            $groupMapping = [];
            
            // Get all KAM groups that have ' kam' suffix
            $kamGroups = DB::table('groups')
                ->where('title', 'like', '% kam')
                ->get();
            
            foreach ($kamGroups as $kamGroup) {
                // Remove ' kam' suffix to find original group
                $originalGroupTitle = str_replace(' kam', '', $kamGroup->title);
                
                $originalGroup = DB::table('groups')
                    ->where('title', $originalGroupTitle)
                    ->first();
                
                if ($originalGroup) {
                    $groupMapping[$originalGroup->id] = $kamGroup->id;
                }
            }
            
            $this->command->info("Found " . count($groupMapping) . " group mappings");
            
            // ========================================
            // STEP 4: Duplicate Custom Fields
            // ========================================
            $this->command->info("Step 4: Duplicating custom fields from In-house to KAM...");
            
            // Get all custom fields associated with In-house workflow
            $inHouseCustomFields = DB::table('custom_fields_groups_type')
                ->where('wf_type_id', $inHouseTypeId)
                ->get();
            
            $this->command->info("Found " . $inHouseCustomFields->count() . " In-house custom fields to duplicate");
            
            $duplicatedCount = 0;
            $skippedCount = 0;
            $nullGroupCount = 0;
            
            foreach ($inHouseCustomFields as $customField) {
                // Handle NULL or empty group_id
                $newGroupId = null;
                
                if ($customField->group_id) {
                    // Get the corresponding KAM group ID
                    $newGroupId = $groupMapping[$customField->group_id] ?? null;
                    
                    if (!$newGroupId) {
                        $this->command->warn("Skipping custom field - group mapping not found (Group ID: {$customField->group_id})");
                        $skippedCount++;
                        continue;
                    }
                } else {
                    // group_id is NULL - we'll duplicate with NULL group_id
                    $nullGroupCount++;
                }
                
                // Get the corresponding KAM status ID if status_id exists
                $newStatusId = null;
                if ($customField->status_id) {
                    $newStatusId = $statusMapping[$customField->status_id] ?? null;
                    
                    if (!$newStatusId) {
                        $this->command->warn("Skipping custom field - status mapping not found (Status ID: {$customField->status_id})");
                        $skippedCount++;
                        continue;
                    }
                }
                
                // Check if this custom field already exists for KAM
                $existingField = DB::table('custom_fields_groups_type')
                    ->where('wf_type_id', $kamWorkflowTypeId)
                    ->where('custom_field_id', $customField->custom_field_id)
                    ->where('form_type', $customField->form_type)
                    ->when($newGroupId !== null, function ($query) use ($newGroupId) {
                        return $query->where('group_id', $newGroupId);
                    }, function ($query) {
                        return $query->whereNull('group_id');
                    })
                    ->when($newStatusId, function ($query) use ($newStatusId) {
                        return $query->where('status_id', $newStatusId);
                    })
                    ->first();
                
                if ($existingField) {
                    $groupIdDisplay = $newGroupId ?? 'NULL';
                    $this->command->info("Custom field already exists - skipping (Field ID: {$customField->custom_field_id}, Group ID: {$groupIdDisplay})");
                    $skippedCount++;
                    continue;
                }
                
                // Insert the new custom field for KAM
                DB::table('custom_fields_groups_type')->insert([
                    'form_type' => $customField->form_type,
                    'group_id' => $newGroupId,
                    'wf_type_id' => $kamWorkflowTypeId,
                    'custom_field_id' => $customField->custom_field_id,
                    'sort' => $customField->sort,
                    'active' => $customField->active,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'validation_type_id' => $customField->validation_type_id,
                    'enable' => $customField->enable,
                    'status_id' => $newStatusId,
                ]);
                
                $duplicatedCount++;
                
                if ($duplicatedCount % 10 == 0) {
                    $this->command->info("Progress: {$duplicatedCount} custom fields duplicated...");
                }
            }
            
            $this->command->info("\n✅ Custom fields duplication completed!");
            $this->command->table(
                ['Item', 'Count'],
                [
                    ['In-house Type ID', $inHouseTypeId],
                    ['KAM Type ID', $kamWorkflowTypeId],
                    ['Status Mappings', count($statusMapping)],
                    ['Group Mappings', count($groupMapping)],
                    ['Custom Fields with NULL group', $nullGroupCount],
                    ['Custom Fields Duplicated', $duplicatedCount],
                    ['Custom Fields Skipped', $skippedCount],
                    ['Total Processed', $inHouseCustomFields->count()],
                ]
            );
            
            if ($duplicatedCount == 0 && $skippedCount > 0) {
                $this->command->warn("\n⚠️  No custom fields were duplicated!");
                $this->command->warn("This might be because:");
                $this->command->warn("1. All custom fields already exist for KAM");
                $this->command->warn("2. Group mappings are missing (groups need ' kam' suffix)");
                $this->command->warn("3. Status mappings are missing (statuses need ' kam' suffix)");
            }
        });
    }
}