<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class UserGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('user_groups')->insert([
            'user_id' => 1,
            'group_id' => 9,
        ]);
        DB::table('user_groups')->insert([
            'user_id' => 1,
            'group_id' => 1,
        ]);
        DB::table('user_groups')->insert([
            'user_id' => 2,
            'group_id' => 9,
        ]);
        DB::table('user_groups')->insert([
            'user_id' => 2,
            'group_id' => 10,
        ]);
        DB::table('user_groups')->insert([
            'user_id' => 3,
            'group_id' => 9,
        ]);
        DB::table('user_groups')->insert([
            'user_id' => 3,
            'group_id' => 10,
        ]);
        DB::table('user_groups')->insert([
            'user_id' => 4,
            'group_id' => 9,
        ]);
        DB::table('user_groups')->insert([
            'user_id' => 4,
            'group_id' => 10,
        ]);
        DB::table('user_groups')->insert([
            'user_id' => 5,
            'group_id' => 9,
        ]);
        DB::table('user_groups')->insert([
            'user_id' => 5,
            'group_id' => 10,
        ]);

    }
}
