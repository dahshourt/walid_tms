<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('stages')->insert([
            'name' => 'Analysis',
            'active' => '1',
        ]);
        DB::table('stages')->insert([
            'name' => 'Estimation',
            'active' => '1',
        ]);
        DB::table('stages')->insert([
            'name' => 'Design',
            'active' => '1',
        ]);
        DB::table('stages')->insert([
            'name' => 'Development',
            'active' => '1',
        ]);
        DB::table('stages')->insert([
            'name' => 'Testing',
            'active' => '1',
        ]);
        DB::table('stages')->insert([
            'name' => 'Sanity',
            'active' => '1',
        ]);
    }
}
