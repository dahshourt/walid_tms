<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KamRejectCancelStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->command->info("Starting KAM Reject and Cancel status creation...");
            
            // ========================================
            // STEP 1: Get KAM workflow type ID
            // ========================================
            $kamWorkflowType = DB::table('workflow_type')
                ->where('name', 'KAM')
                ->first();
            
            if (!$kamWorkflowType) {
                $this->command->error("KAM workflow type not found. Please run KamWorkflowSeeder first.");
                return;
            }
            
            $kamWorkflowTypeId = $kamWorkflowType->id;
            $this->command->info("KAM workflow type ID: {$kamWorkflowTypeId}");
            
            // ========================================
            // STEP 2: Get original Reject status details
            // ========================================
            $originalRejectStatus = DB::table('statuses')
                ->where('status_name', 'LIKE', '%Reject%')
                ->where('status_name', 'NOT LIKE', '%kam%')
                ->first();
            
            if (!$originalRejectStatus) {
                $this->command->error("Original Reject status not found.");
                return;
            }
            
            $this->command->info("Found original Reject status: {$originalRejectStatus->status_name} (ID: {$originalRejectStatus->id})");
            
            // ========================================
            // STEP 3: Create Reject kam status
            // ========================================
            $this->command->info("Step 3: Creating Reject kam status...");
            
            $existingRejectKam = DB::table('statuses')
                ->where('status_name', 'Reject kam')
                ->first();
            
            if ($existingRejectKam) {
                $rejectKamStatusId = $existingRejectKam->id;
                $this->command->info("Reject kam status already exists (ID: {$rejectKamStatusId})");
            } else {
                $rejectKamStatusId = DB::table('statuses')->insertGetId([
                    'status_name' => 'Reject kam',
                    'stage_id' => $originalRejectStatus->stage_id,
                    'active' => $originalRejectStatus->active,
                    'type' => $originalRejectStatus->type,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'high_level_status_id' => $originalRejectStatus->high_level_status_id,
                    'sla' => $originalRejectStatus->sla,
                    'defect' => $originalRejectStatus->defect,
                    'view_technical_team_flag' => $originalRejectStatus->view_technical_team_flag,
                ]);
                
                $this->command->info("Created Reject kam status (ID: {$rejectKamStatusId})");
            }
            
            // ========================================
            // STEP 4: Create Cancel kam status
            // ========================================
            $this->command->info("Step 4: Creating Cancel kam status...");
            
            $existingCancelKam = DB::table('statuses')
                ->where('status_name', 'Cancel kam')
                ->first();
            
            if ($existingCancelKam) {
                $cancelKamStatusId = $existingCancelKam->id;
                $this->command->info("Cancel kam status already exists (ID: {$cancelKamStatusId})");
            } else {
                $cancelKamStatusId = DB::table('statuses')->insertGetId([
                    'status_name' => 'Cancel kam',
                    'stage_id' => $originalRejectStatus->stage_id, // Use same stage as reject
                    'active' => $originalRejectStatus->active,
                    'type' => $originalRejectStatus->type,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'high_level_status_id' => $originalRejectStatus->high_level_status_id,
                    'sla' => $originalRejectStatus->sla,
                    'defect' => $originalRejectStatus->defect,
                    'view_technical_team_flag' => $originalRejectStatus->view_technical_team_flag,
                ]);
                
                $this->command->info("Created Cancel kam status (ID: {$cancelKamStatusId})");
            }
            
            // ========================================
            // STEP 5: Get workflows that lead to original Reject status
            // ========================================
            $this->command->info("Step 5: Finding workflows that lead to Reject status...");
            
            $rejectWorkflows = DB::table('new_workflow_statuses as nws')
                ->join('new_workflow as nw', 'nws.new_workflow_id', '=', 'nw.id')
                ->where('nws.to_status_id', $originalRejectStatus->id)
                ->where('nw.type_id', 3) // In-house workflow
                ->select('nw.*', 'nws.default_to_status')
                ->get();
            
            $this->command->info("Found {$rejectWorkflows->count()} workflows that lead to Reject status");
            
            // ========================================
            // STEP 6: Create similar workflows for Reject kam and Cancel kam
            // ========================================
            $this->command->info("Step 6: Creating KAM workflows for Reject kam and Cancel kam...");
            
            $createdWorkflowsCount = 0;
            
            foreach ($rejectWorkflows as $rejectWorkflow) {
                // Get the KAM version of the from_status
                $originalFromStatus = DB::table('statuses')->find($rejectWorkflow->from_status_id);
                if (!$originalFromStatus) continue;
                
                $kamFromStatus = DB::table('statuses')
                    ->where('status_name', $originalFromStatus->status_name . ' kam')
                    ->first();
                
                if (!$kamFromStatus) {
                    $this->command->warn("KAM from status not found for: {$originalFromStatus->status_name}");
                    continue;
                }
                
                // Get KAM version of previous_status if exists
                $kamPreviousStatusId = null;
                if ($rejectWorkflow->previous_status_id) {
                    $originalPreviousStatus = DB::table('statuses')->find($rejectWorkflow->previous_status_id);
                    if ($originalPreviousStatus) {
                        $kamPreviousStatus = DB::table('statuses')
                            ->where('status_name', $originalPreviousStatus->status_name . ' kam')
                            ->first();
                        $kamPreviousStatusId = $kamPreviousStatus ? $kamPreviousStatus->id : null;
                    }
                }
                
                // Create workflow for Reject kam
                $this->createKamWorkflow(
                    $rejectWorkflow, 
                    $kamFromStatus->id, 
                    $kamPreviousStatusId, 
                    $rejectKamStatusId, 
                    $kamWorkflowTypeId,
                    'Reject kam',
                    $rejectWorkflow->default_to_status
                );
                
                // Create workflow for Cancel kam
                $this->createKamWorkflow(
                    $rejectWorkflow, 
                    $kamFromStatus->id, 
                    $kamPreviousStatusId, 
                    $cancelKamStatusId, 
                    $kamWorkflowTypeId,
                    'Cancel kam',
                    $rejectWorkflow->default_to_status
                );
                
                $createdWorkflowsCount += 2;
            }
            
            // ========================================
            // STEP 7: Add statuses to relevant groups
            // ========================================
            $this->command->info("Step 7: Adding new statuses to KAM groups...");
            
            $kamGroups = DB::table('groups')
                ->where('title', 'LIKE', '% kam')
                ->get();
            
            $groupStatusCount = 0;
            
            foreach ($kamGroups as $group) {
                // Check if group has the original reject status
                $hasRejectStatus = DB::table('group_statuses')
                    ->where('group_id', $group->id)
                    ->whereIn('status_id', function($query) use ($originalRejectStatus) {
                        $query->select('id')
                            ->from('statuses')
                            ->where('status_name', 'LIKE', '%Reject%');
                    })
                    ->exists();
                
                if ($hasRejectStatus) {
                    // Add Reject kam to group
                    $rejectKamExists = DB::table('group_statuses')
                        ->where('group_id', $group->id)
                        ->where('status_id', $rejectKamStatusId)
                        ->exists();
                    
                    if (!$rejectKamExists) {
                        DB::table('group_statuses')->insert([
                            'status_id' => $rejectKamStatusId,
                            'group_id' => $group->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'type' => 'to', // or whatever type is appropriate
                        ]);
                        $groupStatusCount++;
                    }
                    
                    // Add Cancel kam to group
                    $cancelKamExists = DB::table('group_statuses')
                        ->where('group_id', $group->id)
                        ->where('status_id', $cancelKamStatusId)
                        ->exists();
                    
                    if (!$cancelKamExists) {
                        DB::table('group_statuses')->insert([
                            'status_id' => $cancelKamStatusId,
                            'group_id' => $group->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'type' => 'to', // or whatever type is appropriate
                        ]);
                        $groupStatusCount++;
                    }
                }
            }
            
            $this->command->info("✅ KAM Reject and Cancel status seeding completed!");
            $this->command->table(
                ['Item', 'Details'],
                [
                    ['Reject kam Status ID', $rejectKamStatusId],
                    ['Cancel kam Status ID', $cancelKamStatusId],
                    ['Workflows Created', $createdWorkflowsCount],
                    ['Group Status Links Added', $groupStatusCount],
                ]
            );
        });
    }
    
    private function createKamWorkflow($originalWorkflow, $kamFromStatusId, $kamPreviousStatusId, $kamToStatusId, $kamWorkflowTypeId, $toStatusLabel, $isDefault)
    {
        // Check if workflow already exists
        $existingWorkflow = DB::table('new_workflow')
            ->where('from_status_id', $kamFromStatusId)
            ->where('type_id', $kamWorkflowTypeId)
            ->whereExists(function ($query) use ($kamToStatusId) {
                $query->select(DB::raw(1))
                    ->from('new_workflow_statuses')
                    ->whereColumn('new_workflow_statuses.new_workflow_id', 'new_workflow.id')
                    ->where('new_workflow_statuses.to_status_id', $kamToStatusId);
            })
            ->first();
        
        if ($existingWorkflow) {
            $this->command->info("Workflow already exists from {$kamFromStatusId} to {$kamToStatusId}");
            return;
        }
        
        // Create new workflow
        $newWorkflowId = DB::table('new_workflow')->insertGetId([
            'same_time_from' => $originalWorkflow->same_time_from,
            'previous_status_id' => $kamPreviousStatusId,
            'from_status_id' => $kamFromStatusId,
            'active' => $originalWorkflow->active,
            'same_time' => $originalWorkflow->same_time,
            'workflow_type' => $originalWorkflow->workflow_type,
            'to_status_label' => $toStatusLabel,
            'created_at' => now(),
            'updated_at' => now(),
            'type_id' => $kamWorkflowTypeId,
        ]);
        
        // Create workflow status link
        DB::table('new_workflow_statuses')->insert([
            'new_workflow_id' => $newWorkflowId,
            'to_status_id' => $kamToStatusId,
            'default_to_status' => $isDefault,
            'created_at' => now(),
            'updated_at' => now(),
            'dependency_ids' => null,
        ]);
        
        $this->command->info("Created workflow from {$kamFromStatusId} to {$kamToStatusId} (Workflow ID: {$newWorkflowId})");
    }
}