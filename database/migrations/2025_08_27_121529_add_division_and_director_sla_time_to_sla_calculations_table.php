<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDivisionAndDirectorSlaTimeToSlaCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sla_calculations', function (Blueprint $table) {
        $table->integer('division_sla_time')->after('unit_sla_time');
        $table->integer('director_sla_time')->after('division_sla_time');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sla_calculations', function (Blueprint $table) {
        $table->dropColumn(['division_sla_time', 'director_sla_time']);
    });
    }
}
