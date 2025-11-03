<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateUserSeeder extends Seeder
{
    public function run()
    {
        DB::table('change_request_statuses')
            ->where('cr_id', 31136)
            ->where('new_status_id', 109)
            ->update([
                'active' => '1',
            ]);
    }
}
