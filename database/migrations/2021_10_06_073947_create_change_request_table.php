<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_request', function (Blueprint $table) {
            $table->id();
            $table->integer('cr_no');
            $table->string('title');
            $table->longText('description');
            $table->enum('active', ['0', '1'])->default(1);
            $table->foreignId('developer_id')->nullable();
            $table->foreignId('tester_id')->nullable();
            $table->foreignId('designer_id')->nullable();
            $table->foreignId('requester_id')->nullable();

            $table->integer('design_duration')->nullable();
            $table->dateTime('start_design_time')->nullable();
            $table->dateTime('end_design_time')->nullable();

            $table->integer('develop_duration')->nullable();
            $table->dateTime('start_develop_time')->nullable();
            $table->dateTime('end_develop_time')->nullable();

            $table->integer('test_duration')->nullable();
            $table->dateTime('start_test_time')->nullable();
            $table->dateTime('end_test_time')->nullable();

            $table->foreignId('depend_cr_id')->nullable();

            $table->integer('helpdesk_id')->nullable();

            $table->string('requester_name')->nullable();
            $table->string('requester_email')->nullable();
            $table->string('requester_unit')->nullable();
            $table->string('requester_division_manager')->nullable();
            $table->string('requester_department')->nullable();
            $table->string('application_name')->nullable();

            $table->enum('testable', ['0', '1'])->default(1);

            $table->foreign('developer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('designer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('depend_cr_id')->references('id')->on('change_request')->onDelete('cascade');

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
        Schema::dropIfExists('change_request');
    }
}
