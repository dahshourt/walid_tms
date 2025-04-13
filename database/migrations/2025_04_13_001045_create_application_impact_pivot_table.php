<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationImpactPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_impact', function (Blueprint $table) {
            $table->id(); // auto-increment primary key
            $table->unsignedBigInteger('impacts_id');
            $table->unsignedBigInteger('application_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('impacts_id')->references('id')->on('deployment_impacts')->onDelete('cascade');
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_impact');
    }
}
