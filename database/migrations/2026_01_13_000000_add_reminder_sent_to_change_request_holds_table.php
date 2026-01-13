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
        Schema::table('change_request_holds', function (Blueprint $table) {
            $table->boolean('reminder_sent')->default(false)->after('justification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('change_request_holds', function (Blueprint $table) {
            $table->dropColumn('reminder_sent');
        });
    }
};
