<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_workflow', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_status_id');
            $table->enum('active', ['0', '1'])->default(1);
            $table->enum('same_time', ['0', '1'])->default(0);
            $table->enum('workflow_type', ['0', '1'])->default(0); // 0 normal | 1 especial
            $table->string('to_status_label')->nullable();
            $table->foreign('from_status_id')->references('id')->on('statuses')->onDelete('cascade');
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
        Schema::dropIfExists('new_workflow');
    }
}
