<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParallelWorkflowBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // database/migrations/[timestamp]_create_parallel_workflow_branches_table.php
public function up()
{
    Schema::create('parallel_workflow_branches', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('tracking_id');
        $table->unsignedBigInteger('start_status_id');
        $table->unsignedBigInteger('end_status_id');
        $table->unsignedBigInteger('current_status_id')->nullable();
        $table->boolean('is_completed')->default(false);
        $table->timestamps();

        $table->foreign('tracking_id')->references('id')->on('parallel_workflow_tracking')->onDelete('cascade');
        $table->foreign('start_status_id')->references('id')->on('statuses')->onDelete('cascade');
        $table->foreign('end_status_id')->references('id')->on('statuses')->onDelete('cascade');
        $table->foreign('current_status_id')->references('id')->on('statuses')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parallel_workflow_branches');
    }
}
