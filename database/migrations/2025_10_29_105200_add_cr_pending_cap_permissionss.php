<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Always clear the cached permissions and roles
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define parent permission
        $parent = Permission::firstOrCreate(['name' => 'cr pending cap']);

        // Define child permissions
        $permissions = [
            'Edit cr pending cap',
            'Show cr pending cap',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ], [
                'parent_id' => $parent->id, // 👈 assign parent
            ]);
        }

        // Assign to admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(array_merge([$parent->name], $permissions));
        }
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Remove the permissions and parent
        Permission::whereIn('name', [
            'Edit cr pending cap',
            'Show cr pending cap',

        ])->delete();
    }
};
