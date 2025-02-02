<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('statuses')->insert([
            'status_name' => "Pending CAB",
            'stage_id' => 1,
            'active' => '1',
        ]);
        DB::table('statuses')->insert([
            'status_name' => "Pending Analysis",
            'stage_id' => 1,
            'active' => '1',
        ]);
        DB::table('statuses')->insert([
            'status_name' => "Design estimation",
            'stage_id' => 2,
            'active' => '1',
        ]);
        DB::table('statuses')->insert([
            'status_name' => "Technical estimation",
            'stage_id' => 2,
            'active' => '1',
        ]);
        DB::table('statuses')->insert([
            'status_name' => "Analysis Feedback",
            'stage_id' => 1,
            'active' => '1',
        ]);
        DB::table('statuses')->insert([
            'status_name' => "Testing Estimation",
            'stage_id' => 2,
            'active' => '1',
        ]);
        DB::table('statuses')->insert([
            'status_name' => "Pending Design",
            'stage_id' => 3,
            'active' => '1',
        ]);
        DB::table('statuses')->insert([
            'status_name' => "Pending implementation",
            'stage_id' => 4,
            'active' => '1',
        ]);
        DB::table('statuses')->insert([
            'status_name' => "Design feedback",
            'stage_id' => 3,
            'active' => '1',
        ]);
        DB::table('statuses')->insert([
            'status_name' => "Technical Implementation",
            'stage_id' => 4,
            'active' => '1',
        ]);
        DB::table('statuses')->insert([
            'status_name' => "Pending Testing",
            'stage_id' => 5,
            'active' => '1',
        ]);
        DB::table('statuses')->insert([
            'status_name' => "Test Case approval",
            'stage_id' => 5,
            'active' => '1',
        ]);
        DB::table('statuses')->insert([
            'status_name' => "Testing in progress",
            'stage_id' => 5,
            'active' => '1',
        ]);
        DB::table('statuses')->insert([
            'status_name' => "Pending rework",
            'stage_id' => 4,
            'active' => '1',
        ]);
        
    }
}
