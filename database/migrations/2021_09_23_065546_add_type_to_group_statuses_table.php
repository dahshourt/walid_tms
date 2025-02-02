<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToGroupStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_statuses', function (Blueprint $table) {
            //
            $table->tinyInteger('type')->default(1); // 1- SET BY | 2- VIEW BY
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_statuses', function (Blueprint $table) {
            //
        });
    }
}
