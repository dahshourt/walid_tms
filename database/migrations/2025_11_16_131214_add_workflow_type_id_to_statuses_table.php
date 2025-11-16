<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkflowTypeIdToStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('statuses', function (Blueprint $table) {
			$table->unsignedBigInteger('workflow_type_id')->nullable();

			$table->foreign('workflow_type_id')
				->references('id')
				->on('workflow_type')
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
        Schema::table('statuses', function (Blueprint $table) {
			$table->dropForeign(['workflow_type_id']);
			$table->dropColumn('workflow_type_id');
		});
    }
}
