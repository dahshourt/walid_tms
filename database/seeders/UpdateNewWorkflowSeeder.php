<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateNewWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $updates = [
            [
                'id' => 855,
                'same_time_from' => '0',
                'previous_status_id' => null,
                'from_status_id' => 251,
                'active' => '0',
                'same_time' => '1',
                'workflow_type' => '0',
                'to_status_label' => 'Prototype Approval',
                'created_at' => '2025-12-22 12:01:03',
                'updated_at' => '2025-12-31 22:10:28',
                'type_id' => 5,
            ],
            [
                'id' => 856,
                'same_time_from' => '0',
                'previous_status_id' => null,
                'from_status_id' => 248,
                'active' => '0',
                'same_time' => '0',
                'workflow_type' => '0',
                'to_status_label' => 'Prototype Approval',
                'created_at' => '2025-12-22 13:23:51',
                'updated_at' => '2025-12-31 21:59:59',
                'type_id' => 5,
            ],
            [
                'id' => 857,
                'same_time_from' => '0',
                'previous_status_id' => null,
                'from_status_id' => 248,
                'active' => '0',
                'same_time' => '0',
                'workflow_type' => '0',
                'to_status_label' => 'Need Update',
                'created_at' => '2025-12-22 13:24:32',
                'updated_at' => '2025-12-31 22:00:57',
                'type_id' => 5,
            ],
            [
                'id' => 858,
                'same_time_from' => '0',
                'previous_status_id' => null,
                'from_status_id' => 249,
                'active' => '0',
                'same_time' => '0',
                'workflow_type' => '0',
                'to_status_label' => 'Approved',
                'created_at' => '2025-12-22 13:25:12',
                'updated_at' => '2025-12-31 22:13:01',
                'type_id' => 5,
            ],
            [
                'id' => 859,
                'same_time_from' => '0',
                'previous_status_id' => null,
                'from_status_id' => 249,
                'active' => '0',
                'same_time' => '0',
                'workflow_type' => '0',
                'to_status_label' => 'Need Update',
                'created_at' => '2025-12-22 13:25:43',
                'updated_at' => '2025-12-31 22:11:55',
                'type_id' => 5,
            ],
            [
                'id' => 868,
                'same_time_from' => '0',
                'previous_status_id' => 255,
                'from_status_id' => 254,
                'active' => '1',
                'same_time' => '1',
                'workflow_type' => '0',
                'to_status_label' => 'CR Doc Approved',
                'created_at' => '2025-12-22 14:23:36',
                'updated_at' => '2026-01-01 10:34:49',
                'type_id' => 5,
            ],
            [
                'id' => 871,
                'same_time_from' => '0',
                'previous_status_id' => 258,
                'from_status_id' => 259,
                'active' => '0',
                'same_time' => '0',
                'workflow_type' => '0',
                'to_status_label' => null,
                'created_at' => '2025-12-22 14:27:50',
                'updated_at' => '2026-01-01 10:34:48',
                'type_id' => 5,
            ],
            [
                'id' => 872,
                'same_time_from' => '0',
                'previous_status_id' => 260,
                'from_status_id' => 258,
                'active' => '1',
                'same_time' => '0',
                'workflow_type' => '0',
                'to_status_label' => null,
                'created_at' => '2025-12-22 14:29:41',
                'updated_at' => '2026-01-01 10:54:57',
                'type_id' => 5,
            ],
            [
                'id' => 873,
                'same_time_from' => '0',
                'previous_status_id' => 258,
                'from_status_id' => 259,
                'active' => '1',
                'same_time' => '1',
                'workflow_type' => '0',
                'to_status_label' => 'approved from cr team',
                'created_at' => '2025-12-22 14:31:05',
                'updated_at' => '2026-01-01 10:35:54',
                'type_id' => 5,
            ],
            [
                'id' => 878,
                'same_time_from' => '0',
                'previous_status_id' => 261,
                'from_status_id' => 263,
                'active' => '1',
                'same_time' => '1',
                'workflow_type' => '0',
                'to_status_label' => 'approved from cr team',
                'created_at' => '2025-12-22 14:41:55',
                'updated_at' => '2025-12-31 20:12:41',
                'type_id' => 5,
            ],
            [
                'id' => 879,
                'same_time_from' => '0',
                'previous_status_id' => 261,
                'from_status_id' => 263,
                'active' => '1',
                'same_time' => '0',
                'workflow_type' => '0',
                'to_status_label' => null,
                'created_at' => '2025-12-22 14:43:26',
                'updated_at' => '2025-12-31 20:14:31',
                'type_id' => 5,
            ],
        ];

        foreach ($updates as $update) {
            $id = $update['id'];
            unset($update['id']); // Remove id from the data array
            
            DB::table('new_workflow')
                ->where('id', $id)
                ->update($update);
        }
    }
}