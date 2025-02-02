<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => "Mahmoud Bastawisy",
            'user_name' => "mahmoud.bastawisy",
            'email' => "mahmoud.bastawisy@te.eg",
            'password' => Hash::make('password'),
            'active' => '1',
            'default_group' => 10,
        ]);
        DB::table('users')->insert([
            'name' => "Sara Mostafa",
            'user_name' => "sara.mostafa",
            'email' => "sara.mostafa@te.eg",
            'password' => Hash::make('password'),
            'active' => '1',
            'default_group' => 10,
        ]);
        DB::table('users')->insert([
            'name' => "Fayrouz yousef",
            'user_name' => "fayrouz.yousef",
            'email' => "fayrouz.yousef@te.eg",
            'password' => Hash::make('password'),
            'active' => '1',
            'default_group' => 10,
        ]);

        DB::table('users')->insert([
            'name' => "Walid Dahshour",
            'user_name' => "walid.dahshour",
            'email' => "walid.dahshour@te.eg",
            'password' => Hash::make('password'),
            'active' => '1',
            'default_group' => 10,
        ]);

        DB::table('users')->insert([
            'name' => "Tarek Adel",
            'user_name' => "tarek.adel",
            'email' => "tarek.adel@te.eg",
            'password' => Hash::make('password'),
            'active' => '1',
            'default_group' => 10,
        ]);
    }
}
