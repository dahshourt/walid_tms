<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReleaseLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('release_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('release_id');
            $table->foreignId('user_id');
            $table->longText('log_text');
            $table->foreignId('status_id')->nullable(); // 1- SET BY | 2- VIEW BY
            $table->foreign('release_id')->references('id')->on('releases')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
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
        Schema::dropIfExists('release_logs');
    }
}
