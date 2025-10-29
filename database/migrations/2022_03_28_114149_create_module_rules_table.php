<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModuleRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id');
            $table->string('rule_name');
            $table->string('rule_slug');
            $table->string('action_url');
            $table->integer('sort')->nullable();
            $table->enum('active', ['0', '1'])->default(1);

            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');

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
        Schema::dropIfExists('module_rules');
    }
}
