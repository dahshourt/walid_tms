<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class GroupStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('group_statuses')->insert([
            'status_id' => 1,
            'group_id' => 8,
            'type' => '2',
        ]);
        DB::table('group_statuses')->insert([
            'status_id' => 2,
            'group_id' => 8,
            'type' => '2',
        ]);
        DB::table('group_statuses')->insert([
            'status_id' => 3,
            'group_id' => 8,
            'type' => '1',
        ]);
        DB::table('group_statuses')->insert([
            'status_id' => 3,
            'group_id' => 9,
            'type' => '2',
        ]);
        DB::table('group_statuses')->insert([
            'status_id' => 4,
            'group_id' => 8,
            'type' => '1',
        ]);
        DB::table('group_statuses')->insert([
            'status_id' => 4,
            'group_id' => 10,
            'type' => '2',
        ]);

        DB::table('group_statuses')->insert([
            'status_id' => 5,
            'group_id' => 8,
            'type' => '2',
        ]);
        DB::table('group_statuses')->insert([
            'status_id' => 5,
            'group_id' => 9,
            'type' => '1',
        ]);
        DB::table('group_statuses')->insert([
            'status_id' => 5,
            'group_id' => 10,
            'type' => '1',
        ]);
        DB::table('group_statuses')->insert([
            'status_id' => 5,
            'group_id' => 11,
            'type' => '1',
        ]);

        DB::table('group_statuses')->insert([
            'status_id' => 6,
            'group_id' => 8,
            'type' => '1',
        ]);
        DB::table('group_statuses')->insert([
            'status_id' => 6,
            'group_id' => 11,
            'type' => '2',
        ]);

        DB::table('group_statuses')->insert([
            'status_id' => 7,
            'group_id' => 11,
            'type' => '1',
        ]);
        DB::table('group_statuses')->insert([
            'status_id' => 7,
            'group_id' => 9,
            'type' => '2',
        ]);

    }
}
