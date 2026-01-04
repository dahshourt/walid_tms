<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddNeedUiUxCustomField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the custom field
        $fieldId = DB::table('custom_fields')->insertGetId([
            'type' => 'checkbox',
            'name' => 'need_ui_ux',
            'label' => 'Need UI/UX',
            'class' => 'form-check-input',
            'default_value' => '0',
            'active' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get the workflow type ID for normal change requests (assuming type 1 is normal)
        $workflowTypeId = DB::table('workflow_type')
            ->where('name', 'Normal')
            ->value('id') ?? 1;

        // Get the maximum sort value for the form type to place it at the end
        $maxSort = DB::table('custom_fields_groups_type')
            ->where('form_type', 1) // 1 = create ticket form
            ->max('sort') ?? 0;

        // Associate the field with the change request form
        DB::table('custom_fields_groups_type')->insert([
            'form_type' => 1, // 1 = create ticket form
            'wf_type_id' => $workflowTypeId,
            'custom_field_id' => $fieldId,
            'sort' => $maxSort + 1,
            'active' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Find and delete the field association
        $field = DB::table('custom_fields')
            ->where('name', 'need_ui_ux')
            ->first();

        if ($field) {
            // Remove from groups type table
            DB::table('custom_fields_groups_type')
                ->where('custom_field_id', $field->id)
                ->delete();

            // Remove the field
            DB::table('custom_fields')
                ->where('id', $field->id)
                ->delete();
        }
    }
}
