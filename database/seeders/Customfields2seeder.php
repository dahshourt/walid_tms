<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Customfields2seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customFields = [
            [
                'type' => 'select',
                'name' => 'cr_type',
                'label' => 'CR Type',
                'class' => 'dropdown',
                'default_value' => null,
                'related_table' => 'cr_types',
                'active' => '1',
            ],
            [
                'type' => 'checkbox',
                'name' => 'need_ui_ux',
                'label' => 'Need UI/UX',
                'class' => 'form-check-input',
                'default_value' => '0',
                'related_table' => null,
                'active' => '1',
            ],
            [
                'type' => 'select',
                'name' => 'ui_ux_member',
                'label' => 'UI/UX Member',
                'class' => 'form-control select2',
                'default_value' => null,
                'related_table' => 'user',
                'active' => '1',
            ],
            [
                'type' => 'checkbox',
                'name' => 'need_bi',
                'label' => 'Need BI',
                'class' => 'form-check-input',
                'default_value' => '0',
                'related_table' => null,
                'active' => '1',
            ],
            [
                'type' => 'input',
                'name' => 'ui_ux_estimation_duration',
                'label' => 'UI/UX Estimation Duration (Hours)',
                'class' => 'form-control',
                'default_value' => '0',
                'related_table' => null,
                'active' => '1',
            ],
        ];

        foreach ($customFields as $field) {
            // Check if record exists by unique 'name' field
            $exists = DB::table('custom_fields')
                ->where('name', $field['name'])
                ->exists();

            if (!$exists) {
                DB::table('custom_fields')->insert(array_merge($field, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]));
            }
        }
    }
}
