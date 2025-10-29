<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnicalCrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technical_crs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cr_id')->nullable();
            $table->enum('status', ['0', '1', '2'])->default(0);
            $table->timestamps();
            $table->foreign('cr_id')->references('id')->on('change_request')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('technical_crs');
    }
}
