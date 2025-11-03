<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssignmentUserIdToChangeRequestStatusesTable extends Migration
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

            $table->foreignId('assignment_user_id')->nullable();
            $table->foreign('assignment_user_id')->references('id')->on('users')->onDelete('cascade');
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
            $table->dropForeign('change_request_statuses_assignment_user_id_foreign');
            $table->dropColumn('assignment_user_id');
        });
    }
}
