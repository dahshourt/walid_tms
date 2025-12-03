<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('change_request_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('change_request_id')->constrained('change_request')->cascadeOnDelete();
            $table->foreignId('hold_reason_id')->constrained('hold_reasons')->cascadeOnDelete();
            $table->dateTime('resuming_date');
            $table->text('justification')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_request_holds');
    }
};
