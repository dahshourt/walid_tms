<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogMessageToStatusesAndCustomFieldsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('statuses', function (Blueprint $table) {
            $table->text('log_message')->nullable()->after('view_technical_team_flag');
        });

        Schema::table('custom_fields', function (Blueprint $table) {
            $table->text('log_message')->nullable()->after('related_table');
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
            $table->dropColumn('log_message');
        });

        Schema::table('custom_fields', function (Blueprint $table) {
            $table->dropColumn('log_message');
        });
    }
}

