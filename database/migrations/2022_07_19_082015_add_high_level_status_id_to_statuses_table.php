<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHighLevelStatusIdToStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('statuses', function (Blueprint $table) {
            //
            $table->foreignId('high_level_status_id')->nullable();
            $table->foreign('high_level_status_id')->references('id')->on('high_level_statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('statuses', function (Blueprint $table) {
            //
            $table->dropForeign('statuses_high_level_status_id_foreign');
            $table->dropColumn('high_level_status_id');
        });
    }
}
