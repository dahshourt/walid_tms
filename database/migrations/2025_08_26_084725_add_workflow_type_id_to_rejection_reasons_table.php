<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkflowTypeIdToRejectionReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rejection_reasons', function (Blueprint $table) {
            $table->tinyInteger('workflow_type_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rejection_reasons', function (Blueprint $table) {
            $table->dropColumn('workflow_type_id');
        });
    }
}
