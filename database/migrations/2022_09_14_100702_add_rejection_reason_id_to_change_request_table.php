<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectionReasonIdToChangeRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_request', function (Blueprint $table) {
            //
            $table->foreignId('rejection_reason_id')->nullable();
            $table->foreign('rejection_reason_id')->references('id')->on('rejection_reasons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('change_request', function (Blueprint $table) {
            //
            $table->dropForeign('change_request_rejection_reason_id_foreign');
            $table->dropColumn('rejection_reason_id');
        });
    }
}
