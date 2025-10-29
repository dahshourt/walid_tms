<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameSlaTimeColumnInSlaCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sla_calculations', function (Blueprint $table) {
            $table->renameColumn('sla_time', 'unit_sla_time');
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
            $table->renameColumn('unit_sla_time', 'sla_time');
        });
    }
}
