<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Workflow;
use App\Models\User;
use App\Models\Application;
use App\Models\Group;

class DataCleansingSeeder extends Seeder
{
    /**
     * Backup a table in the database
     *
     * @param string $table
     * @return void
     */
    private function backupTableInDatabase($table)
    {
        $date = date('Y_m_d'); // format without time
        $backupTable = "{$table}_{$date}";

        // Drop old table if exists (optional)
        DB::statement("DROP TABLE IF EXISTS `$backupTable`");

        // Create a copy structure + data
        DB::statement("CREATE TABLE `$backupTable` LIKE `$table`");
        DB::statement("INSERT INTO `$backupTable` SELECT * FROM `$table`");
    }

    /**
     * Delete inactive workflows
     *
     * @return int Number of deleted records
     */
    private function deleteInactiveWorkflows(): int
    {
        $this->command->info('Checking Workflows table...');
        $workflowCount = Workflow::where('active', '0')->count();

        if ($workflowCount > 0) {
            $workflowDeleted = Workflow::where('active', '0')->delete();
            $this->command->info("✓ Deleted {$workflowDeleted} inactive workflow(s) from workflow table.");
            return $workflowDeleted;
        }

        $this->command->info("  No inactive workflows found");
        return 0;
    }

    /**
     * Delete inactive users and their related records
     *
     * @return int Number of deleted users
     */
    private function deleteInactiveUsers(): int
    {
        $this->command->info('Checking Users table...');
        $userCount = User::where('active', '0')->count();

        if ($userCount > 0) {
            $userIds = User::where('active', '0')->pluck('id')->toArray();

            // Delete from model_has_roles table (Spatie permissions)
            $rolesDeleted = DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->whereIn('model_id', $userIds)
                ->delete();

            $this->command->info("✓ Deleted {$rolesDeleted} role assignment(s) from model_has_roles table.");

            // Delete from model_has_permissions table (Spatie permissions)
            $permissionsDeleted = DB::table('model_has_permissions')
                ->where('model_type', User::class)
                ->whereIn('model_id', $userIds)
                ->delete();

            $this->command->info("✓ Deleted {$permissionsDeleted} permission assignment(s) from model_has_permissions table.");

            // Delete from sessions table
            $sessionsDeleted = DB::table('sessions')
                ->whereIn('user_id', $userIds)
                ->delete();

            $this->command->info("✓ Deleted {$sessionsDeleted} session(s) from sessions table.");

            // Delete from change_request_custom_fields table
            $change_request_custom_fields = DB::table('change_request_custom_fields')
                ->whereIn('user_id', $userIds)
                ->delete();

            $this->command->info("✓ Deleted {$change_request_custom_fields} record(s) from change_request_custom_fields table.");

            // Delete users
            $userDeleted = User::where('active', '0')->delete();
            $this->command->info("✓ Deleted {$userDeleted} inactive user(s) from users table.");
            $this->command->info("  └─ CASCADE will automatically delete related records from other tables");

            return $userDeleted;
        }

        $this->command->info("  No inactive users found");
        return 0;
    }

    /**
     * Delete inactive applications
     *
     * @return int Number of deleted records
     */
    private function deleteInactiveApplications(): int
    {
        $this->command->info('Checking Applications table...');
        $applicationCount = Application::where('active', '0')->count();

        if ($applicationCount > 0) {
            $applicationIds = Application::where('active', '0')->pluck('id')->toArray();

            // Delete the applications
            $applicationDeleted = Application::whereIn('id', $applicationIds)->delete();

            $this->command->info("✓ Deleted {$applicationDeleted} inactive application(s) from applications table.");
            $this->command->info("  └─ CASCADE will automatically delete related records from other tables");

            return $applicationDeleted;
        }

        $this->command->info("  No inactive applications found");
        return 0;
    }

    /**
     * Delete inactive groups
     *
     * @return int Number of deleted records
     */
    private function deleteInactiveGroups(): int
    {
        $this->command->info('Checking Groups table...');
        $groupCount = Group::where('active', '0')->count();

        if ($groupCount > 0) {
            $groupIds = Group::where('active', '0')->pluck('id')->toArray();

            // Update change_request_statuses - set current_group_id to NULL (RESTRICT constraint)
            $currentGroupUpdated = DB::table('change_request_statuses')
                ->whereIn('current_group_id', $groupIds)
                ->update(['current_group_id' => null]);

            $this->command->info("✓ Updated {$currentGroupUpdated} record(s) in change_request_statuses (current_group_id set to NULL).");

            // Update change_request_statuses - set reference_group_id to NULL (RESTRICT constraint)
            $referenceGroupUpdated = DB::table('change_request_statuses')
                ->whereIn('reference_group_id', $groupIds)
                ->update(['reference_group_id' => null]);

            $this->command->info("✓ Updated {$referenceGroupUpdated} record(s) in change_request_statuses (reference_group_id set to NULL).");

            // Update change_request_statuses - set previous_group_id to NULL (RESTRICT constraint)
            $previousGroupUpdated = DB::table('change_request_statuses')
                ->whereIn('previous_group_id', $groupIds)
                ->update(['previous_group_id' => null]);

            $this->command->info("✓ Updated {$previousGroupUpdated} record(s) in change_request_statuses (previous_group_id set to NULL).");

            // Delete from custom_fields_groups_type
            $customFieldsDeleted = DB::table('custom_fields_groups_type')
                ->whereIn('group_id', $groupIds)
                ->delete();

            $this->command->info("✓ Deleted {$customFieldsDeleted} record(s) from custom_fields_groups_type table.");

            // Delete the groups
            $groupDeleted = Group::whereIn('id', $groupIds)->delete();

            $this->command->info("✓ Deleted {$groupDeleted} inactive group(s) from groups table.");
            $this->command->info("  └─ CASCADE will automatically delete related records from other tables");

            return $groupDeleted;
        }

        $this->command->info("  No inactive groups found");
        return 0;
    }

    /**
     * Run the database seeds.
     * Deletes all inactive records (active = 0) from multiple tables.
     *
     * @return void
     * @throws \Throwable
     */
    public function run()
    {
        $this->command->info('Starting data cleansing process...');
        $this->command->info('');

        // Backup tables before deletion
        $this->backupTableInDatabase('workflow');
        $this->backupTableInDatabase('users');
        $this->backupTableInDatabase('applications');
        $this->backupTableInDatabase('groups');

        // Start transaction
        DB::beginTransaction();

        try {
            $totalDeleted = 0;

            // Delete inactive records from each table
            $totalDeleted += $this->deleteInactiveWorkflows();
            $totalDeleted += $this->deleteInactiveUsers();
            $totalDeleted += $this->deleteInactiveApplications();
            $totalDeleted += $this->deleteInactiveGroups();

            // Commit transaction
            DB::commit();

            $this->command->info('');
            $this->command->info("═══════════════════════════════════════");
            $this->command->info("✓ Data cleansing completed successfully!");
            $this->command->info("  Total records deleted: {$totalDeleted}");
            $this->command->info("═══════════════════════════════════════");

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollback();
            $this->command->error('');
            $this->command->error('✗ Error during data cleansing: ' . $e->getMessage());
            $this->command->error('  All changes have been rolled back.');
        }
    }
}
