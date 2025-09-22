<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnicalCrTeamStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technical_cr_team_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
                $table->unsignedBigInteger('technical_cr_team_id');
                $table->unsignedBigInteger('old_status_id')->nullable();
                $table->unsignedBigInteger('new_status_id');
                $table->unsignedBigInteger('user_id');
                $table->text('note')->nullable();
                $table->timestamps();

                $table->index('technical_cr_team_id', 'tcts_team_idx');
                $table->index('new_status_id', 'tcts_new_status_idx');
                $table->index('user_id', 'tcts_user_idx');

                $table->foreign('technical_cr_team_id', 'tcts_team_fk')
                      ->references('id')->on('technical_cr_teams')
                      ->cascadeOnDelete();

                $table->foreign('new_status_id', 'tcts_new_status_fk')
                      ->references('id')->on('statuses')
                      ->cascadeOnDelete();

                $table->foreign('old_status_id', 'tcts_old_status_fk')
                      ->references('id')->on('statuses')
                      ->nullOnDelete();

                $table->foreign('user_id', 'tcts_user_fk')
                      ->references('id')->on('users')
                      ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('technical_cr_team_statuses');
    }
}
