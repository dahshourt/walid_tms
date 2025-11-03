<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusWorkFlowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_work_flow', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->default(1); // normal work | 0- back work
            $table->foreignId('from_status_id');
            $table->foreignId('to_status_id');
            $table->foreignId('from_stage_id')->nullable();
            $table->foreignId('to_stage_id')->nullable();

            $table->foreign('from_status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('to_status_id')->references('id')->on('statuses')->onDelete('cascade');

            $table->foreign('from_stage_id')->references('id')->on('stages')->onDelete('cascade');
            $table->foreign('to_stage_id')->references('id')->on('stages')->onDelete('cascade');
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
        Schema::dropIfExists('status_work_flow');
    }
}
