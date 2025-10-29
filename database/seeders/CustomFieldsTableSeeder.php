<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomFieldsTableSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks to avoid constraint issues
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Insert new custom fields
        $fields = [
            [
                'id' => 98,
                'type' => 'date',
                'name' => 'Deployment Date',
                'label' => 'deployment_date',
                'class' => 'date',
                'active' => '1',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 99,
                'type' => 'checkbox',
                'name' => 'Smoke Test Done',
                'label' => 'smoke_test_done',
                'class' => 'checkbox',
                'active' => '1',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 100,
                'type' => 'radio',
                'name' => 'test_case_approved',
                'label' => 'Test Case Approved',
                'class' => 'radio',
                'active' => '1',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 101,
                'type' => 'input',
                'name' => 'sdd_estimate',
                'label' => 'SDD Estimate "Duration"',
                'class' => 'text',
                'active' => '1',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 102,
                'type' => 'select',
                'name' => 'sub_application_id',
                'label' => 'Sub Target System',
                'class' => 'dropdown',
                'default_value' => null,
                'related_table' => 'applications',
                'active' => '1',
                'created_at' => null,
                'updated_at' => null,
            ],
        ];

        // Insert or update the fields
        foreach ($fields as $field) {
            DB::table('custom_fields')->updateOrInsert(
                ['id' => $field['id']],
                $field
            );
        }

        // Update existing fields' types to 'date'
        $dateFields = [
            'start_date_mds',
            'end_date_mds',
            'ready_for_uat',
            'proposed_available_time',
            'kick_off_meeting_date',
            'accumulative_mds',
            'Deployment Date',
        ];

        DB::table('custom_fields')
            ->whereIn('name', $dateFields)
            ->update(['type' => 'date']);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
