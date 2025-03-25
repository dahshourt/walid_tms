<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToChangeRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_request', function (Blueprint $table) {
            $table->integer('CR_duration')->nullable();
            $table->bigInteger('chnage_requester_id')->after('CR_duration')->nullable();
            $table->dateTime('start_CR_time')->after('chnage_requester_id')->nullable();
            $table->dateTime('end_CR_time')->after('start_CR_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('change_request', function (Blueprint $table) {
            $table->dropColumn(['CR_duration', 'chnage_requester_id', 'start_CR_time', 'end_CR_time']);
        });
    }
}
