<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KamWorkflowSeeder extends Seeder
{

    private function backupTableInDatabase($table)
    {
        $date = date('Y_m_d'); // format without time
        $backupTable = "{$table}_{$date}";
    
        // Drop old table if exists (optional)
        DB::statement("DROP TABLE IF EXISTS `$backupTable`");
    
        // Create a copy structure + data
        DB::statement("CREATE TABLE `$backupTable` LIKE `$table`");
        DB::statement("INSERT INTO `$backupTable` SELECT * FROM `$table`");
    }
    

    public function run(): void
    {

        $this->backupTableInDatabase('workflow_type');
        $this->backupTableInDatabase('statuses');
        $this->backupTableInDatabase('new_workflow');
        $this->backupTableInDatabase('new_workflow_statuses');
        $this->backupTableInDatabase('groups');
        $this->backupTableInDatabase('group_statuses');
        $this->backupTableInDatabase('group_applications');
        $this->backupTableInDatabase('model_has_roles');
        $this->backupTableInDatabase('custom_fields_groups_type');
       
        $this->backupTableInDatabase('applications');


        DB::transaction(function () {
            $statusMapping = [];
            
            // ========================================
            // STEP 1: Create new KAM workflow type
            // ========================================
            $this->command->info("Step 1: Creating KAM workflow type...");
            
            $existingKamWorkflow = DB::table('workflow_type')
                ->where('name', 'KAM')
                ->first();
            
            if ($existingKamWorkflow) {
                $kamWorkflowTypeId = $existingKamWorkflow->id;
                $this->command->info("KAM workflow type already exists (ID: {$kamWorkflowTypeId})");
            } else {
                $inHouseWorkflow = DB::table('workflow_type')->where('id', 3)->first();
                
                $kamWorkflowTypeId = DB::table('workflow_type')->insertGetId([
                    'name' => 'KAM',
                    'parent_id' => $inHouseWorkflow->parent_id ?? null,
                    'active' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $this->command->info("Created KAM workflow type (ID: {$kamWorkflowTypeId})");
            }
            
            // ========================================
            // STEP 2: Create single KAM Application
            // ========================================
            $this->command->info("Step 2: Creating KAM application...");
            
            $existingKamApp = DB::table('applications')
                ->where('name', 'KAM')
                ->first();
            
            if ($existingKamApp) {
                $kamApplicationId = $existingKamApp->id;
                $this->command->info("KAM application already exists (ID: {$kamApplicationId})");
            } else {
                $kamApplicationId = DB::table('applications')->insertGetId([
                    'parent_id' => null,
                    'name' => 'KAM',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'workflow_type_id' => $kamWorkflowTypeId,
                    'active' => '1',
                    'file' => null,
                ]);
                
                $this->command->info("Created KAM application (ID: {$kamApplicationId})");
            }
            
            // ========================================
            // STEP 3: Duplicate Statuses with kam suffix
            // ========================================
            $this->command->info("Step 3: Duplicating statuses for KAM workflow...");
            
            $inHouseStatuses = DB::table('statuses as s')
                ->join('new_workflow as nw', function($join) {
                    $join->on('nw.from_status_id', '=', 's.id');
                })
                ->where('nw.type_id', 3)
                ->select('s.*')
                ->distinct()
                ->get();
            
            $additionalStatuses = DB::table('statuses as s')
                ->join('new_workflow_statuses as nws', 'nws.to_status_id', '=', 's.id')
                ->join('new_workflow as nw', 'nw.id', '=', 'nws.new_workflow_id')
                ->where('nw.type_id', 3)
                ->select('s.*')
                ->distinct()
                ->get();
            
            $allStatuses = $inHouseStatuses->merge($additionalStatuses)->unique('id');
            
            $this->command->info("Found " . $allStatuses->count() . " statuses to duplicate");
            
            foreach ($allStatuses as $status) {
                $existingKamStatus = DB::table('statuses')
                    ->where('status_name', $status->status_name . ' kam')
                    ->first();
                
                if ($existingKamStatus) {
                    $statusMapping[$status->id] = $existingKamStatus->id;
                    $this->command->info("Status already exists: {$status->status_name} kam (ID: {$existingKamStatus->id})");
                } else {
                    $newStatusId = DB::table('statuses')->insertGetId([
                        'status_name' => $status->status_name . ' kam',
                        'stage_id' => $status->stage_id,
                        'active' => $status->active,
                        'type' => $status->type,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'high_level_status_id' => $status->high_level_status_id,
                        'sla' => $status->sla,
                        'defect' => $status->defect,
                        'view_technical_team_flag' => $status->view_technical_team_flag,
                    ]);
                    
                    $statusMapping[$status->id] = $newStatusId;
                    $this->command->info("Created status: {$status->status_name} kam (Old ID: {$status->id} => New ID: {$newStatusId})");
                }
            }
            
            // ========================================
            // STEP 4: Duplicate Workflow
            // ========================================
            $this->command->info("Step 4: Duplicating workflows for KAM...");
            
            $inHouseWorkflows = DB::table('new_workflow')
                ->where('type_id', 3)
                ->get();
            
            $workflowMapping = [];
            
            foreach ($inHouseWorkflows as $workflow) {
                $newFromStatusId = $statusMapping[$workflow->from_status_id] ?? null;
                
                if (!$newFromStatusId) {
                    $this->command->warn("Skipping workflow {$workflow->id} - from_status not found");
                    continue;
                }
                
                $newWorkflowId = DB::table('new_workflow')->insertGetId([
                    'same_time_from' => $workflow->same_time_from,
                    'previous_status_id' => $workflow->previous_status_id,
                    'from_status_id' => $newFromStatusId,
                    'active' => $workflow->active,
                    'same_time' => $workflow->same_time,
                    'workflow_type' => $workflow->workflow_type,
                    'to_status_label' => $workflow->to_status_label ? $workflow->to_status_label . ' kam' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'type_id' => $kamWorkflowTypeId,
                ]);
                
                $workflowMapping[$workflow->id] = $newWorkflowId;
                $this->command->info("Created workflow: Old ID {$workflow->id} => New ID {$newWorkflowId}");
            }
            
            // ========================================
            // STEP 5: Duplicate Workflow Statuses
            // ========================================
            $this->command->info("Step 5: Duplicating workflow statuses...");
            
            $wfStatusCount = 0;
            foreach ($workflowMapping as $oldWorkflowId => $newWorkflowId) {
                $workflowStatuses = DB::table('new_workflow_statuses')
                    ->where('new_workflow_id', $oldWorkflowId)
                    ->get();
                
                foreach ($workflowStatuses as $wfStatus) {
                    $newToStatusId = $statusMapping[$wfStatus->to_status_id] ?? null;
                    
                    if (!$newToStatusId) {
                        continue;
                    }
                    
                    DB::table('new_workflow_statuses')->insert([
                        'new_workflow_id' => $newWorkflowId,
                        'to_status_id' => $newToStatusId,
                        'default_to_status' => $wfStatus->default_to_status,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'dependency_ids' => $wfStatus->dependency_ids,
                    ]);
                    $wfStatusCount++;
                }
            }
            
            // ========================================
            // STEP 6: Duplicate Groups
            // ========================================
            $this->command->info("Step 6: Duplicating groups...");
            
            $inHouseGroups = DB::table('groups as g')
                ->join('group_applications as ga', 'ga.group_id', '=', 'g.id')
                ->join('applications as a', 'a.id', '=', 'ga.application_id')
                ->where('a.workflow_type_id', 3)
                ->select('g.*')
                ->distinct()
                ->get();
            
            $this->command->info("Found " . $inHouseGroups->count() . " In-house groups to duplicate");
            
            foreach ($inHouseGroups as $group) {
                $this->command->info("Duplicating group: {$group->title}");
                
                $existingKamGroup = DB::table('groups')
                    ->where('title', $group->title . ' kam')
                    ->first();
                
                if ($existingKamGroup) {
                    $this->command->info("Group already exists: {$group->title} kam");
                    continue;
                }
                
                $newGroupId = DB::table('groups')->insertGetId([
                    'title' => $group->title . ' kam',
                    'description' => $group->description,
                    'parent_id' => $group->parent_id,
                    'head_group_name' => $group->head_group_name ? $group->head_group_name . ' kam' : null,
                    'recieve_notification' => $group->recieve_notification,
                    'head_group_email' => $group->head_group_email,
                    'active' => $group->active,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'man_power' => $group->man_power,
                    'technical_team' => $group->technical_team,
                    'director_id' => $group->director_id,
                    'division_manager_id' => $group->division_manager_id,
                    'unit_id' => $group->unit_id,
                ]);
                
                // Duplicate group_statuses
                $groupStatuses = DB::table('group_statuses')
                    ->where('group_id', $group->id)
                    ->get();
                
                foreach ($groupStatuses as $gs) {
                    $newStatusId = $statusMapping[$gs->status_id] ?? $gs->status_id;
                    
                    DB::table('group_statuses')->insert([
                        'status_id' => $newStatusId,
                        'group_id' => $newGroupId,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'type' => $gs->type,
                    ]);
                }
                
                // Link to KAM application
                DB::table('group_applications')->insert([
                    'application_id' => $kamApplicationId,
                    'group_id' => $newGroupId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Duplicate permissions
                if (DB::getSchemaBuilder()->hasTable('model_has_roles')) {
                    $roles = DB::table('model_has_roles')
                        ->where('model_id', $group->id)
                        ->where('model_type', 'App\\Models\\Group')
                        ->get();
                    
                    foreach ($roles as $role) {
                        DB::table('model_has_roles')->insert([
                            'role_id' => $role->role_id,
                            'model_type' => $role->model_type,
                            'model_id' => $newGroupId,
                        ]);
                    }
                }
                
                // Duplicate custom_fields_groups_type
                $customFieldsGroups = DB::table('custom_fields_groups_type as cfg')
                    ->where('cfg.group_id', $group->id)
                    ->where('cfg.wf_type_id', 3)
                    ->get();
                
                foreach ($customFieldsGroups as $cfg) {
                    $newStatusIdForField = isset($cfg->status_id) && isset($statusMapping[$cfg->status_id]) 
                        ? $statusMapping[$cfg->status_id] 
                        : $cfg->status_id;
                    
                    DB::table('custom_fields_groups_type')->insert([
                        'form_type' => $cfg->form_type,
                        'group_id' => $newGroupId,
                        'wf_type_id' => $kamWorkflowTypeId,
                        'custom_field_id' => $cfg->custom_field_id,
                        'sort' => $cfg->sort,
                        'active' => $cfg->active,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'validation_type_id' => $cfg->validation_type_id,
                        'enable' => $cfg->enable,
                        'status_id' => $newStatusIdForField,
                    ]);
                }
                
                // Duplicate SLA calculations
                // if (DB::getSchemaBuilder()->hasTable('sla_calculations')) {
                //     $slaCalcs = DB::table('sla_calculations')
                //         ->where('group_id', $group->id)
                //         ->get();
                    
                //     foreach ($slaCalcs as $sla) {
                //         $slaStatusId = $statusMapping[$sla->status_id] ?? $sla->status_id;
                        
                //         try {
                //             DB::table('sla_calculations')->insert([
                //                 'unit_sla_time' => $sla->unit_sla_time,
                //                 'sla_type_unit' => $sla->sla_type_unit,
                //                 'division_sla_time' => $sla->division_sla_time,
                //                 'sla_type_division' => $sla->sla_type_division,
                //                 'director_sla_time' => $sla->director_sla_time,
                //                 'sla_type_director' => $sla->sla_type_director,
                //                 'status_id' => $slaStatusId,
                //                 'group_id' => $newGroupId,
                //                 'created_at' => now(),
                //                 'updated_at' => now(),
                //             ]);
                //         } catch (\Exception $e) {
                //             $this->command->warn("Could not duplicate SLA calculation: {$e->getMessage()}");
                //         }
                //     }
                // }
                
                $this->command->info("Created new group: {$group->title} kam (ID: {$newGroupId})");
            }
            
            $this->command->info("✅ Seeding completed successfully!");
            $this->command->table(
                ['Item', 'Count'],
                [
                    ['KAM Workflow Type ID', $kamWorkflowTypeId],
                    ['KAM Application ID', $kamApplicationId],
                    ['Statuses', count($statusMapping)],
                    ['Workflows', count($workflowMapping)],
                    ['Workflow Statuses', $wfStatusCount],
                    ['Groups', $inHouseGroups->count()],
                ]
            );
            $this->command->warn("NOTE: Users NOT duplicated - assign manually");
        });
    }
}