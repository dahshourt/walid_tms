<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds is_dependency_hold flag to change_request table.
     * This flag is set to 1 when a CR attempts to transition to Design Estimation
     * but has unresolved dependencies.
     * 
     * Note: The list of blocking CRs is NOT stored here - it's queried from
     * the cr_dependencies table to avoid data duplication.
     */
    public function up(): void
    {
        Schema::table('change_request', function (Blueprint $table) {
            $table->boolean('is_dependency_hold')->default(false)->after('hold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('change_request', function (Blueprint $table) {
            $table->dropColumn('is_dependency_hold');
        });
    }
};
