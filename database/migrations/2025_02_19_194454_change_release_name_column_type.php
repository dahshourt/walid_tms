<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeReleaseNameColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_request', function (Blueprint $table) {
            // Change the column type to integer
            $table->unsignedBigInteger('release_name')->change();
    
            // Add foreign key constraint
            $table->foreign('release_name')->references('id')->on('releases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('change_request', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['release_name']);
    
            // Revert the column type back to string
            $table->string('release_name')->change();
        });
    }
}
