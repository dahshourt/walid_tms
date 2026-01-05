<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'admin@te.eg')->first();

        if (!$user) {
            DB::table('users')->insert([
                'name' => 'admin',
                'user_name' => 'admin',
                'email' => 'admin@te.eg',
                'password' => Hash::make('password'),
                'active' => '0',
                'default_group' => 10,
            ]);
        }
    }
}
