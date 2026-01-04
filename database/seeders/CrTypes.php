<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cr_types')->insert([
            'name' => 'Normal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('cr_types')->insert([
            'name' => 'Depend On',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('cr_types')->insert([
            'name' => 'Relevant',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
