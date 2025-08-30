<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('cr_id');

            // Flags for escalation mails
            $table->boolean('unit_sent')->default(false);
            $table->boolean('division_sent')->default(false);
            $table->boolean('director_sent')->default(false);

            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // Foreign key to change_requests table (adjust if your CR table has a different name)
            //$table->foreign('cr_id')->references('cr_no')->on('change_request');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalation_logs');
    }
};
