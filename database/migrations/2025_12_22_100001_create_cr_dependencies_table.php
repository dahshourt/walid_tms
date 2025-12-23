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
        Schema::create('cr_dependencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cr_id')->comment('The CR that depends on others');
            $table->unsignedBigInteger('depends_on_cr_id')->comment('The CR being depended upon');
            $table->enum('status', ['0', '1'])->default('0')->comment('0=active, 1=resolved');
            $table->timestamps();

            $table->foreign('cr_id')
                ->references('id')
                ->on('change_request')
                ->onDelete('cascade');

            $table->foreign('depends_on_cr_id')
                ->references('id')
                ->on('change_request')
                ->onDelete('cascade');

            // To prevent duplicate dependency entries
            $table->unique(['cr_id', 'depends_on_cr_id'], 'unique_cr_dependency');
            
            // Index for efficient lookups when a CR is delivered
            $table->index(['depends_on_cr_id', 'status'], 'idx_depends_on_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cr_dependencies');
    }
};
