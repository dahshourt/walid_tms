<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDependWorkflowStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('depend_workflow_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('to_status_id');
            $table->foreignId('depend_status_id');
            $table->enum('active', ['0', '1'])->default(1);
            $table->foreign('depend_status_id')->references('id')->on('statuses')->onDelete('cascade');
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
        Schema::dropIfExists('depend_workflow_status');
    }
}
