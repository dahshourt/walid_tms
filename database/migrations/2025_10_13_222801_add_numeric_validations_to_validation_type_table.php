<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumericValidationsToValidationTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('validation_type')->insert([
            ['name' => 'Numeric', 'active' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Numeric Required', 'active' => '1', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::table('validation_type')->whereIn('name', ['Numeric', 'Numeric Required'])->delete();

    }
}
