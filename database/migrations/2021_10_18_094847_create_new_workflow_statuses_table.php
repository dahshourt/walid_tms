<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewWorkflowStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_workflow_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('new_workflow_id');
            $table->foreignId('to_status_id');
            $table->enum('default_to_status', ['0', '1'])->default(0);
            $table->foreign('new_workflow_id')->references('id')->on('new_workflow')->onDelete('cascade');
            $table->foreign('to_status_id')->references('id')->on('statuses')->onDelete('cascade');
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
        Schema::dropIfExists('new_workflow_statuses');
    }
}
