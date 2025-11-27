<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpiChangeRequestTable extends Migration
{
    public function up()
    {
        Schema::create('kpi_change_request', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_id')->constrained('kpis')->onDelete('cascade');
            $table->foreignId('cr_id')->constrained('change_request')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['kpi_id', 'cr_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kpi_change_request');
    }
}
