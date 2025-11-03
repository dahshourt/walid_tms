<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceGroupIdToChangeRequestStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_request_statuses', function (Blueprint $table) {
            //
            $table->foreignId('reference_group_id')->nullable(); // just for technical teams
            $table->foreignId('previous_group_id')->nullable();
            $table->foreignId('current_group_id')->nullable();

            $table->foreign('reference_group_id')->references('id')->on('groups');
            $table->foreign('previous_group_id')->references('id')->on('groups');
            $table->foreign('current_group_id')->references('id')->on('groups');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('change_request_statuses', function (Blueprint $table) {
            //
            $table->dropForeign(['reference_group_id']);
            $table->dropColumn('reference_group_id');
            $table->dropForeign(['previous_group_id']);
            $table->dropColumn('previous_group_id');
            $table->dropForeign(['current_group_id']);
            $table->dropColumn('current_group_id');
        });
    }
}
