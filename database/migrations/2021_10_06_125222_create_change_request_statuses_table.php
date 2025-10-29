<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeRequestStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_request_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cr_id');
            $table->foreignId('old_status_id');
            $table->foreignId('new_status_id');
            $table->foreignId('user_id');
            $table->enum('active', ['0', '1', '2'])->default(1);
            $table->foreign('cr_id')->references('id')->on('change_request')->onDelete('cascade');
            $table->foreign('old_status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('new_status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('change_request_statuses');
    }
}
