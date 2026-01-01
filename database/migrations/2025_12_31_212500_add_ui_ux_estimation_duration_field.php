<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUiUxEstimationDurationField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert the new custom field
        $fieldId = DB::table('custom_fields')->insertGetId([
            'type' => 'number',
            'name' => 'ui_ux_estimation_duration',
            'label' => 'UI/UX Estimation Duration (Hours)',
            'class' => 'form-control',
            'default_value' => '0',
            'related_table' => null,
            'active' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get the group ID for the technical team (you may need to adjust this based on your actual group structure)
        $group = DB::table('groups')->where('name', 'like', '%technical%')->first();
        $groupId = $group ? $group->id : null;

        // Get the workflow type ID for change requests
        $workflowType = DB::table('workflow_type')->where('name', 'like', '%change%request%')->first();
        $workflowTypeId = $workflowType ? $workflowType->id : null;

        // Insert the field into the custom_fields_groups_type table to make it visible in forms
        if ($groupId && $workflowTypeId) {
            DB::table('custom_fields_groups_type')->insert([
                'form_type' => '1', // 1 = create ticket form
                'group_id' => $groupId,
                'wf_type_id' => $workflowTypeId,
                'custom_field_id' => $fieldId,
                'sort' => 999, // Adjust sort order as needed
                'active' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Also add to update form
            DB::table('custom_fields_groups_type')->insert([
                'form_type' => '2', // 2 = update ticket form
                'group_id' => $groupId,
                'wf_type_id' => $workflowTypeId,
                'custom_field_id' => $fieldId,
                'sort' => 999, // Adjust sort order as needed
                'active' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Find and delete the custom field and its associations
        $field = DB::table('custom_fields')
            ->where('name', 'ui_ux_estimation_duration')
            ->first();

        if ($field) {
            // Delete from custom_fields_groups_type
            DB::table('custom_fields_groups_type')
                ->where('custom_field_id', $field->id)
                ->delete();

            // Delete the custom field
            DB::table('custom_fields')
                ->where('id', $field->id)
                ->delete();
        }
    }
}
