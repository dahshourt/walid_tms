<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('roles')->insert([

            'name' => 'User',
        ]);
        DB::table('roles')->insert([
            'name' => 'Developer',
        ]);
        DB::table('roles')->insert([
            'name' => 'Tester',
        ]);
        DB::table('roles')->insert([
            'name' => 'Designer',
        ]);
        DB::table('roles')->insert([
            'name' => 'SuperVisor',
        ]);
        DB::table('roles')->insert([
            'name' => 'Division Manager',
        ]);
        DB::table('roles')->insert([
            'name' => 'Manager',
        ]);
        DB::table('roles')->insert([
            'name' => 'Administrator',
        ]);

    }
}
