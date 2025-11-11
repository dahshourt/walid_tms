<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUnitIdNullableInSlaCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
         Schema::table('sla_calculations', function (Blueprint $table) {
			 
            // First, drop the foreign key if it exists
            $table->dropForeign(['unit_id']);

            // Then, make the column nullable
            $table->unsignedBigInteger('unit_id')->nullable()->change();
        });
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sla_calculations', function (Blueprint $table) {
            // Reverse the nullable change
            $table->unsignedBigInteger('unit_id')->nullable(false)->change();

            // Re-add the foreign key constraint
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }
}
