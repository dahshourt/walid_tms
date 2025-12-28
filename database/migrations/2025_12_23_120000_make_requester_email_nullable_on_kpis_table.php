<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeRequesterEmailNullableOnKpisTable extends Migration
{
    public function up(): void
    {
        Schema::table('kpis', function (Blueprint $table) {
            // Ensure existing nulls won't break the change
            DB::table('kpis')->whereNull('requester_email')->update(['requester_email' => '']);
            $table->string('requester_email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kpis', function (Blueprint $table) {
            // Revert to not nullable; fill empty strings back to a placeholder before changing
            DB::table('kpis')->whereNull('requester_email')->update(['requester_email' => '']);
            $table->string('requester_email')->nullable(false)->change();
        });
    }
}
