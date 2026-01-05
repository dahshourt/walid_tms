<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesSeeder extends Seeder
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
                'status_name' => 'Pending MDs Sign off',
                'stage_id' => 3,
                'active' => 1,
                'type' => 1,
                'created_at' => '2025-12-31 20:11:22',
                'updated_at' => '2025-12-31 20:11:22',
                'high_level_status_id' => null,
                'sla' => 4,
                'defect' => 0,
                'view_technical_team_flag' => 0,
                'workflow_type_id' => 5,
            ],
            [
                'status_name' => 'Reject and Re-validation CR',
                'stage_id' => 3,
                'active' => 1,
                'type' => 1,
                'created_at' => '2026-01-01 11:10:52',
                'updated_at' => '2026-01-01 11:10:52',
                'high_level_status_id' => null,
                'sla' => 4,
                'defect' => 0,
                'view_technical_team_flag' => 0,
                'workflow_type_id' => 5,
            ],
        ];

        DB::table('statuses')->insert($data);
    }
}
