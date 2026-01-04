<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('change_request', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('change_request')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('change_request', function (Blueprint $table) {
            $table->dropForeign('change_request_parent_id_foreign');
        });
    }
};
