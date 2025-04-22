<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeRequestTechnicalTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_request_technical_team', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cr_id');
            $table->unsignedBigInteger('technical_team_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('cr_id')->references('id')->on('change_request')->onDelete('cascade');
            $table->foreign('technical_team_id')->references('id')->on('technical_teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('change_request_technical_team');
    }
}
