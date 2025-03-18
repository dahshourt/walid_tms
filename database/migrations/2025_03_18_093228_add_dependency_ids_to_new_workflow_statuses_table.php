<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDependencyIdsToNewWorkflowStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_workflow_statuses', function (Blueprint $table) {
            //
            $table->json('dependency_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_workflow_statuses', function (Blueprint $table) {
            //
            $table->dropColumn('dependency_ids');
        });
    }
}
