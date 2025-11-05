<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGroupIdAndAddNotificationsToYourTableName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sla_calculations', function (Blueprint $table) {
            // 1. Drop old foreign key first
            $table->dropForeign(['group_id']);

            // 2. Rename column
            $table->renameColumn('group_id', 'unit_id');
        });

        Schema::table('sla_calculations', function (Blueprint $table) {
            // 3. Add new foreign key to "units" table
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            // 4. Add new notification columns
            $table->boolean('unit_notification')->default(false);
            $table->boolean('division_notification')->default(false);
            $table->boolean('director_notification')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('sla_calculations', function (Blueprint $table) {
            // Drop the new foreign key first
            $table->dropForeign(['unit_id']);

            // Drop notification columns
            $table->dropColumn(['unit_notification', 'division_notification', 'director_notification']);

            // Rename back to group_id
            $table->renameColumn('unit_id', 'group_id');
        });

        Schema::table('sla_calculations', function (Blueprint $table) {
            // Recreate the original foreign key to groups table
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }
}
