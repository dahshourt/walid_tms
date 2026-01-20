<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class AddTopManagementPermissionsOnly extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add all Top Management CRS permissions using Spatie Permission system
        // Create parent permission for Top Managers only
        
        // Create parent permission for Top Managers
        $topManagersParent = Permission::firstOrCreate([
            'name' => 'Top Managers',
            'guard_name' => 'web',
        ], [
            'module' => 'Top Managers',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create child permissions for Top Managers
        $topManagersChildren = [
            'Edit Top Management Form',
            'Access Top Management CRS',
            'Update Top Management Flag',
            'List Top Management CRS',
            'Create Top Management CRS',
            'Delete Top Management CRS'
        ];

        foreach ($topManagersChildren as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ], [
                'module' => 'Top Managers',
                'parent_id' => $topManagersParent->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Add module rules for URL-based permissions
        $moduleRules = [
            [
                'module_id' => '7', // Change Request module
                'rule_name' => 'Edit Top Management Form',
                'rule_slug' => 'edit-top-management-form',
                'action_url' => '/top_management_crs/form',
                'active' => '1',
            ],
            [
                'module_id' => '7', // Change Request module
                'rule_name' => 'Access Top Management CRS',
                'rule_slug' => 'access-top-management-crs',
                'action_url' => '/top_management_crs',
                'active' => '1',
            ],
            [
                'module_id' => '7', // Change Request module
                'rule_name' => 'Update Top Management Flag',
                'rule_slug' => 'update-top-management-flag',
                'action_url' => '/update_top_management',
                'active' => '1',
            ],
            [
                'module_id' => '7', // Change Request module
                'rule_name' => 'List Top Management CRS',
                'rule_slug' => 'list-top-management-crs',
                'action_url' => '/top_management_crs/list',
                'active' => '1',
            ],
            [
                'module_id' => '7', // Change Request module
                'rule_name' => 'Create Top Management CRS',
                'rule_slug' => 'create-top-management-crs',
                'action_url' => '/top_management_crs/create',
                'active' => '1',
            ],
            [
                'module_id' => '7', // Change Request module
                'rule_name' => 'Delete Top Management CRS',
                'rule_slug' => 'delete-top-management-crs',
                'action_url' => '/top_management_crs/delete',
                'active' => '1',
            ],
        ];

        // Insert module rules only if they don't exist
        foreach ($moduleRules as $rule) {
            DB::table('module_rules')->updateOrInsert(
                ['rule_slug' => $rule['rule_slug']],
                array_merge($rule, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Define all permission names to delete (only Top Managers parent and its children)
        $permissionNames = [
            'Top Managers',
            'Edit Top Management Form',
            'Access Top Management CRS',
            'Update Top Management Flag',
            'List Top Management CRS',
            'Create Top Management CRS',
            'Delete Top Management CRS'
        ];

        // Delete permissions from Spatie permissions table
        foreach ($permissionNames as $permissionName) {
            Permission::where('name', $permissionName)->delete();
        }

        // Define all permission slugs to delete from module_rules
        $permissionSlugs = [
            'edit-top-management-form',
            'access-top-management-crs',
            'update-top-management-flag',
            'list-top-management-crs',
            'create-top-management-crs',
            'delete-top-management-crs'
        ];

        // Delete the module rules
        DB::table('module_rules')
            ->whereIn('rule_slug', $permissionSlugs)
            ->delete();
    }
}
