<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_request', function (Blueprint $table) {
            $table->tinyInteger('need_ui_ux')->default(0)->after('parent_id');
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
            $table->dropColumn('need_ui_ux');
        });
    }
};
