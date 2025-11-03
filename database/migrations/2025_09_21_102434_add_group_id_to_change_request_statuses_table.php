<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupIdToChangeRequestStatusesTable extends Migration
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
            $table->unsignedBigInteger('group_id')->nullable()->after('user_id');
            $table->index('group_id', 'cr_statuses_group_id_idx');
            $table->foreign('group_id', 'cr_statuses_group_id_fk')
                ->references('id')->on('groups')
                ->nullOnDelete();
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
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }
}
