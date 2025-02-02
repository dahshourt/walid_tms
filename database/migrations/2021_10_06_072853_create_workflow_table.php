<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_status_id');
            $table->string('from_status_name');
            $table->foreignId('to_status_id');
            $table->string('to_status_name');
            $table->enum('default_to_status', ['0', '1'])->default(0);
            $table->enum('active', ['0', '1'])->default(1);
            $table->string('to_status_label')->nullable();

            $table->foreign('from_status_id')->references('id')->on('statuses')->onDelete('cascade');
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
        Schema::dropIfExists('workflow');
    }
}
