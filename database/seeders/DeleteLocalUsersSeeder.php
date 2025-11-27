<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DeleteLocalUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all local users (user_type = 0)
        $localUserIds = User::where('user_type', '0')->pluck('id')->toArray();

        $userCount = count($localUserIds);

        if ($userCount === 0) {
            $this->command->info('No local users found to delete.');
            return;
        }

        $this->command->info("Found {$userCount} local user(s) to delete.");

        // Start transaction
        DB::beginTransaction();

        try {
            // Delete from model_has_roles table (Spatie permissions)
            $rolesDeleted = DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->whereIn('model_id', $localUserIds)
                ->delete();

            $this->command->info("✓ Deleted {$rolesDeleted} role assignment(s) from model_has_roles table.");

            // Delete from model_has_permissions table (Spatie permissions)
            $permissionsDeleted = DB::table('model_has_permissions')
                ->where('model_type', User::class)
                ->whereIn('model_id', $localUserIds)
                ->delete();

            $this->command->info("✓ Deleted {$permissionsDeleted} permission assignment(s) from model_has_permissions table.");

            // Delete from sessions table
            $sessionsDeleted = DB::table('sessions')
                ->whereIn('user_id', $localUserIds)
                ->delete();

            $this->command->info("✓ Deleted {$sessionsDeleted} sessions from sessions table.");

            // Delete from change_request_custom_fields table
            $change_request_custom_fields = DB::table('change_request_custom_fields')
                ->whereIn('user_id', $localUserIds)
                ->delete();

            $this->command->info("✓ Deleted {$change_request_custom_fields} change_request_custom_fields from change_request_custom_fields table.");


            // Delete local users from users table
            $usersDeleted = DB::table('users')->whereIn('id', $localUserIds)->delete();

            $this->command->info("✓ Deleted {$usersDeleted} local user(s) from users table.");

            // Commit transaction
            DB::commit();

            $this->command->info('');
            $this->command->info('✓ Successfully deleted all local users and their related records!');

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollback();
            $this->command->error('✗ Error deleting local users: ' . $e->getMessage());
        }
    }
}
