<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParallelWorkflowTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   // database/migrations/[timestamp]_create_parallel_workflow_tracking_table.php
public function up()
{
    Schema::create('parallel_workflow_tracking', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('cr_id');
        $table->unsignedBigInteger('split_status_id');
        $table->unsignedBigInteger('join_status_id');
        $table->string('workflow_instance_id', 36);
        $table->unsignedInteger('required_completions');
        $table->unsignedInteger('completed_workflows')->default(0);
        $table->boolean('is_completed')->default(false);
        $table->timestamps();

        $table->foreign('cr_id')->references('id')->on('change_request')->onDelete('cascade');
        $table->foreign('split_status_id')->references('id')->on('statuses')->onDelete('cascade');
        $table->foreign('join_status_id')->references('id')->on('statuses')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parallel_workflow_tracking');
    }
}
