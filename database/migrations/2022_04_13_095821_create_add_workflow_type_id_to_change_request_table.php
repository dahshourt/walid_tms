<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddWorkflowTypeIdToChangeRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_request', function (Blueprint $table) {
            //
            $table->foreignId('workflow_type_id')->nullable();
            $table->foreign('workflow_type_id')->references('id')->on('workflow_type')->onDelete('cascade');
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
            //
            $table->dropForeign('change_request_workflow_type_id_foreign');
            $table->dropColumn('type_id');
        });
    }
}
