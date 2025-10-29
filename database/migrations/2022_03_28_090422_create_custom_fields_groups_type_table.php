<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomFieldsGroupsTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_fields_groups_type', function (Blueprint $table) {
            $table->id();
            $table->enum('form_type', ['1', '2', '3'])->default(1)->comment('1- create ticket , 2- update ticket , 3 search');
            $table->foreignId('group_id')->nullable();
            $table->foreignId('wf_type_id')->nullable();
            $table->foreignId('custom_field_id');
            $table->integer('sort')->nullable();
            $table->enum('active', ['0', '1'])->default(1);

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('wf_type_id')->references('id')->on('workflow_type')->onDelete('cascade');
            $table->foreign('custom_field_id')->references('id')->on('custom_fields')->onDelete('cascade');
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
        Schema::dropIfExists('custom_fields_groups_type');
    }
}
