<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        // Create parent permission
        $parentName = 'hold cr';
        $parent = Permission::firstOrCreate(['name' => $parentName]);

        // Child permissions
        $permissions = [
            'show hold cr',
            'edit hold cr',
            'make hold cr',
        ];

        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);

            // If your permissions table has a 'parent_id' column
            if (Schema::hasColumn($perm->getTable(), 'parent_id')) {
                $perm->update(['parent_id' => $parent->id]);
            }
        }
    }

    public function down(): void
    {
        $permissions = [
            'show hold cr',
            'edit hold cr',
            'make hold cr',
        ];

        // Delete child permissions
        Permission::whereIn('name', $permissions)->delete();

        // Delete parent permission
        Permission::where('name', 'hold cr')->delete();
    }
};
