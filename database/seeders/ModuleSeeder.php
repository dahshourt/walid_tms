<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert([
            'name' => 'Users',
            'slug' => 'user',
            'description' => 'userdesc',
            'active' => '1',
        ]);
        DB::table('modules')->insert([
            'name' => 'Groups',
            'slug' => 'groups',
            'description' => 'groupdesc',
            'active' => '1',
        ]);
        DB::table('modules')->insert([
            'name' => 'Roles',
            'slug' => 'role',
            'description' => 'roledesc',
            'active' => '1',
        ]);
        DB::table('modules')->insert([
            'name' => 'Stages',
            'slug' => 'stage',
            'description' => 'stagedesc',
            'active' => '1',
        ]);
        DB::table('modules')->insert([
            'name' => 'Statuses',
            'slug' => 'status',
            'description' => 'statusdesc',
            'active' => '1',
        ]);
        DB::table('modules')->insert([
            'name' => 'Workflow',
            'slug' => 'workflow',
            'description' => 'workflowdesc',
            'active' => '1',
        ]);
        DB::table('modules')->insert([
            'name' => 'Change Request',
            'slug' => 'change_request',
            'description' => 'requestdesc',
            'active' => '1',
        ]);
        DB::table('modules')->insert([
            'name' => 'Permissions',
            'slug' => 'Permissions',
            'description' => 'Permissionsdesc',
            'active' => '1',
        ]);
        DB::table('modules')->insert([
            'name' => 'division managers',
            'slug' => 'division_managers',
            'description' => '',
            'active' => '1',
        ]);
        DB::table('modules')->insert([
            'name' => 'high level statuses',
            'slug' => 'high_level_statuses',
            'description' => '',
            'active' => '1',
        ]);
        DB::table('modules')->insert([
            'name' => 'My Assignments',
            'slug' => 'My Assignments',
            'description' => '',
            'active' => '1',
        ]);

        DB::table('modules')->insert([
            'name' => 'system',
            'slug' => 'system',
            'description' => '',
            'active' => '1',
        ]);
        DB::table('modules')->insert([//RejectionReasons
            'name' => 'parent crs',
            'slug' => 'parent_crs',
            'description' => '',
            'active' => '1',
        ]);

        DB::table('modules')->insert([//RejectionReasons
            'name' => 'Rejection Reasons',
            'slug' => 'RejectionReasons',
            'description' => '',
            'active' => '1',
        ]);
    }
}
