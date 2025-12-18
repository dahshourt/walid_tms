<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Delete existing KPIs
        DB::table('kpis')->delete();

        // Drop old columns
        Schema::table('kpis', function (Blueprint $table) {
            $table->dropColumn(['type', 'strategic_pillar', 'initiative', 'sub_initiative']);
        });

        // Add new foreign key columns with constraints
        Schema::table('kpis', function (Blueprint $table) {
            $table->foreignId('pillar_id')->after('priority')->constrained('kpi_pillars')->cascadeOnDelete();
            $table->foreignId('initiative_id')->after('pillar_id')->constrained('kpi_initiatives')->cascadeOnDelete();
            $table->foreignId('sub_initiative_id')->nullable()->after('initiative_id')->constrained('kpi_sub_initiatives')->cascadeOnDelete();
            $table->foreignId('type_id')->after('target_launch_year')->constrained('kpi_types')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpis', function (Blueprint $table) {
            // Drop foreign key columns
            $table->dropConstrainedForeignId('type_id');
            $table->dropConstrainedForeignId('pillar_id');
            $table->dropConstrainedForeignId('initiative_id');
            $table->dropConstrainedForeignId('sub_initiative_id');
            
            // Restore old columns
            $table->enum('type', ['Test Type 1', 'Test Type 2', 'Test Type 3', 'Test Type 4'])->after('target_launch_year');
            $table->string('strategic_pillar')->after('priority');
            $table->string('initiative')->after('strategic_pillar');
            $table->string('sub_initiative')->nullable()->after('initiative');
        });
    }
};
