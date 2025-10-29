<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowSpecialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow_special', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('no_need_desgin')->default(0); // 1- no need desgin
            $table->tinyInteger('not_testable')->default(0); // 1- not testable
            $table->foreignId('workflow_type_id')->nullable();
            $table->foreignId('from_status_id')->nullable();
            $table->foreignId('to_workflow_id')->nullable();
            $table->foreign('workflow_type_id')->references('id')->on('workflow_type')->onDelete('cascade');
            $table->foreign('from_status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('to_workflow_id')->references('id')->on('new_workflow')->onDelete('cascade');
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
        Schema::dropIfExists('workflow_special');
    }
}
