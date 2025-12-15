<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectKpiMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_kpi_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_kpi_quarter_id')->constrained('project_kpi_quarters')->cascadeOnDelete();
            $table->text('milestone');
            $table->enum('status', ['Not Started', 'In Progress', 'Delivered', 'On-Hold', 'Canceled'])->default('Not Started');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_kpi_milestones');
    }
}
