<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrerequisitesAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prerequisites_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prerequisite_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->text('file');
            $table->foreign('prerequisite_id')->references('id')->on('prerequisites')->onDelete('cascade');
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
        Schema::dropIfExists('prerequisites_attachments');
    }
}
