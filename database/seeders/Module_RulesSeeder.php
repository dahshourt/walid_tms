<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class Module_RulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_rules')->insert([
            'module_id' => '1',
            'rule_name' => 'List Users',
            'rule_slug' => 'list-users',
            'action_url' => '/user/list-users',
            'active' => '1',
        ]);

        DB::table('module_rules')->insert([
            'module_id' => '1',
            'rule_name' => 'Add User',
            'rule_slug' => 'add-user',
            'action_url' => '/user/form',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '2',
            'rule_name' => 'List Groups',
            'rule_slug' => 'list-groups',
            'action_url' => '/group/list-groups',
            'active' => '1',
        ]);

        DB::table('module_rules')->insert([
            'module_id' => '2',
            'rule_name' => 'Add Group',
            'rule_slug' => 'add-group',
            'action_url' => '/group/form',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '3',
            'rule_name' => 'List Roles',
            'rule_slug' => 'list-roles',
            'action_url' => '/role/list-roles',
            'active' => '1',
        ]);

        DB::table('module_rules')->insert([
            'module_id' => '3',
            'rule_name' => 'Add Role',
            'rule_slug' => 'add-role',
            'action_url' => '/role/form',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '4',
            'rule_name' => 'List Stages',
            'rule_slug' => 'list-stages',
            'action_url' => '/stage/list-stages',
            'active' => '1',
        ]);

        DB::table('module_rules')->insert([
            'module_id' => '4',
            'rule_name' => 'Add Stage',
            'rule_slug' => 'add-stage',
            'action_url' => '/stage/form',
            'active' => '1',
        ]);

        DB::table('module_rules')->insert([
            'module_id' => '5',
            'rule_name' => 'List Statusess',
            'rule_slug' => 'list-Statusess',
            'action_url' => '/status/list-statuses',
            'active' => '1',
        ]);

        DB::table('module_rules')->insert([
            'module_id' => '5',
            'rule_name' => 'Add Status',
            'rule_slug' => 'add-status',
            'action_url' => '/status/form',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '7',
            'rule_name' => 'List Change Requestes',
            'rule_slug' => 'list-change-request',
            'action_url' => '/change-request/list-change-requestes',
            'active' => '1',
        ]);

        DB::table('module_rules')->insert([
            'module_id' => '7',
            'rule_name' => 'Add Change Requestes',
            'rule_slug' => 'add-change-request',
            'action_url' => '/change-request/form',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '6',
            'rule_name' => 'Status Workflows',
            'rule_slug' => 'list-workflow',
            'action_url' => '/StatusWorkflow/list_status_workflow',
            'active' => '1',
        ]);

        DB::table('module_rules')->insert([
            'module_id' => '6',
            'rule_name' => 'Configure Status Workflow',
            'rule_slug' => 'StatusWorkflow-status_workflow',
            'action_url' => '/StatusWorkflow/status_workflow',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '8',
            'rule_name' => 'Configure Permission',
            'rule_slug' => 'Configure Permission',
            'action_url' => '/permission/config',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '9',
            'rule_name' => 'list division managers',
            'rule_slug' => 'list_division_managers',
            'action_url' => '/division_managers/list',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '9',
            'rule_name' => 'form division managers',
            'rule_slug' => 'form_division_managers',
            'action_url' => '/division_managers/form',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '10',
            'rule_name' => 'add high level statuses',
            'rule_slug' => 'add_high_level_statuses',
            'action_url' => '/high_level_statuses/add',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '10',
            'rule_name' => 'list high level statuses',
            'rule_slug' => 'list_high_level_statuses',
            'action_url' => '/high_level_statuses/list',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '11',
            'rule_name' => 'My Assignment',
            'rule_slug' => 'My Assignment',
            'action_url' => '/change-request/my-assignments',
            'active' => '1',
        ]);

        DB::table('module_rules')->insert([
            'module_id' => '12',
            'rule_name' => 'add system',
            'rule_slug' => 'add_system',
            'action_url' => '/system/add',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '12',
            'rule_name' => 'list system',
            'rule_slug' => 'list_system',
            'action_url' => '/system/list',
            'active' => '1',
        ]);

        DB::table('module_rules')->insert([
            'module_id' => '13',
            'rule_name' => 'make parent crs',
            'rule_slug' => 'make_parent_crs',
            'action_url' => '/parent/add',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '13',
            'rule_name' => 'list parent crs',
            'rule_slug' => 'list_parent_crs',
            'action_url' => '/parent/list',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '14',
            'rule_name' => 'add Rejection Reasons',
            'rule_slug' => 'add_Rejection_Reasons',
            'action_url' => '/RejectionReasons/add',
            'active' => '1',
        ]);
        DB::table('module_rules')->insert([
            'module_id' => '14',
            'rule_name' => 'list Rejection Reasons',
            'rule_slug' => 'list_Rejection_Reasons',
            'action_url' => '/RejectionReasons/list',
            'active' => '1',
        ]);

    }
}
