<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlaDifToChangeRequestStatusesTable extends Migration
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
            $table->integer('sla_dif')->default(0)->comment('Integer value in days'); // 1- SET BY | 2- VIEW BY
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
            $table->dropColumn('sla_dif');
        });
    }
}
