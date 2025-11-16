<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTechUnitIdToChangeRequestTable extends Migration
{
  
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_request', function (Blueprint $table) {
			$table->unsignedBigInteger('tech_unit_id')->nullable()->after('id');

			$table->foreign('tech_unit_id')
				->references('id')
				->on('units')
				->onDelete('set null');
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
			$table->dropForeign(['tech_unit_id']);
			$table->dropColumn('tech_unit_id');
		});
    }
}
