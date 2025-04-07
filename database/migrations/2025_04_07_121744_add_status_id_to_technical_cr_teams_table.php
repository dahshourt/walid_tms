<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusIdToTechnicalCrTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('technical_cr_teams', function (Blueprint $table) {
            //
            $table->foreignId('current_status_id')->nullable();
            $table->foreign('current_status_id')->references('id')->on('statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('technical_cr_teams', function (Blueprint $table) {
            //
            $table->dropColumn('current_status_id');
        });
    }
}
