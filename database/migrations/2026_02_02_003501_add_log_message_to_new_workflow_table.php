<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogMessageToNewWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_workflow', function (Blueprint $table) {
            $table->longText('log_message')->nullable()->after('to_status_label');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_workflow', function (Blueprint $table) {
            $table->dropColumn('log_message');
        });
    }
}
