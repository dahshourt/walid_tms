<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnicalCrTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technical_cr_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->nullable();
            $table->foreignId('technical_cr_id')->nullable();
            $table->enum('status', ['0', '1', '2'])->default(0);
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('technical_cr_id')->references('id')->on('technical_crs')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('technical_cr_teams');
    }
}
