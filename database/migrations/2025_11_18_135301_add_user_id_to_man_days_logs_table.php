<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToManDaysLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('man_days_logs', function (Blueprint $table) {
            // Add the column after group_id
            $table->unsignedBigInteger('user_id')->after('group_id')->nullable();

            // Add the foreign key constraint
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); // optional: delete logs if user deleted
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            Schema::table('man_days_logs', function (Blueprint $table) {
            // Drop FK first (important)
            $table->dropForeign(['user_id']);

            // Drop the column
            $table->dropColumn('user_id');
         });
    }
}
