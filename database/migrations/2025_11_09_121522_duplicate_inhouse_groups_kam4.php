<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $statusMapping = []; // Map old status_id => new status_id
            
            // ========================================
            // STEP 1: Create new KAM workflow type
            // ========================================
            echo "Step 1: Creating KAM workflow type...\n";
            
            // Check if KAM workflow type already exists
            $existingKamWorkflow = DB::table('workflow_type')
                ->where('name', 'KAM')
                ->first();
            
            if ($existingKamWorkflow) {
                $kamWorkflowTypeId = $existingKamWorkflow->id;
                echo "KAM workflow type already exists (ID: {$kamWorkflowTypeId})\n";
            } else {
                // Get In-house workflow to copy parent_id and active status
                $inHouseWorkflow = DB::table('workflow_type')->where('id', 3)->first();
                
                $kamWorkflowTypeId = DB::table('workflow_type')->insertGetId([
                    'name' => 'KAM',
                    'parent_id' => $inHouseWorkflow->parent_id ?? null,
                    'active' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                echo "Created KAM workflow type (ID: {$kamWorkflowTypeId})\n";
            }
            
            // ========================================
            // STEP 2: Create single KAM Application
            // ========================================
            echo "\nStep 2: Creating KAM application...\n";
            
            $existingKamApp = DB::table('applications')
                ->where('name', 'KAM')
                ->first();
            
            if ($existingKamApp) {
                $kamApplicationId = $existingKamApp->id;
                echo "KAM application already exists (ID: {$kamApplicationId})\n";
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
                
                echo "Created KAM application (ID: {$kamApplicationId})\n";
            }
            
            // ========================================
            // STEP 3: Duplicate Statuses with  kam suffix
            // ========================================
            echo "\nStep 3: Duplicating statuses for KAM workflow...\n";
            
            // Get all statuses used in In-house workflow (type_id = 1)
            $inHouseStatuses = DB::table('statuses as s')
                ->join('new_workflow as nw', function($join) {
                    $join->on('nw.from_status_id', '=', 's.id');
                })
                ->where('nw.type_id', 3)
                ->select('s.*')
                ->distinct()
                ->get();
            
            // Also get statuses from new_workflow_statuses
            $additionalStatuses = DB::table('statuses as s')
                ->join('new_workflow_statuses as nws', 'nws.to_status_id', '=', 's.id')
                ->join('new_workflow as nw', 'nw.id', '=', 'nws.new_workflow_id')
                ->where('nw.type_id', 3)
                ->select('s.*')
                ->distinct()
                ->get();
            
            // Merge both collections
            $allStatuses = $inHouseStatuses->merge($additionalStatuses)->unique('id');
            
            echo "Found " . $allStatuses->count() . " statuses to duplicate\n";
            
            foreach ($allStatuses as $status) {
                // Check if  kam status already exists in statuses table
                $existingKamStatus = DB::table('statuses')
                    ->where('status_name', $status->status_name . ' kam')
                    ->first();
                
                if ($existingKamStatus) {
                    $statusMapping[$status->id] = $existingKamStatus->id;
                    echo "Status already exists: {$status->status_name} kam (ID: {$existingKamStatus->id})\n";
                } else {
                    // Duplicate status with  kam suffix in statuses table
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
                    echo "Created status: {$status->status_name} kam (Old ID: {$status->id} => New ID: {$newStatusId})\n";
                }
            }
            
            // ========================================
            // STEP 4: Duplicate Workflow (new_workflow) with KAM type_id
            // ========================================
            echo "\nStep 4: Duplicating workflows for KAM...\n";
            
            $inHouseWorkflows = DB::table('new_workflow')
                ->where('type_id', 3)
                ->get();
            
            $workflowMapping = []; // Map old workflow_id => new workflow_id
            
            foreach ($inHouseWorkflows as $workflow) {
                $newFromStatusId = $statusMapping[$workflow->from_status_id] ?? null;
                
                if (!$newFromStatusId) {
                    echo "Skipping workflow {$workflow->id} - from_status not found\n";
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
                    'type_id' => $kamWorkflowTypeId, // Use KAM workflow type ID
                ]);
                
                $workflowMapping[$workflow->id] = $newWorkflowId;
                echo "Created workflow: Old ID {$workflow->id} => New ID {$newWorkflowId}\n";
            }
            
            // ========================================
            // STEP 5: Duplicate Workflow Statuses (new_workflow_statuses)
            // ========================================
            echo "\nStep 5: Duplicating workflow statuses...\n";
            
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
            // STEP 6: Duplicate Groups with  kam suffix
            // ========================================
            echo "\nStep 6: Duplicating groups...\n";
            
            $inHouseGroups = DB::table('groups as g')
                ->join('group_applications as ga', 'ga.group_id', '=', 'g.id')
                ->join('applications as a', 'a.id', '=', 'ga.application_id')
                ->where('a.workflow_type_id', 3)
                ->select('g.*')
                ->distinct()
                ->get();
            
            echo "Found " . $inHouseGroups->count() . " In-house groups to duplicate\n";
            
            foreach ($inHouseGroups as $group) {
                echo "Duplicating group: {$group->title}\n";
                
                // Check if  kam group already exists
                $existingKamGroup = DB::table('groups')
                    ->where('title', $group->title . ' kam')
                    ->first();
                
                if ($existingKamGroup) {
                    echo "Group already exists: {$group->title} kam\n";
                    continue;
                }
                
                // Duplicate group with  kam suffix
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
                
                // Duplicate group_statuses with new status IDs
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
                
                // Link new group to the single KAM application
                DB::table('group_applications')->insert([
                    'application_id' => $kamApplicationId,
                    'group_id' => $newGroupId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Duplicate permissions (model_has_roles)
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
                
                // Duplicate custom_fields_groups_type with KAM workflow type
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
                        'wf_type_id' => $kamWorkflowTypeId, // Use KAM workflow type
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
                
                // DO NOT duplicate user_groups - users will be assigned manually
                // This section is intentionally removed
                
                // Duplicate SLA calculations (if table exists)
                if (DB::getSchemaBuilder()->hasTable('sla_calculations')) {
                    $slaCalcs = DB::table('sla_calculations')
                        ->where('group_id', $group->id)
                        ->get();
                    
                    foreach ($slaCalcs as $sla) {
                        $slaStatusId = $statusMapping[$sla->status_id] ?? $sla->status_id;
                        
                        try {
                            DB::table('sla_calculations')->insert([
                                'unit_sla_time' => $sla->unit_sla_time,
                                'sla_type_unit' => $sla->sla_type_unit,
                                'division_sla_time' => $sla->division_sla_time,
                                'sla_type_division' => $sla->sla_type_division,
                                'director_sla_time' => $sla->director_sla_time,
                                'sla_type_director' => $sla->sla_type_director,
                                'status_id' => $slaStatusId,
                                'group_id' => $newGroupId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } catch (\Exception $e) {
                            echo "Warning: Could not duplicate SLA calculation: {$e->getMessage()}\n";
                        }
                    }
                }
                
                echo "Created new group: {$group->title} kam (ID: {$newGroupId})\n";
            }
            
            echo "\n✅ Migration completed successfully!\n";
            echo "Summary:\n";
            echo "  - Created KAM workflow type (ID: {$kamWorkflowTypeId})\n";
            echo "  - Created KAM application (ID: {$kamApplicationId})\n";
            echo "  - Statuses: " . count($statusMapping) . "\n";
            echo "  - Workflows: " . count($workflowMapping) . "\n";
            echo "  - Workflow Statuses: {$wfStatusCount}\n";
            echo "  - Groups: " . $inHouseGroups->count() . "\n";
            echo "  - NOTE: Users NOT duplicated - assign manually\n";
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            echo "Rolling back migration...\n";
            
            // Delete KAM application
            $kamApp = DB::table('applications')->where('name', 'KAM')->first();
            if ($kamApp) {
                echo "Deleting KAM application...\n";
                DB::table('group_applications')->where('application_id', $kamApp->id)->delete();
                DB::table('applications')->where('id', $kamApp->id)->delete();
            }
            
            // Delete  kam groups and related data
            $kamGroups = DB::table('groups')
                ->where('title', 'like', '% kam')
                ->pluck('id');
            
            if ($kamGroups->isNotEmpty()) {
                echo "Deleting " . $kamGroups->count() . "  kam groups and related data...\n";
                
                DB::table('group_statuses')->whereIn('group_id', $kamGroups)->delete();
                DB::table('group_applications')->whereIn('group_id', $kamGroups)->delete();
                DB::table('custom_fields_groups_type')->whereIn('group_id', $kamGroups)->delete();
                
                if (DB::getSchemaBuilder()->hasTable('sla_calculations')) {
                    DB::table('sla_calculations')->whereIn('group_id', $kamGroups)->delete();
                }
                
                if (DB::getSchemaBuilder()->hasTable('model_has_roles')) {
                    DB::table('model_has_roles')
                        ->where('model_type', 'App\\Models\\Group')
                        ->whereIn('model_id', $kamGroups)
                        ->delete();
                }
                
                DB::table('groups')->whereIn('id', $kamGroups)->delete();
            }
            
            // Delete  kam statuses
            $kamStatuses = DB::table('statuses')
                ->where('status_name', 'like', '% kam')
                ->pluck('id');
            
            if ($kamStatuses->isNotEmpty()) {
                echo "Deleting " . $kamStatuses->count() . "  kam statuses and related data...\n";
                
                DB::table('new_workflow_statuses')->whereIn('to_status_id', $kamStatuses)->delete();
                DB::table('new_workflow')->whereIn('from_status_id', $kamStatuses)->delete();
                DB::table('statuses')->whereIn('id', $kamStatuses)->delete();
            }
            
            // Delete KAM workflow type
            $kamWorkflowType = DB::table('workflow_type')->where('name', 'KAM')->first();
            if ($kamWorkflowType) {
                echo "Deleting KAM workflow type...\n";
                
                DB::table('new_workflow')->where('type_id', $kamWorkflowType->id)->delete();
                DB::table('custom_fields_groups_type')->where('wf_type_id', $kamWorkflowType->id)->delete();
                DB::table('workflow_type')->where('id', $kamWorkflowType->id)->delete();
            }
            
            echo "✅ Rollback completed!\n";
        });
    }
};