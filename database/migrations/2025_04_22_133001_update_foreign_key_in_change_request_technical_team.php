<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeyInChangeRequestTechnicalTeam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_request_technical_team', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['technical_team_id']);

            // Add new foreign key constraint to groups table
            $table->foreign('technical_team_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('change_request_technical_team', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['technical_team_id']);

            // Restore the original foreign key to technical_teams table
            $table->foreign('technical_team_id')->references('id')->on('technical_teams')->onDelete('cascade');
        });
    }
}
