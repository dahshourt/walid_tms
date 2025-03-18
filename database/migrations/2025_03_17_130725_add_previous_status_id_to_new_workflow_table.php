<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreviousStatusIdToNewWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_workflow', function (Blueprint $table) {
            //
            $table->foreignId('previous_status_id')->nullable();
            $table->foreign('previous_status_id')->references('id')->on('statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_workflow', function (Blueprint $table) {
            //
            $table->dropColumn('previous_status_id');
        });
    }
}
