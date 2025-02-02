<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActualDatesToReleasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('releases', function (Blueprint $table) {
            //
			$table->date('actual_start_iot_date')->nullable(); 
            $table->date('actual_end_iot_date')->nullable(); 
            $table->date('actual_start_e2e_date')->nullable(); 
            $table->date('actual_end_e2e_date')->nullable(); 
            $table->date('actual_start_uat_date')->nullable(); 
            $table->date('actual_end_uat_date')->nullable(); 
            $table->date('actual_start_smoke_test_date')->nullable(); 
            $table->date('actual_end_smoke_test_date')->nullable(); 
            $table->date('actual_closure_date')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('releases', function (Blueprint $table) {
            //
        });
    }
}
