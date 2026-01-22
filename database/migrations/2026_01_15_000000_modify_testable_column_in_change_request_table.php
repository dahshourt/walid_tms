<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyTestableColumnInChangeRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Using raw SQL to modify the enum column to be nullable without default value
        DB::statement("ALTER TABLE `change_request` MODIFY COLUMN `testable` ENUM('0', '1') NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to NOT NULL with default '1'
        DB::statement("ALTER TABLE `change_request` MODIFY COLUMN `testable` ENUM('0', '1') NOT NULL DEFAULT '1'");
    }
}
