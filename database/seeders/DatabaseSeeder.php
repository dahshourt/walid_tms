<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            GroupSeeder::class,
            UserGroupsSeeder::class,
            StageSeeder::class,
            StatusSeeder::class,
            GroupStatusSeeder::class,
            RoleSeeder::class,
            ModuleSeeder::class,
            Module_RulesSeeder::class,
            PermissionSeeder::class,
            CustomFieldsTableSeeder::class,
            CrCreationNotification::class,
            CrUpdateNotification::class,
            UpdateDivisionTemplateSeeder::class,

        ]);
        // \App\Models\User::factory(10)->create();
    }
}
