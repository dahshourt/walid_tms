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
            // Drop old column
            $table->dropColumn('type');

            // Add new columns with enum (day/hour)
            $table->enum('sla_type_unit', ['day', 'hour'])->after('unit_sla_time');
            $table->enum('sla_type_division', ['day', 'hour'])->after('division_sla_time');
            $table->enum('sla_type_director', ['day', 'hour'])->after('director_sla_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('your_table_name', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['sla_unit', 'sla_division', 'sla_director']);

            // Restore old column
            $table->string('SLA_type')->nullable();
        });
    }
};
