<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateUserSeeder extends Seeder
{
    public function run()
    {
        DB::table('cab_crs')
            ->where('cr_id', 31111)
            ->update([
                'status' => '0'
            ]);
			
			DB::table('cab_cr_users')
            ->where('id', 13)
            ->update([
                'status' => '0'
            ]);
    }
}
