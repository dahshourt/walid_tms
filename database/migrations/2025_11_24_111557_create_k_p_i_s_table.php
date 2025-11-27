<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKPISTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('priority', ['Critical', 'High', 'Medium', 'Low']);
            $table->string('strategic_pillar');
            $table->string('initiative');
            $table->string('sub_initiative')->nullable();
            $table->string('bu');
            $table->string('sub_bu')->nullable();
            $table->enum('target_launch_quarter', ['Q1', 'Q2', 'Q3', 'Q4']);
            $table->year('target_launch_year');
            $table->enum('type', ['Test Type 1', 'Test Type 2', 'Test Type 3', 'Test Type 4']);
            $table->text('kpi_brief');
            $table->enum('classification', ['CR', 'PM']);
            $table->enum('status', ['Open', 'In Progress', 'Delivered'])->default('Open');
            $table->foreignId('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('kpis');
    }
}
