<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sla_calculations', function (Blueprint $table) {
            $table->integer('unit_sla_time')->nullable()->change();
            $table->string('sla_type_unit', 10)->nullable()->change();
            $table->integer('division_sla_time')->nullable()->change();
            $table->string('sla_type_division', 10)->nullable()->change();
            $table->integer('director_sla_time')->nullable()->change();
            $table->string('sla_type_director', 10)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sla_calculations', function (Blueprint $table) {
            $table->integer('unit_sla_time')->nullable(false)->change();
            $table->string('sla_type_unit', 10)->nullable(false)->change();
            $table->integer('division_sla_time')->nullable(false)->change();
            $table->string('sla_type_division', 10)->nullable(false)->change();
            $table->integer('director_sla_time')->nullable(false)->change();
            $table->string('sla_type_director', 10)->nullable(false)->change();
        });
    }
};
