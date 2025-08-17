<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Add foreign key columns
            $table->unsignedBigInteger('director_id')->nullable()->after('technical_team');
            $table->unsignedBigInteger('division_manager_id')->nullable()->after('director_id');
            $table->unsignedBigInteger('unit_id')->nullable()->after('division_manager_id');

            // Add foreign key constraints
            $table->foreign('director_id')->references('id')->on('directors')->onDelete('set null');
            $table->foreign('division_manager_id')->references('id')->on('division_managers')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Drop constraints first
            $table->dropForeign(['director_id']);
            $table->dropForeign(['division_manager_id']);
            $table->dropForeign(['unit_id']);

            // Drop columns
            $table->dropColumn(['director_id', 'division_manager_id', 'unit_id']);
        });
    }
};
