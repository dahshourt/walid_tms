<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachementsCrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachements_crs', function (Blueprint $table) {
            $table->id();
            $table->text('file');
            $table->foreignId('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreignId('cr_id')
                ->references('id')->on('change_request')
                ->onDelete('cascade');
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
        Schema::dropIfExists('attachements_crs');
    }
}
