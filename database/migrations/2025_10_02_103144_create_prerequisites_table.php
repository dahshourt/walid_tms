<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrerequisitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prerequisites', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->foreignId('promo_id')->nullable();
            $table->foreignId('group_id')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->string('requester_department')->nullable();
            $table->integer('requester_mobile')->nullable();
            // $table->enum('status',['open','pending','closed'])->default('open');
            $table->foreignId('status_id')->nullable();
            $table->foreign('promo_id')->references('id')->on('change_request')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('prerequisites');
    }
}
