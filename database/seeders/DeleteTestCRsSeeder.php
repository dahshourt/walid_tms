<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Change_request;

class DeleteTestCRsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Deletes test change requests by CR number.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting test CRs deletion process...');
        $this->command->info('');

        // List of test CR numbers to delete
        $testCrNumbers = [
            2046,
            6098,
            6099,
            6100,
            6101,
            6102,
            6103,
            6104,
            6105,
            6053,
            6097,
            6100,
            6036,
        ];

        // Remove duplicates
        $testCrNumbers = array_unique($testCrNumbers);

        $this->command->info("Found " . count($testCrNumbers) . " test CR number(s) to delete.");
        $this->command->info("CR Numbers: " . implode(', ', $testCrNumbers));
        $this->command->info('');

        // Start transaction
        DB::beginTransaction();

        try {
            // Get the change requests that match the CR numbers
            $changeRequests = Change_request::whereIn('cr_no', $testCrNumbers)->get();

            if ($changeRequests->isEmpty()) {
                $this->command->info('No change requests found with the specified CR numbers.');
                DB::rollback();
                return;
            }

            $crIds = $changeRequests->pluck('id')->toArray();
            $foundCrNumbers = $changeRequests->pluck('cr_no')->toArray();

            $this->command->info("Found " . count($crIds) . " change request(s) in database.");
            $this->command->info("CR Numbers found: " . implode(', ', $foundCrNumbers));
            $this->command->info('');

            $crDeleted = Change_request::whereIn('id', $crIds)->delete();
            $this->command->info("✓ Deleted {$crDeleted} change request(s) from change_request table.");

            // Commit transaction
            DB::commit();

            $this->command->info('');
            $this->command->info("═══════════════════════════════════════");
            $this->command->info("✓ Test CRs deletion completed successfully!");
            $this->command->info("  Total CRs deleted: {$crDeleted}");
            $this->command->info("═══════════════════════════════════════");

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollback();
            $this->command->error('');
            $this->command->error('✗ Error deleting test CRs: ' . $e->getMessage());
            $this->command->error('  All changes have been rolled back.');
        }
    }
}

