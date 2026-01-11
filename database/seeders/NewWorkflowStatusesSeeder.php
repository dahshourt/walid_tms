<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewWorkflowStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'new_workflow_id' => 878,
                'to_status_id' => 267,
                'default_to_status' => '1',
                'created_at' => '2025-12-31 20:12:42',
                'updated_at' => '2025-12-31 20:12:42',
                'dependency_ids' => null,
            ],
            [
                'new_workflow_id' => 879,
                'to_status_id' => 260,
                'default_to_status' => '0',
                'created_at' => '2025-12-31 20:14:31',
                'updated_at' => '2025-12-31 20:14:31',
                'dependency_ids' => null,
            ],
        ];

        // Insert the data
        DB::table('new_workflow_statuses')->insert($data);
    }
}