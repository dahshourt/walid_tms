<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Group;

class AddUiUxMemberCustomField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, check if the ux.it group exists
        $uxItGroup = DB::table('groups')->where('title', 'ux.it')->first();
        
        if (!$uxItGroup) {
            // Create the ux.it group if it doesn't exist
            $uxItGroupId = DB::table('groups')->insertGetId([
                'title' => 'ux.it',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $uxItGroup = (object)['id' => $uxItGroupId];
        }

        // Check if the custom field already exists
        $existingField = DB::table('custom_fields')->where('name', 'ui_ux_member')->first();
        
        if ($existingField) {
            // Update existing field if it already exists
            DB::table('custom_fields')
                ->where('id', $existingField->id)
                ->update([
                    'type' => 'select',
                    'label' => 'UI/UX Member',
                    'class' => 'form-control select2',
                    'related_table' => 'user_groups',
                    'related_condition' => json_encode([
                        'group_id' => $uxItGroup->id,
                        'join' => [
                            'table' => 'users',
                            'first' => 'user_groups.user_id',
                            'operator' => '=',
                            'second' => 'users.id'
                        ]
                    ]),
                    'active' => '1',
                    'updated_at' => now(),
                ]);
            $fieldId = $existingField->id;
        } else {
            // Insert new UI/UX Member custom field
            $fieldId = DB::table('custom_fields')->insertGetId([
                'type' => 'select',
                'name' => 'ui_ux_member',
                'label' => 'UI/UX Member',
                'class' => 'form-control select2',
                'default_value' => null,
                'related_table' => 'user_groups',
                'related_condition' => json_encode([
                    'group_id' => $uxItGroup->id,
                    'join' => [
                        'table' => 'users',
                        'first' => 'user_groups.user_id',
                        'operator' => '=',
                        'second' => 'users.id'
                    ]
                ]),
                'active' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Remove any existing associations to prevent duplicates
        DB::table('custom_fields_groups_type')
            ->where('custom_field_id', $fieldId)
            ->delete();
            
        // Associate the field with the ux.it group for all form types (create, update, search)
        foreach (['1', '2', '3'] as $formType) {
            DB::table('custom_fields_groups_type')->insert([
                'form_type' => $formType,
                'group_id' => $uxItGroup->id,
                'wf_type_id' => null,
                'custom_field_id' => $fieldId,
                'sort' => 100, // Adjust the sort order as needed
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
        // Find and remove the custom field and its associations
        $field = DB::table('custom_fields')->where('name', 'ui_ux_member')->first();
        
        if ($field) {
            // Remove from custom_fields_groups_type
            DB::table('custom_fields_groups_type')
                ->where('custom_field_id', $field->id)
                ->delete();
                
            // Remove from custom_fields
            DB::table('custom_fields')
                ->where('id', $field->id)
                ->delete();
        }
    }
}
