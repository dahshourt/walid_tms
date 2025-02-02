<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReleasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->string('release_name');
            $table->date('go_live_planned_date'); 
            $table->date('planned_start_iot_date')->nullable(); 
            $table->date('planned_end_iot_date')->nullable(); 
            $table->date('planned_start_e2e_date')->nullable(); 
            $table->date('planned_end_e2e_date')->nullable(); 
            $table->date('planned_start_uat_date')->nullable(); 
            $table->date('planned_end_uat_date')->nullable(); 
            $table->date('planned_start_smoke_test_date')->nullable(); 
            $table->date('planned_end_smoke_test_date')->nullable(); 
            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::dropIfExists('releases');*/
    }
}
