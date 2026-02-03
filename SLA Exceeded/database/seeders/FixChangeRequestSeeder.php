<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixChangeRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {

            // ===============================
            // Update change_request dates
            // ===============================
            DB::table('change_request')
                ->where('id', 31056)
                ->update([
                    'start_develop_time' => '2025-10-27 14:55:35',
                    'end_develop_time'   => '2025-11-03 14:55:35',
                    'start_test_time'    => '2025-11-03 15:55:35',
                    'end_test_time'      => '2025-11-05 15:55:35',
                ]);

            // ===============================
            // Update change_request_statuses
            // ===============================
            DB::table('change_request_statuses')
                ->where('id', 971)
                ->update([
                    'created_at' => '2025-10-27 14:57:20',
                    'updated_at' => '2025-11-03 12:07:55',
                ]);

            DB::table('change_request_statuses')
                ->where('id', 1022)
                ->update([
                    'created_at' => '2025-10-27 12:07:55',
                    'updated_at' => '2025-11-03 14:33:00',
                ]);

            DB::table('change_request_statuses')
                ->where('id', 1048)
                ->update([
                    'created_at' => '2025-11-03 14:33:00',
                ]);

        });
    }
}
