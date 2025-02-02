<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManPowerToChangeRequest extends Migration
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
            $table->integer('man_days')->nullable();
            $table->integer('release')->nullable();
            $table->integer('associated')->nullable();
            $table->integer('depend_on')->nullable();
            $table->longText('analysis_feedback')->nullable();
            $table->longText('technical_feedback')->nullable();
            $table->string('approval')->nullable();
            $table->integer('need_design')->nullable();
            $table->longText('impacted_services')->nullable();
            $table->longText('impact_during_deployment')->nullable();
            $table->dateTime('release_delivery_date')->nullable();
            $table->string('release_name')->nullable();
            $table->dateTime('release_receiving_date')->nullable();
            $table->integer('need_iot_e2e_testing')->nullable();
            $table->dateTime('te_testing_date')->nullable();
            $table->dateTime('uat_date')->nullable();
            $table->integer('cost')->nullable();
            $table->string('uat_duration')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('creator_mobile_number')->nullable();
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
            Schema::table('change_request', function (Blueprint $table) {
                $table->dropColumn(['man_days', 'technical_feedback']);
            });
        });
    }
}
