<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RelevantMultiselectFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if the field already exists
        $exists = DB::table('custom_fields')
            ->where('name', 'relevant')
            ->exists();

        if (!$exists) {
            // Insert the custom field
            $fieldId = DB::table('custom_fields')->insertGetId([
                'type' => 'multiselect',
                'name' => 'relevant',
                'label' => 'Relevant CRs',
                'class' => 'form-control form-control-lg select2',
                'default_value' => null,
                'related_table' => 'change_request',
                'active' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add the field to the custom_fields_groups_type table
            $formTypes = [1, 2, 3]; // 1 = create, 2 = update, 3 = search
            
            foreach ($formTypes as $formType) {
                // Get the maximum sort value for this form type
                $maxSort = DB::table('custom_fields_groups_type')
                    ->where('form_type', $formType)
                    ->max('sort');

                DB::table('custom_fields_groups_type')->insert([
                    'form_type' => $formType,
                    'group_id' => null, // Available for all groups
                    'wf_type_id' => null, // Available for all workflow types
                    'custom_field_id' => $fieldId,
                    'sort' => $maxSort ? $maxSort + 1 : 1,
                    'active' => '1',
                    'enable' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->command->info('Relevant custom field created successfully!');
        } else {
            $this->command->warn('Relevant custom field already exists.');
        }
    }
}