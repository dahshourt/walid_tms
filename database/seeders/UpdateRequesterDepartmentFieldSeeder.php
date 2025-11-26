<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRequesterDepartmentFieldSeeder extends Seeder
{
    public function run()
    {
        DB::table('custom_fields')->updateOrInsert(
            // Condition to find the record
            ['name' => 'requester_department'], 

            // Data to update/insert
            [
                'type'          => 'select',
                'label'         => 'Requester department',
                'class'         => 'dropdown',
                'default_value' => null,
                'related_table' => 'requester_departments',
                'active'        => '1',
                'updated_at'    => now(),
                'created_at'    => now(),
            ]
        );
    }
}
