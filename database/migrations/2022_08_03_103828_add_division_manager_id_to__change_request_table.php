<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDivisionManagerIdToChangeRequestTable extends Migration
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

            $table->foreignId('division_manager_id')->nullable();
            $table->foreign('division_manager_id')->references('id')->on('division_managers')->onDelete('cascade');
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
            $table->dropForeign('change_request_division_manager_id_foreign');
            $table->dropColumn('division_manager_id');
        });
    }
}
