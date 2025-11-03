<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateActiveEnumInChangeRequestStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add '3' as a new enum value for 'active' column
        DB::statement("ALTER TABLE change_request_statuses 
            MODIFY COLUMN active ENUM('0', '1', '2', '3') NOT NULL DEFAULT '0'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Rollback: remove '3' from enum values
        DB::statement("ALTER TABLE change_request_statuses 
            MODIFY COLUMN active ENUM('0', '1', '2') NOT NULL DEFAULT '0'");
    }
}
