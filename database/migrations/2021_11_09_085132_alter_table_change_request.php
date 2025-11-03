<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableChangeRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('change_request', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable();
            $table->foreignId('priority_id')->nullable();

            $table->foreignId('unit_id')->nullable();
            $table->foreignId('department_id')->nullable();

            $table->foreignId('application_id')->nullable();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('priority_id')->references('id')->on('priorities')->onDelete('cascade');

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');

            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('change_request', function (Blueprint $table) {
            $table->dropColumn('category_id');
            $table->dropColumn('priority_id');
            $table->dropColumn('unit_id');
            $table->dropColumn('department_id');

            $table->dropColumn('application_id');
        });
    }
}
